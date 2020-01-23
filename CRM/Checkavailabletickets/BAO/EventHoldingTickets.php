<?php

class CRM_Checkavailabletickets_BAO_EventHoldingTickets extends CRM_Checkavailabletickets_DAO_EventHoldingTickets {

  /**
   * Check to see if when including the number of tickets that are in progress or being 'held'
   * for an event makes the event sell out
   * @param int $eventId
   * @param bool|int $sessionCount - A number of tickets being proposed to be held by the current session or FALSE
   *
   * @return bool
   */
  public static function isEventFull($eventId, $sessionCount = FALSE) {
    $eventDetails = civicrm_api3('Event', 'getsingle', ['id' => $eventId]);
    if (empty($eventDetails['max_participants'])) {
      return FALSE;
    }
    $eventFull = CRM_Event_BAO_Participant::eventFull($eventId, FALSE, FALSE);
    if (!$eventFull) {
      $emptySeats = CRM_Event_BAO_Participant::eventFull($eventId, TRUE, FALSE);
      $holdingTickets = civicrm_api3('EventHoldingTickets', 'getsingle', ['event_id' => $eventId]);
      $check = $emptySeats - $holdingTickets['number_holding_tickets'];
      if ($check < 0) {
        return TRUE;
      }
      if ($sessionCount) {
        $check = $check - $sessionCount;
        if ($check < 0) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Update the count of the number of tickets currently being processed / held in the database
   * @param int $count - Number of tickets to either add or remove from the count
   * @param string $operator are we adding or removing from the count
   * @param int $eventId - The event id for which we are updating
   * @param string|null $session_id - the Current session id
   */
  public static function updateHoldingTicketsCount($count, $operator, $eventId, $session_id = NULL) {
    CRM_Core_DAO::executeQuery("UPDATE civicrm_event_holding_tickets SET number_holding_tickets = IF((number_holding_tickets {$operator} %1) < 0, 0, number_holding_tickets {$operator} %1) WHERE event_id = %2", [
      1 => [$count, 'Integer'],
      2 => [$eventId, 'Positive'],
    ]);
    if ($session_id) {
      CRM_Core_DAO::executeQuery("UPDATE civicrm_event_holding_tickets_session SET number_holding_tickets = IF((number_holding_tickets {$operator} %1) < 0, 0, number_holding_tickets {$operator} %1) WHERE event_id = %2 AND session_id = %3", [
        1 => [$count, 'Integer'],
        2 => [$eventId, 'Positive'],
        3 => [$session_id, 'String'],
      ]);
    }
  }

 
  /**
   * Updates the current count of number of tickets in holding by removing any that were attached to now abandoned sessions
   */
  public static function updateTicketHoldingCount() {
    $getDeadSessionCount = CRM_Core_DAO::executeQuery("SELECT hts.event_id, sum(hts.number_holding_tickets) as holding_tickets
     FROM civicrm_event_holding_tickets_session hts
     LEFT JOIN civicrm_cache c ON c.path LIKE CONCAT('%', hts.session_id, '%')
     WHERE c.path IS NULL
     GROUP BY hts.event_id")->fetchAll();
    if (!empty($getDeadSessionCount)) {
      foreach ($getDeadSessionCount as $eventSessionCount) {
        self::updateHoldingTicketsCount($eventSessionCount['holding_tickets'], '-', $eventSessionCount['event_id']);
      }
    }
    CRM_Core_DAO::executeQuery("DELETE hts.* FROM civicrm_event_holding_tickets_session hts
     LEFT JOIN civicrm_cache c ON c.path LIKE CONCAT('%', hts.session_id, '%')
     WHERE c.path IS NULL");
  }

}

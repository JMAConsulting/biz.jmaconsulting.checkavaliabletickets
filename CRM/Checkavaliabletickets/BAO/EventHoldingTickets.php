<?php

class CRM_Checkavaliabletickets_BAO_EventHoldingTickets extends CRM_Checkavaliabletickets_DAO_EventHoldingTickets {

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

}

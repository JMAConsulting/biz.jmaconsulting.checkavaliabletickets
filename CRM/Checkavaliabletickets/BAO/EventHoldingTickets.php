<?php
use CRM_Checkavaliabletickets_ExtensionUtil as E;

class CRM_Checkavaliabletickets_BAO_EventHoldingTickets extends CRM_Checkavaliabletickets_DAO_EventHoldingTickets {


  public static function isEventFull($eventId, $sessionCount = FALSE) {
    $eventFull = CRM_Event_BAO_Participant::eventFull($eventId, FALSE, FALSE);
    if (!$eventFull) {
      $emptySeats =  CRM_Event_BAO_Participant::eventFull($eventId, TRUE, FALSE);
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

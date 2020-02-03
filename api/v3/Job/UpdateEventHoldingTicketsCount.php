<?php
use CRM_Checkavailabletickets_ExtensionUtil as E;

/**
 * Job.update_event_holding_ticket_count API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_job_update_event_holding_tickets_count($params) {
  CRM_Checkavailabletickets_BAO_EventHoldingTickets::updateTicketHoldingCount();
  return civicrm_api3_create_success([], $params, 'Job', 'UpdateEventHoldingTicketsCount');
}

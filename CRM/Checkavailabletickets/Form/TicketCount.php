<?php

use CRM_Checkavailabletickets_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Checkavailabletickets_Form_TicketCount extends CRM_Event_Form_ManageEvent {

  /**
   * Set variables up before form is built.
   */
  public function preProcess() {
    parent::preProcess();
    $this->setSelectedChild('ticketcount');
  }

  public function buildQuickForm() {

    $currentCount = civicrm_api3('EventHoldingTickets', 'get', ['event_id' => $this->_id]);
    $count = empty($currentCount['count']) ? 0 : $currentCount['values'][$currentCount['id']]['number_holding_tickets'];
    $current_count_field = $this->add('text', 'current_count', E::ts('Current Count of tickets in progress of being sold'));
    $this->setDefaults(['current_count' => $count]);
    $current_count_field->freeze();
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Reset Counter'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
  }

  public function postProcess() {
    $values = $this->exportValues();
    parent::postProcess();
    civicrm_api3('Job', 'update_event_holding_tickets_count', []);
    $sessionCount = CRM_Core_DAO::singleValueQuery("SELECT sum(number_holding_tickets)
      FROM civicrm_event_holding_tickets_session
      WHERE event_id = %1
      GROUP BY event_id", [1 => [$this->_id, 'Positive']]);
    $counter = civicrm_api3('EventHoldingTickets', 'get', ['event_id' => $this->_id]);
    civicrm_api3('EventHoldingTickets', 'create', ['id' => $counter['id'], 'number_holding_tickets' => $sessionCount]);
    
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}

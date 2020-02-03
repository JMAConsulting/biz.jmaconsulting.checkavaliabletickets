<?php

require_once 'checkavailabletickets.civix.php';

use CRM_Checkavailabletickets_ExtensionUtil as E; 

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function checkavailabletickets_civicrm_config(&$config) {
  _checkavailabletickets_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function checkavailabletickets_civicrm_xmlMenu(&$files) {
  _checkavailabletickets_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function checkavailabletickets_civicrm_install() {
  _checkavailabletickets_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function checkavailabletickets_civicrm_postInstall() {
  _checkavailabletickets_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function checkavailabletickets_civicrm_uninstall() {
  _checkavailabletickets_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function checkavailabletickets_civicrm_enable() {
  _checkavailabletickets_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function checkavailabletickets_civicrm_disable() {
  _checkavailabletickets_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function checkavailabletickets_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _checkavailabletickets_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function checkavailabletickets_civicrm_managed(&$entities) {
  _checkavailabletickets_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function checkavailabletickets_civicrm_caseTypes(&$caseTypes) {
  _checkavailabletickets_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function checkavailabletickets_civicrm_angularModules(&$angularModules) {
  _checkavailabletickets_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function checkavailabletickets_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _checkavailabletickets_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function checkavailabletickets_civicrm_entityTypes(&$entityTypes) {
  _checkavailabletickets_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function checkavailabletickets_civicrm_themes(&$themes) {
  _checkavailabletickets_civix_civicrm_themes($themes);
}

/**
 * Implements hook_civicrm_pageRun().
 */
function checkavailabletickets_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');
  if ($pageName === 'CRM_Event_Page_EventInfo') {
    $store = NULL;
    $holdingFull = CRM_Utils_Request::retrieve('holdingFull', 'Int', $store, FALSE, 0);
    if ($holdingFull) {
      $page->assign('allowRegistration', 0);
    }
  }
}

/**
 * Implements hook_civicrm_preProcess().
 */
function checkavailabletickets_civicrm_preProcess($formName, &$form) {
  if ($formName === 'CRM_Event_Form_Registration_Register'
    || $formName === 'CRM_Event_Form_Registration_Confirm'
    || $formName === 'CRM_Event_Form_Registration_ThankYou') {
    $initialCheck = civicrm_api3('EventHoldingTickets', 'get', ['event_id' => $form->_eventId]);
    if (!$initialCheck['count']) {
      civicrm_api3('EventHoldingTickets', 'create', ['event_id' => $form->_eventId, 'number_holding_tickets' => 0]);
    }
    $initialCheck = civicrm_api3('EventHoldingTicketsSession', 'get', ['event_id' => $form->_eventId, 'session_id' => $form->controller->_key]);
    if (!$initialCheck['count']) {
      civicrm_api3('EventHoldingTicketsSession', 'create', ['event_id' => $form->_eventId, 'number_holding_tickets' => 0, 'session_id' => $form->controller->_key]);
    }
    // This gets set in the Register PostProcess hook.
    $submittedParticipantCount = $form->get('partCount');
    // We have either returned to the start due to an error e.g. payment processor error or have made it all the way through to the end.
    if (($formName === 'CRM_Event_Form_Registration_Register' ||
      $formName === 'CRM_Event_Form_Registration_ThankYou') && !is_null($submittedParticipantCount)) {
      CRM_Checkavaliabletickets_BAO_EventHoldingTickets::updateHoldingTicketsCount($submittedParticipantCount, '-', $form->_eventId, $form->controller->_key);
    }
    $sessionCount = FALSE;
    if ($formName === 'CRM_Event_Form_Registration_Register') {
      $sessionCount = 1;
    }
    if ($formName === 'CRM_Event_Form_Registration_Register' || $formName === 'CRM_Event_Form_Registration_Confirm') {
      $fullCheck = CRM_Checkavailabletickets_BAO_EventHoldingTickets::isEventFull($form->_eventId, $sessionCount);
      if ($fullCheck) {
        $event = civicrm_api3('Event', 'getsingle', ['id' => $form->_eventId]);
        CRM_Core_Session::setStatus($event['event_full_text']);
        CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/event/info', "reset=1&id={$form->_eventId}&holdingFull=1", FALSE, NULL, FALSE, TRUE));
      }
    }
  }
}

/**
 * Implements hook_civicrm_validateForm().
 */
function checkavailabletickets_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  if ($formName === 'CRM_Event_Form_Registration_Confirm') {
    $fullCheck = CRM_Checkavailabletickets_BAO_EventHoldingTickets::isEventFull($form->_eventId);
    if ($fullCheck) {
      $event = civicrm_api3('Event', 'getsingle', ['id' => $form->_eventId]);
      CRM_Core_Session::setStatus($event['event_full_text']);
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/event/info', "reset=1&id={$form->_eventId}&holdingFull=1", FALSE, NULL, FALSE, TRUE));
    }
  }
}

/**
 * Implements hook_civicrm_postProcess().
 */
function checkavailabletickets_civicrm_postProcess($formName, &$form) {
  if ($formName === 'CRM_Event_Form_Registration_Register') {
    $submittedParams = $form->get('params')[0];
    $submittedParticipantCount = CRM_Event_Form_Registration::getParticipantCount($form, $submittedParams);
    $form->set('partCount', $submittedParticipantCount);
    $fullCheck = CRM_Checkavailabletickets_BAO_EventHoldingTickets::isEventFull($form->_eventId, $submittedParticipantCount);
    if ($fullCheck) {
      $event = civicrm_api3('Event', 'getsingle', ['id' => $form->_eventId]);
      CRM_Core_Session::setStatus($event['event_full_text']);
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/event/info', "reset=1&id={$form->_eventId}&holdingFull=1", FALSE, NULL, FALSE, TRUE));
    }
    CRM_Checkavailabletickets_BAO_EventHoldingTickets::updateHoldingTicketsCount($submittedParticipantCount, '+', $form->_eventId, $form->controller->_key);
  }
}

function checkavailabletickets_civicrm_tabSet($tabsetName, &$tabs, $context) {
  if ($tabsetName == 'civicrm/event/manage') {
    if (!empty($context)) {
      $eventID = $context['event_id'];
      $url = CRM_Utils_System::url( 'civicrm/event/manage/ticketcount',
        "reset=1&snippet=5&force=1&id=$eventID&action=update&component=event" );
      $tabs['ticketcount'] = array(
        'title' => E::ts('Current Holding Ticket Count'),
        'link' => $url,
        'valid' => 1,
        'active' => 1,
        'current' => false,
      );
    }
    else {
      $tabs['ticketcount'] = [
        'title' => E::ts('Current Holding Ticket Count'),
        'url' => 'civicrm/event/manage/ticketcount',
      ];
    }
  }
}

<?php

require_once 'checkavaliabletickets.civix.php';
use CRM_Checkavaliabletickets_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function checkavaliabletickets_civicrm_config(&$config) {
  _checkavaliabletickets_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function checkavaliabletickets_civicrm_xmlMenu(&$files) {
  _checkavaliabletickets_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function checkavaliabletickets_civicrm_install() {
  _checkavaliabletickets_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function checkavaliabletickets_civicrm_postInstall() {
  _checkavaliabletickets_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function checkavaliabletickets_civicrm_uninstall() {
  _checkavaliabletickets_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function checkavaliabletickets_civicrm_enable() {
  _checkavaliabletickets_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function checkavaliabletickets_civicrm_disable() {
  _checkavaliabletickets_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function checkavaliabletickets_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _checkavaliabletickets_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function checkavaliabletickets_civicrm_managed(&$entities) {
  _checkavaliabletickets_civix_civicrm_managed($entities);
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
function checkavaliabletickets_civicrm_caseTypes(&$caseTypes) {
  _checkavaliabletickets_civix_civicrm_caseTypes($caseTypes);
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
function checkavaliabletickets_civicrm_angularModules(&$angularModules) {
  _checkavaliabletickets_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function checkavaliabletickets_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _checkavaliabletickets_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function checkavaliabletickets_civicrm_entityTypes(&$entityTypes) {
  _checkavaliabletickets_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function checkavaliabletickets_civicrm_themes(&$themes) {
  _checkavaliabletickets_civix_civicrm_themes($themes);
}

function checkavaliabletickets_civicrm_preProcess($formName, &$form) {
  if ($formName === 'CRM_Event_Form_Registration_Register'
    || $formName === 'CRM_Event_Form_Registration_Confirm'
    || $formName === 'CRM_Event_Form_Registration_ThankYou') {
    $initialCheck = civicrm_api3('EventHoldingTickets', 'get', ['event_id' => $form->_eventId]);
    if (!$initialCheck['count']) {
      civicrm_api3('EventHoldingTickets', 'create', ['event_id' => $form->_eventId, 'number_holding_tickets' => 0]);
    }
    // This gets set in the Register PostProcess hook.
    $submittedParticipantCount = $form->get('partCount');
    // We have either returned to the start due to an error e.g. payment processor error or have made it all the way through to the end.
    if (($formName === 'CRM_Event_Form_Registration_Register' ||
      $formName === 'CRM_Event_Form_Registration_ThankYou') && !is_null($submittedParticipantCount)) {
      $lock = Civi::lockManager()->acquire('worker.avaliabletickets.' . $form->_eventId);
      if ($lock->isAcquired()) {
        $currentCount = civicrm_api3('EventHoldingTickets', 'get', ['event_id' => $form->_eventId]);
        $updated_count = $currentCount['values'][$currentCount['id']]['number_holding_tickets'] - $submittedParticipantCount;
        civicrm_api3('EventHoldingTickets', 'create', ['id' => $currentCount['id'], 'number_holding_tickets' => $updated_count]);
        $lock->release();
      }
    }
    $sessionCount = FALSE;
    if ($formName === 'CRM_Event_Form_Registration_Register') {
      $sessionCount = 1;
    }
    $fullCheck = CRM_Checkavaliabletickets_BAO_EventHoldingTickets::isEventFull($form->_eventId, $sessionCount);
    if ($fullCheck) {
      $event = civicrm_api3('Event', 'getsingle', ['id' => $form->_eventId]);
      CRM_Core_Session::setStatus($event['event_full_text']);
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/event/info', "reset=1&id={$form->_eventId}", FALSE, NULL, FALSE, TRUE));
    }
  }
}

/**
 * Implements hook_civicrm_validateForm().
 *
 * @param string $formName
 * @param array $fields
 * @param array $files
 * @param CRM_Core_Form $form
 * @param array $errors
 */
function checkavaliabletickets_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  if ($formName === 'CRM_Event_Form_Registration_Confirm') {
    $fullCheck = CRM_Checkavaliabletickets_BAO_EventHoldingTickets::isEventFull($form->_eventId);
    if ($fullCheck) {
      $event = civicrm_api3('Event', 'getsingle', ['id' => $form->_eventId]);
      CRM_Core_Session::setStatus($event['event_full_text']);
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/event/info', "reset=1&id={$form->_eventId}", FALSE, NULL, FALSE, TRUE));
    }
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 */
function checkavaliabletickets_civicrm_postProcess($formName, &$form) {
  if ($formName === 'CRM_Event_Form_Registration_Register') {
    $submittedParams = $form->get('params')[0];
    $submittedParticipantCount = CRM_Event_Form_Registration::getParticipantCount($form, $submittedParams);
    $form->set('partCount', $submittedParticipantCount);
    $fullCheck = CRM_Checkavaliabletickets_BAO_EventHoldingTickets::isEventFull($form->_eventId, $submittedParticipantCount);
    if ($fullCheck) {
      $event = civicrm_api3('Event', 'getsingle', ['id' => $form->_eventId]);
      CRM_Core_Session::setStatus($event['event_full_text']);
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/event/info', "reset=1&id={$form->_eventId}", FALSE, NULL, FALSE, TRUE));
    }
    $lock = Civi::lockManager()->acquire('worker.avaliabletickets.' . $form->_eventId);
    if ($lock->isAcquired()) {
      $currentCount = civicrm_api3('EventHoldingTickets', 'get', ['event_id' => $form->_eventId]);
      $updated_count = $currentCount['values'][$currentCount['id']]['number_holding_tickets'] + $submittedParticipantCount;
      civicrm_api3('EventHoldingTickets', 'create', ['id' => $currentCount['id'], 'number_holding_tickets' => $updated_count]);
      $lock->release();
    }
  }
  if ($formName === 'CRM_Event_Form_Registration_Confirm') {
  }
}

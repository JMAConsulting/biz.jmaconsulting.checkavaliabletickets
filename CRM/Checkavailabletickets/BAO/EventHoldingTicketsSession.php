<?php
use CRM_Checkavaliabletickets_ExtensionUtil as E;

class CRM_Checkavaliabletickets_BAO_EventHoldingTicketsSession extends CRM_Checkavaliabletickets_DAO_EventHoldingTicketsSession {

  /**
   * Create a new EventHoldingTicketsSession based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Checkavaliabletickets_DAO_EventHoldingTicketsSession|NULL
   *
  public static function create($params) {
    $className = 'CRM_Checkavaliabletickets_DAO_EventHoldingTicketsSession';
    $entityName = 'EventHoldingTicketsSession';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}

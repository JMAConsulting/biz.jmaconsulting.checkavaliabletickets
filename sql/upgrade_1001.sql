-- /*******************************************************
-- *
-- * civicrm_event_holding_tickets_session
-- *
-- * FIXME
-- *
-- *******************************************************/
CREATE TABLE IF NOT EXISTS `civicrm_event_holding_tickets_session` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique EventHoldingTicketsSession ID',
     `event_id` int unsigned    COMMENT 'FK to Event',
     `number_holding_tickets` int    COMMENT 'Column to hold the number of tickets currently being held in this session',
     `session_id` varchar(255) NOT NULL   COMMENT 'CiviCRM QuickForm Session ID' 
,
        PRIMARY KEY (`id`)

    ,     UNIQUE INDEX `UI_event_id_session_id`(
        event_id
      , session_id
  )

,          CONSTRAINT FK_civicrm_event_holding_tickets_session_event_id FOREIGN KEY (`event_id`) REFERENCES `civicrm_event`(`id`) ON DELETE CASCADE  
)    ;

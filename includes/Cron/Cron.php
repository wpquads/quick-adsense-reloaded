<?php

/**
 * Chron relevant stuff
 */

// No Direct Access
if( !defined( "WPINC" ) ) {
   die;
}

class quadsCron {

   public function __construct() {
      add_filter( 'cron_schedules', array($this, 'add_new_intervals'), 100 );

   }

   /**
    * Add new intervals for wp cron jobs
    * @param type $schedules
    * @return type
    */
   public function add_new_intervals( $schedules ) {
      // add weekly and monthly intervals
      $schedules['weekly'] = array(
          'interval' => 604800,
          'display' => __( 'Once Weekly','quick-adsense-reloaded' )
      );

      $schedules['monthly'] = array(
          'interval' => 2635200,
          'display' => __( 'Once a month','quick-adsense-reloaded' )
      );

      return $schedules;
   }
   
   
   

   public function schedule_event() {

      if( !wp_next_scheduled( 'quads_weekly_event' ) ) {
         wp_schedule_event( time(), 'weekly', 'quads_weekly_event' );

      }
      if( !wp_next_scheduled( 'quads_daily_event' ) ) {
         wp_schedule_event( time(), 'daily', 'quads_daily_event' );

      }
    }
}
$quadsCron = new quadsCron(); 
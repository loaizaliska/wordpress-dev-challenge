<?php
require_once( LISKA_LINK_CHECKER_PLUGIN_DIR . '/includes/functions/CheckLinks.php' );

class CronJobHook
{
  public $cronJob;
  public function __construct()
  {
    add_action('wp', array($this, 'schedule_revalidation'));
    add_action('link_revalidation_cronjob', array($this, 'schedule_revalidation'));
    if (!wp_next_scheduled('link_revalidation_cronjob')) {
      wp_schedule_event(time(), '4days', 'link_revalidation_cronjob');
    }
  }

  public function schedule_revalidation() {
    $cronJob = new CheckLinks();
    $cronJob->check_error_links();

  }
}

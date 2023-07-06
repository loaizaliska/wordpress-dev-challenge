<?php

class ActivationPlugin {

  public function __construct() {
   
  }

  public static function activate() {
    self::create_error_links_table();
  }

  public static function create_error_links_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'error_links';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
   
      $charset_collate = $wpdb->get_charset_collate();
      $sql = "CREATE TABLE $table (
        id INT NOT NULL AUTO_INCREMENT,
        url VARCHAR(255) NOT NULL,
        status_code VARCHAR(255) NOT NULL,
        post_id INT NOT NULL,
        detection_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        flagged BOOLEAN DEFAULT FALSE,
        PRIMARY KEY (id),
        KEY post_id (post_id)
    ) $charset_collate;";
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta($sql);
    }
  }

}
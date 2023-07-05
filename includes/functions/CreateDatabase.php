<?php

class CreateDatabase {

  public function __construct() {
   
  }

  public static function create_table_wrong_links() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'wrong_links';

    if ($wpdb->get_var("SHOW TABLES LIKE '$tabla'") != $tabla) {
   
      $charset_collate = $wpdb->get_charset_collate();
      $sql = "CREATE TABLE $tabla (
          id INT NOT NULL AUTO_INCREMENT,
          url VARCHAR(255) NOT NULL,
          state VARCHAR(100) NOT NULL,
          origin INT NOT NULL,
          date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          KEY origin (origin)
      ) $charset_collate;";
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta($sql);
    }
  }
}
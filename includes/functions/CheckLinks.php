<?php

require_once(__DIR__ .'/ErrorsVerification.php');

class CheckLinks
{
  public $checkError;


  public function __construct()
  {

    add_action('admin_menu', array($this, 'display_verification_results'));
    $this->checkError = new ErrorsVerification();
  }

  // Function to check error links
  public function check_error_links() {
    echo ("check_error_links");
    global $wpdb;

    // Get all posts
    $posts = get_posts(array(
      'post_type' => 'post',
      'posts_per_page' => -1,
    ));
    foreach ($posts as $post) {
      $existing_meta = get_post_meta($post->ID, 'link_verification_done', true);
      // Check if the link has already been reviewed before
      if ($existing_meta) {
          // Check if enough time has passed to perform a revalidation
          $last_verification_time = get_post_meta($post->ID, 'last_verification_time', true);
          $current_time = time();
          $time_difference = $current_time - $last_verification_time;
          $revalidation_interval = 4 * 24 * 60 * 60; // 4 days in seconds
          if ($time_difference < $revalidation_interval) {
              // No need to re-verify links in this post
              continue;
          }
      }

      // Get post content
      $content = $post->post_content;

      // Check for error links in the post content

      $this->checkError->check_error_links_in_content($post->ID, $content);

      if (empty($existing_meta)) {
        add_post_meta($post->ID, 'link_verification_done', true);
        add_post_meta($post->ID, 'last_verification_time', time());
      } else {
        // Mark the post as verified
        update_post_meta($post->ID, 'link_verification_done', true);
        update_post_meta($post->ID, 'last_verification_time', time());
      }

    }
  }

  // Helper function to get the failed links from the custom table
  function get_error_links() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'error_links';

    // Get the links with errors from the custom table
    $query = "SELECT * FROM $table_name";
    $results = $wpdb->get_results($query, ARRAY_A);

    return $results;
  }

  // Function to process the verification results and display them in the admin panel
  function process_verification_results() {
    // Get the links with errors from the database or from wherever you have stored them
    $error_links = $this->get_error_links(); 

    // Verifica si hay enlaces con errores
    if (!empty($error_links)) {
        // Create a table to display the results in the admin panel
        echo '<h2>Error Links</h2>';
        echo '<table class="wp-list-table widefat striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>URL</th>';
        echo '<th>Error</th>';
        echo '<th>Origen</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($error_links as $error_link) {
            // Get link details with error
            $url = $error_link['url'];
            $error = $error_link['status_code'];
            $origin = $error_link['post_id'];

            // Show the details in the table
            echo '<tr>';
            echo '<td>' . esc_html($url) . '</td>';
            echo '<td>' . esc_html($error) . '</td>';
            echo '<td>' . esc_html($origin) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        // Show a message if there are no links with errors
        echo '<p>No error links found.</p>';
    }
  }

  // Function to display verification results in admin panel
  function display_verification_results() {
      add_submenu_page(
          'edit.php',
          'Link Verification Results',
          'Link Verification',
          'manage_options',
          'link-verification-results',
          array($this, 'process_verification_results')
      );
  }

}

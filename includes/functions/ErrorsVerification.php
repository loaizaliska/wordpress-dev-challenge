<?php

class ErrorsVerification
{
  public function __construct()
  {
  }

  // Function to check if a link is insecure
  public function check_error_links_in_content($postID, $content) {
    $error_links = array();
    $matches = array();
    // Variable to store the result of the verification
    $pattern = '/<a\s+(?:[^>]*?\s+)?href=(["\'])(.*?)\1/';

    // Get all links in the content
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

    // For each link, check if it is insecure
    foreach ($matches as $match) {
        $link_url = $match[2];

        // Check if the link is insecure
        $errors = [];

        if ($error = $this->is_insecure_link($link_url)) {
          $errors[] = $error;
        }

        if($error = $this->is_protocol_not_specified($link_url)) {
          $errors[] = $error;
        }

        if($error = $this->is_malformed_link($link_url)) {
          $errors[] = $error;
        }

        if($error = $this->has_invalid_status_code($link_url)) {
          $errors[] = $error;
        }

        if(!empty($errors)) {
          $error_links[] = array(
            'url' => $link_url,
            'errors' => $errors,
            'postID' => $postID,
          );
          $this->save_error_links_to_database($error_links);
        }
    }
  }
  
  // Verificar si el enlace es inseguro (comienza con "http://")
  public function is_insecure_link($link_url) {
      if (strpos($link_url, 'http://') === 0) {
        return 'Enlace inseguro';
    }

    return false;
  }
  
  // Verificar si el enlace no especifica un protocolo (comienza con "//" o no contiene "://")
  public function is_protocol_not_specified($link_url) {
    if (strpos($link_url, 'http://') !== 0 && strpos($link_url, 'https://') !== 0) {
      return 'protocol_not_specified';
    }

    return false;
  }
  
  // Verificar si el enlace está mal formado (comienza con "https://" seguido de una URL inválida)
  public function is_malformed_link($link_url) {
    if (!filter_var($link_url, FILTER_VALIDATE_URL)) {
      return 'malformed';
    }

    return false;
  }

  // Verificar si el enlace devuelve un código de estado inválido (40X, 50X)
  public function has_invalid_status_code($link_url) {
    $headers = get_headers($link_url);
    if ($headers && preg_match('/HTTP\/\d+\.\d+\s+(\d+)/', $headers[0], $matches)) {
        $status_code = intval($matches[1]);
        if ($status_code >= 400 && $status_code <= 599) {
          return 'invalid_status_code: ' . $status_code;
        }
    }
    return false;
  }
  
  // Función para guardar los enlaces con errores en la base de datos
  function save_error_links_to_database($errors) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'error_links'; // Nombre de la tabla personalizada

    // Itera a través de los enlaces con errores y los guarda en la base de datos
    foreach ($errors as $error) {
        $url = $error['url'];
        $status = implode(', ', $error['errors']);;
        $postID = $error['postID'];

        $data = array(
            'url' => $url,
            'status_code' => $status,
            'post_id' => $postID,
            'detection_date' => current_time('mysql'),
            'flagged' => true,
        );

        $wpdb->insert($table_name, $data);
    }
  }  
}

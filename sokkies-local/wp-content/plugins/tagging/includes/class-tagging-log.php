<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Tagging_Log
{
  private static $instance;
  private $log_file;

  public function __construct()
  {
    $this->log_file = plugin_dir_path(__FILE__) . 'tagging.log';
    // This is only for development
    // register_shutdown_function([$this, 'handleFatalError']);

    if (!file_exists($this->log_file)) {
      $this->create_log_file();
    }
  }

  public function handleFatalError() {
    $debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';
    if ($debug) {
      $error = error_get_last();
      if ($error) {
        if (strpos($error['file'], 'plugins/tagging/includes/class-tagging') !== false) {
          if (strpos($error['message'], 'Stack trace:') !== false) {
            $message = explode('Stack trace:', $error['message'])[0];
          } else {
            $message = $error['message'];
          }
          $this->log("Fatal error: " . $message);
        }
        else {
          $this->log("No error details available". print_r($error, true));
        }
      }
    }
  }

  private function create_log_file()
  {
    file_put_contents($this->log_file, '');
  }

  final public static function instance()
  {
    if (is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function log($message)
  {
    file_put_contents($this->log_file, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
  }

  public function get_file_path()
  {
    return $this->log_file;
  }

  public function clear_log()
  {
    file_put_contents($this->log_file, '');
  }

  public function download_log()
  {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="tagging.log"');
    readfile($this->log_file);
  }
}

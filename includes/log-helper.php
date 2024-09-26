<?php
function lm_log_api_error($api_name, $error_message) {
    $log_file = plugin_dir_path(__FILE__) . 'api_errors.log';
    $current_time = date('Y-m-d H:i:s');
    $log_message = "[$current_time] $api_name failed: $error_message" . PHP_EOL;
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}
?>

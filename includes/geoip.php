<?php
// Main function to get user's location using multiple GeoIP services
function get_user_location() {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Check cache first
    $cached_location = lm_get_cached_location($ip);
    if ($cached_location) {
        return $cached_location;
    }

    // Try IPinfo.io first
    $location = get_location_from_ipinfo();
    
    if (!$location) {
        $location = get_location_from_ipstack();
    }
    
    if (!$location) {
        $location = get_location_from_ipbase();
    }
    
    if (!$location) {
        $location = get_location_from_geoplugin();
    }

    // Cache the result if location is found
    if ($location) {
        lm_cache_location($ip, $location);
    }

    return $location;  // Return false if all services fail
}

// Function to get location from IPinfo.io
function get_location_from_ipinfo() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $access_key = get_option('lm_ipinfo_api_key');  // Get API key from admin settings

    // Call API
    $response = wp_remote_get("https://ipinfo.io/{$ip}/json?token={$access_key}");

    if (is_wp_error($response)) {
        lm_log_api_error('IPinfo.io', wp_remote_retrieve_response_message($response)); // Log error
        return false;
    }

    $location_data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($location_data['city']) && isset($location_data['region'])) {
        $location = array(
            'city' => $location_data['city'],
            'region' => $location_data['region']
        );
        
        return $location;
    }

    lm_log_api_error('IPinfo.io', 'City or region not found in the response');
    return false;
}

// Function to get location from IPstack
function get_location_from_ipstack() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $access_key = get_option('lm_ipstack_api_key');  // Get API key from admin settings

    // Call API
    $response = wp_remote_get("http://api.ipstack.com/{$ip}?access_key={$access_key}");

    if (is_wp_error($response)) {
        lm_log_api_error('IPstack', wp_remote_retrieve_response_message($response)); // Log error
        return false;
    }

    $location_data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($location_data['city']) && isset($location_data['region_name'])) {
        return array(
            'city' => $location_data['city'],
            'region' => $location_data['region_name']
        );
    }

    lm_log_api_error('IPstack', 'City or region not found in the response');
    return false;
}

// Function to get location from IPbase
function get_location_from_ipbase() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $access_key = get_option('lm_ipbase_api_key');  // Get API key from admin settings

    // Call API
    $response = wp_remote_get("https://api.ipbase.com/v2/info?ip={$ip}&apikey={$access_key}");

    if (is_wp_error($response)) {
        lm_log_api_error('IPbase', wp_remote_retrieve_response_message($response)); // Log error
        return false;
    }

    $location_data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($location_data['data']['location']['city']['name']) && isset($location_data['data']['location']['region']['name'])) {
        return array(
            'city' => $location_data['data']['location']['city']['name'],
            'region' => $location_data['data']['location']['region']['name']
        );
    }

    lm_log_api_error('IPbase', 'City or region not found in the response');
    return false;
}

// Function to get location from GeoPlugin (unlimited requests)
function get_location_from_geoplugin() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = wp_remote_get("http://www.geoplugin.net/json.gp?ip={$ip}");

    if (is_wp_error($response)) {
        lm_log_api_error('GeoPlugin', wp_remote_retrieve_response_message($response)); // Log error
        return false;
    }

    $location_data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($location_data['geoplugin_city']) && isset($location_data['geoplugin_region'])) {
        return array(
            'city' => $location_data['geoplugin_city'],
            'region' => $location_data['geoplugin_region']
        );
    }

    lm_log_api_error('GeoPlugin', 'City or region not found in the response');
    return false;
}
?>

<?php

function get_user_location() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $cached_location = lm_get_cached_location($ip);
    if ($cached_location) {
        return $cached_location;
    }

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

    if ($location) {
        lm_cache_location($ip, $location);
    }

    return $location; 
}

function get_location_from_ipinfo() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $access_key = get_option('lm_ipinfo_api_key'); 

    $response = wp_remote_get("https://ipinfo.io/{$ip}/json?token={$access_key}");

    if (is_wp_error($response)) {
        lm_log_api_error('IPinfo.io', wp_remote_retrieve_response_message($response)); 
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

function get_location_from_ipstack() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $access_key = get_option('lm_ipstack_api_key'); 

    $response = wp_remote_get("http://api.ipstack.com/{$ip}?access_key={$access_key}");

    if (is_wp_error($response)) {
        lm_log_api_error('IPstack', wp_remote_retrieve_response_message($response)); 
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

function get_location_from_ipbase() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $access_key = get_option('lm_ipbase_api_key');  

    $response = wp_remote_get("https://api.ipbase.com/v2/info?ip={$ip}&apikey={$access_key}");

    if (is_wp_error($response)) {
        lm_log_api_error('IPbase', wp_remote_retrieve_response_message($response));
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

function get_location_from_geoplugin() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = wp_remote_get("http://www.geoplugin.net/json.gp?ip={$ip}");

    if (is_wp_error($response)) {
        lm_log_api_error('GeoPlugin', wp_remote_retrieve_response_message($response)); 
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

function lm_set_location_in_localstorage_with_validation() {
    
    $location = get_user_location();

    if ($location && !empty($location['city']) && !empty($location['region'])) {
        $city = $location['city'];
        $state = $location['region'];

        
        if (!lm_compare_location_with_services($city, $state)) {
            return; 
        }

        ?>
        <script type="text/javascript">

            localStorage.setItem('selected_city', '<?php echo esc_js($city); ?>');
            localStorage.setItem('selected_state', '<?php echo esc_js($state); ?>');
        </script>
        <?php
    }
}
add_action('wp_footer', 'lm_set_location_in_localstorage_with_validation');


function lm_compare_location_with_services($geoip_city, $geoip_state) {
    $locations = lm_get_locations();


    $geoip_city_clean = strtolower(trim($geoip_city));
    $geoip_state_clean = strtolower(trim($geoip_state));

    if (isset($locations[$geoip_state_clean])) {
        $available_cities = explode(',', $locations[$geoip_state_clean]);
        
        
        $available_cities_cleaned = array_map(function($city) {
            return strtolower(trim($city));
        }, $available_cities);

        $result = in_array($geoip_city_clean, $available_cities_cleaned);
        
        return $result;
    }
    return false;
}



?>

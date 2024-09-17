<?php
// Function to cache the location for a specific IP
function lm_cache_location($ip, $location) {
    set_transient('lm_location_' . $ip, $location, 12 * HOUR_IN_SECONDS);  // Cache for 12 hours
}

// Function to get cached location for a specific IP
function lm_get_cached_location($ip) {
    return get_transient('lm_location_' . $ip);
}
?>

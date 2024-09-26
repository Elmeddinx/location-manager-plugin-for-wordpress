<?php
function lm_cache_location($ip, $location) {
    set_transient('lm_location_' . $ip, $location, 12 * HOUR_IN_SECONDS); 
}

function lm_get_cached_location($ip) {
    return get_transient('lm_location_' . $ip);
}
?>

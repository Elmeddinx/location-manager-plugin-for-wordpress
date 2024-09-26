<?php
function lm_register_api_settings() {
    add_options_page(
        'API Settings',
        'API Settings',
        'manage_options',
        'lm-api-settings',
        'lm_display_api_settings'
    );
}
add_action('admin_menu', 'lm_register_api_settings');

function lm_display_api_settings() {
    ?>
    <div class="wrap">
        <h1>API Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('lm_api_settings_group');
            do_settings_sections('lm-api-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function lm_initialize_api_settings() {
    register_setting('lm_api_settings_group', 'lm_ipinfo_api_key');
    register_setting('lm_api_settings_group', 'lm_ipstack_api_key');
    register_setting('lm_api_settings_group', 'lm_ipbase_api_key');

    add_settings_section('lm_api_section', 'API Keys', 'lm_api_section_callback', 'lm-api-settings');

    add_settings_field('lm_ipinfo_api_key_field', 'IPinfo.io API Key', 'lm_ipinfo_api_key_callback', 'lm-api-settings', 'lm_api_section');
    add_settings_field('lm_ipstack_api_key_field', 'IPstack API Key', 'lm_ipstack_api_key_callback', 'lm-api-settings', 'lm_api_section');
    add_settings_field('lm_ipbase_api_key_field', 'IPbase API Key', 'lm_ipbase_api_key_callback', 'lm-api-settings', 'lm_api_section');
}

add_action('admin_init', 'lm_initialize_api_settings');

function lm_api_section_callback() {
    echo '<p>Enter your API keys here for the different GeoIP services.</p>';
}

function lm_ipinfo_api_key_callback() {
    $api_key = get_option('lm_ipinfo_api_key');
    echo '<input type="text" name="lm_ipinfo_api_key" value="' . esc_attr($api_key) . '" />';
}

function lm_ipstack_api_key_callback() {
    $api_key = get_option('lm_ipstack_api_key');
    echo '<input type="text" name="lm_ipstack_api_key" value="' . esc_attr($api_key) . '" />';
}

function lm_ipbase_api_key_callback() {
    $api_key = get_option('lm_ipbase_api_key');
    echo '<input type="text" name="lm_ipbase_api_key" value="' . esc_attr($api_key) . '" />';
}
?>

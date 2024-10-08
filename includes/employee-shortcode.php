<?php
function lm_employee_swiper_shortcode($atts) {
    global $wpdb;
    $employee_options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'lm_employee_%'");

    $employees = array();
    foreach ($employee_options as $option) {
        $employee_data = maybe_unserialize($option->option_value);
        if (is_array($employee_data)) {
            if (!empty($employee_data['image_id'])) {
                $employee_data['image_url'] = wp_get_attachment_url($employee_data['image_id']);
            } else {
                $employee_data['image_url'] = '';
            }
            $employees[] = $employee_data;
        }
    }

    
        wp_enqueue_script('lm-employee-swiper-js', plugin_dir_url(__FILE__) . '../assets/js/employee-swiper.js', array('jquery'), '1.1.1', true);
        wp_localize_script('lm-employee-swiper-js', 'lmEmployeesData', array(
            'employees' => $employees,
        ));
    
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), null, true);

    ob_start();
    ?>
    <div class="employee-swiper-container">
        <div class="employee-swiper-inner">
            <h2 class="employee-swiper-title" id="employee-swiper-title">The <span id="employee-swiper-title-span">Lumen Blinds</span> team you can trust</h2>
            <div class="swiper lm-employee-swiper">
                <div class="swiper-wrapper">
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('lm_employee_swiper', 'lm_employee_swiper_shortcode');
?>

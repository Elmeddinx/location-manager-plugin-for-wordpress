<?php
function lm_employee_swiper_shortcode($atts) {
    // Tüm çalışanları al
    global $wpdb;
    $employee_options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'lm_employee_%'");

    $employees = array();
    foreach ($employee_options as $option) {
        $employee_data = maybe_unserialize($option->option_value);
        if (is_array($employee_data)) {
            // Resim URL'sini ekleyelim
            if (!empty($employee_data['image_id'])) {
                $employee_data['image_url'] = wp_get_attachment_url($employee_data['image_id']);
            } else {
                $employee_data['image_url'] = '';
            }
            $employees[] = $employee_data;
        }
    }

    // Çalışan verilerini JavaScript'e aktaralım
    wp_enqueue_script('lm-employee-swiper-js', plugin_dir_url(__FILE__) . '../assets/js/employee-swiper.js', array('jquery'), '1.0', true);
    wp_localize_script('lm-employee-swiper-js', 'lmEmployeesData', array(
        'employees' => $employees,
    ));

    // Swiper.js ve CSS dosyalarını enqueue edelim
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), null, true);

    // HTML çıktısını oluşturalım
    ob_start();
    ?>
    <!-- Swiper.js container -->
    <div class="swiper lm-employee-swiper">
        <div class="swiper-wrapper">
            <!-- JavaScript ile slide'lar eklenecek -->
        </div>
        <!-- Swiper navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('lm_employee_swiper', 'lm_employee_swiper_shortcode');
?>

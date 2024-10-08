<?php
function lm_register_service_type_cpt() {
    $labels = array(
        'name'                  => _x( 'Service Types', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Service Type', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Service Types', 'text_domain' ),
        'name_admin_bar'        => __( 'Service Type', 'text_domain' ),
    );
    
    $args = array(
        'label'                 => __( 'Service Type', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title' ),
        'public'                => false,
        'show_ui'               => true, 
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-list-view',
        'capability_type'       => 'post',
    );
    
    register_post_type( 'lm_service_type', $args );
}

add_action( 'init', 'lm_register_service_type_cpt', 0 );

function lm_display_service_meta_box($post) {
    $current_service = get_post_meta($post->ID, '_lm_service_type', true);
    
    $services = get_posts(array(
        'post_type' => 'lm_service_type',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    ?>
    <label for="lm_service_type">Select Service Type:</label>
    <select name="lm_service_type" id="lm_service_type">
        <option value="">--Select Service--</option>
        <?php if ($services) : ?>
            <?php foreach ($services as $service) : ?>
                <option value="<?php echo esc_attr($service->post_name); ?>" <?php selected($current_service, $service->post_name); ?>>
                    <?php echo esc_html($service->post_title); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <?php
}

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
?>

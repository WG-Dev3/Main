<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style','hello-elementor-header-footer' ) );

        wp_enqueue_style( 'google-font', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap', array(), wp_get_theme()->get( 'Version' ), 'all' );

        wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/assets/js/custom.js', array(), wp_get_theme()->get( 'Version' ), true );
        wp_localize_script( 'custom-script', 'wg_ajax_object',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'wg_ajax_nonce' )
            )
        );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION

function create_profile_page_for_new_user($user_id) {
    // Get user data
    $user_info = get_userdata($user_id);

    // Check if the user has the Subscriber role
    if (!in_array('subscriber', (array) $user_info->roles)) {
        return; // Exit if the user is not a Subscriber
    }

    $user_name = $user_info->user_login;
    $user_email = $user_info->user_email;

    // Check if the user already has a profile page
    $existing_page = get_posts([
        'post_type'   => 'page',
        'meta_key'    => 'profile_user_id',
        'meta_value'  => $user_id,
        'numberposts' => 1,
    ]);

    if (!empty($existing_page)) {
        return; // User already has a profile page
    }

    // Create the profile page
    $profile_page_id = wp_insert_post([
        'post_title'   => sprintf('%s\'s Profile', $user_name),
        'post_content' => sprintf('Welcome, %s! This is your profile page.', $user_name),
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_author'  => $user_id, // Assign the user as the page author
    ]);

    // Add user ID as meta to the page
    if ($profile_page_id) {
        add_post_meta($profile_page_id, 'profile_user_id', $user_id, true);

        // Assign the custom template to the page
        update_post_meta($profile_page_id, '_wp_page_template', 'page-user-profile.php');

        // Store the profile page ID in user meta (for redirect)
        update_user_meta($user_id, 'profile_page_id', $profile_page_id);

        // Redirect to the newly created profile page
        wp_redirect(get_permalink($profile_page_id)); // Redirect to the profile page
        exit; // Always call exit after redirecting to ensure no further code is executed
    }
}
add_action('user_register', 'create_profile_page_for_new_user');

function redirect_to_profile_after_login($redirect_to, $request, $user) {
    // Get the user ID
    $user_id = $user->ID;

    // Get the user's profile page ID (if you have a custom profile page for each user)
    $profile_page_id = get_user_meta($user_id, 'profile_page_id', true); // Assuming you've saved the user's profile page ID

    // If the profile page ID exists, redirect to that page
    if ($profile_page_id) {
        return get_permalink($profile_page_id);
    }

    // Default behavior: redirect to the dashboard or home page
    return $redirect_to;
}
add_filter('login_redirect', 'redirect_to_profile_after_login', 10, 3);

function set_default_user_heading_value($value, $post_id, $field) {
    // Ensure we're on a specific user profile page (check if the page has the correct meta)
    if ('page' === get_post_type($post_id)) {
        // Check if this page is a profile page by checking if it has a 'profile_user_id' custom field
        $profile_user_id = get_post_meta($post_id, 'profile_user_id', true);

        // Check if the current user is the profile owner (or an administrator)
        if ($profile_user_id == get_current_user_id() || current_user_can('administrator')) {
            // Get the page title
            $page_title = get_the_title($post_id);

            // Set the page title as the default value for the 'user_heading' field if the field is empty
            if (empty($value)) {
                return $page_title; // Return the page title as the default value if no value is set
            }
        }
    }

    return $value;
}

add_filter('acf/load_value/name=user_heading', 'set_default_user_heading_value', 10, 3);


function handle_ajax_request() {

    $response = array(
        'status' => false,
    );

    if ( ! isset($_POST['security']) || ! wp_verify_nonce( $_POST['security'], 'wg_ajax_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Nonce verification failed' ) );
        wp_die();
    }

    $search_value = isset( $_POST['search_data'] ) ? $_POST['search_data'] : '';

    $args = array(
        'post_type'      => 'team',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'order'          => 'DESC',
    );

    if ( ! empty( $search_value ) ) {
        $args['s'] = $search_value;
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        $posts_data = array();

        while ($query->have_posts()) {
            $query->the_post();

            $post_id = get_the_ID();
            $param = array('post_id' => $post_id);
            get_template_part( 'template-part/post', 'data', $param );
        }
        wp_reset_postdata();
    }

    $html = ob_get_clean();

    if (empty($html)) {
        $res_data = array(
            'status' => false,
        );
    } else {
        $res_data = array(
            'status' => true,
            'html'   => $html,
        );
    }

    wp_send_json_success(  $res_data );
}

add_action( 'wp_ajax_wg_ajax_action', 'handle_ajax_request' );
add_action( 'wp_ajax_nopriv_wg_ajax_action', 'handle_ajax_request' );




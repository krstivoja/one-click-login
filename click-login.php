<?php

/**
 * Plugin Name: One Click Admin Login
 * Description: Displays a login button in the footer for non-logged-in users to log in as admin with one click.
 * Version: 1.0
 * Author: Marko Krstic
 */

// Hook to add the button in the footer
add_action('wp_footer', 'display_admin_login_button_in_footer');

function display_admin_login_button_in_footer()
{
    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Do not display the button if already logged in
        return;
    }

    // Create the login button and add it to the footer
    $login_url = wp_nonce_url(admin_url('admin-ajax.php?action=one_click_admin_login'), 'one_click_login_nonce');
    echo '<div style="position:fixed; bottom:2rem; right:2rem; z-index:9999;">';
    echo '<a href="' . esc_url($login_url) . '" class="button" style="background:red; color:white; border-radius:1rem; padding: 1rem; text-decoration:none;">Admin Login</a>';
    echo '</div>';
}

// Handle the admin login on button click
add_action('wp_ajax_one_click_admin_login', 'handle_one_click_admin_login');
add_action('wp_ajax_nopriv_one_click_admin_login', 'handle_one_click_admin_login');

function handle_one_click_admin_login()
{
    // Verify the nonce for security
    if (!wp_verify_nonce($_GET['_wpnonce'], 'one_click_login_nonce')) {
        wp_die('Security check failed.');
    }

    // If 'dev' user not found, try the user with ID 1 (super admin)
    if (!$admin_user) {
        $admin_user = get_user_by('ID', 1);  // Super admin user ID
    }

    // Log in the user if found
    if ($admin_user) {
        wp_set_auth_cookie($admin_user->ID);
        wp_redirect(admin_url());
        exit;
    } else {
        wp_die('Admin user not found.');
    }
}

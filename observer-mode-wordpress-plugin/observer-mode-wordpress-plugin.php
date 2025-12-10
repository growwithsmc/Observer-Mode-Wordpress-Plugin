<?php
/**
 * Plugin Name: Observer Mode
 * Description: Adds an Observer Admin role with full backend visibility but no write capabilities, plus GitHub auto updates and an Observer Mode dashboard.
 * Version: 1.0.0
 * Author: Grow With SMC
 * Text Domain: observer-mode
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * Constants
 */
define('OBSERVER_MODE_VERSION', '1.0.0');
define('OBSERVER_MODE_PLUGIN_FILE', __FILE__);
define('OBSERVER_MODE_GITHUB_REPO', 'growwithsmc/Observer-Mode-Wordpress-Plugin');
define('OBSERVER_MODE_GITHUB_API', 'https://api.github.com/repos/' . OBSERVER_MODE_GITHUB_REPO);

/**
 * Helper: check if current user is Observer Admin
 */
function observer_mode_is_observer() {
    $user = wp_get_current_user();
    if (!$user || empty($user->roles)) {
        return false;
    }
    return in_array('observer_admin', (array) $user->roles, true);
}

/**
 * Create Observer Admin role on activation
 */
register_activation_hook(__FILE__, 'observer_mode_create_role');
function observer_mode_create_role() {

    // Reset if already exists
    remove_role('observer_admin');

    // Capabilities: enough to load editors and admin, no write power
    $caps = array(
        'read'                  => true,
        'read_private_posts'    => true,
        'read_private_pages'    => true,
        'list_users'            => true,

        // Allow editor screens to open, we block the save layer instead
        'edit_posts'            => true,
        'edit_pages'            => true,
        'edit_others_posts'     => true,
        'edit_others_pages'     => true,

        // No creation, publishing, or deleting
        'publish_posts'         => false,
        'publish_pages'         => false,
        'delete_posts'          => false,
        'delete_pages'          => false,
        'delete_others_posts'   => false,
        'delete_others_pages'   => false,
        'delete_published_posts'=> false,
        'delete_published_pages'=> false,

        // No plugin, theme, or settings control
        'manage_options'        => false,
        'install_plugins'       => false,
        'activate_plugins'      => false,
        'update_plugins'        => false,
        'delete_plugins'        => false,
        'edit_plugins'          => false,
        'edit_theme_options'    => false,
        'switch_themes'         => false,
        'customize'             => false,
        'manage_categories'     => false,
        'moderate_comments'     => false,
        'upload_files'          => false,

        // Elementor: can view screens, not edit
        'manage_elementor'      => true,
        'edit_elementor'        => false,
    );

    add_role(
        'observer_admin',
        'Observer Admin',
        $caps
    );
}

/**
 * Admin menu adjustments for observer role
 * Remove Add New and Customize entries
 */
add_action('admin_menu', 'observer_mode_adjust_admin_menu', 999);
function observer_mode_adjust_admin_menu() {
    if (!observer_mode_is_observer()) {
        return;
    }

    global $submenu;

    // Remove Add New from Posts
    if (isset($submenu['edit.php'])) {
        foreach ($submenu['edit.php'] as $index => $item) {
            if (isset($item[2]) && $item[2] === 'post-new.php') {
                unset($submenu['edit.php'][$index]);
            }
        }
    }

    // Remove Add New from Pages
    if (isset($submenu['edit.php?post_type=page'])) {
        foreach ($submenu['edit.php?post_type=page'] as $index => $item) {
            if (isset($item[2]) && strpos($item[2], 'post-new.php?post_type=page') !== false) {
                unset($submenu['edit.php?post_type=page'][$index]);
            }
        }
    }

    // Remove Customize link
    remove_submenu_page('themes.php', 'customize.php');
}

/**
 * Remove + New from admin bar for observer role
 */
add_action('admin_bar_menu', 'observer_mode_admin_bar_cleanup', 999);
function observer_mode_admin_bar_cleanup($wp_admin_bar) {
    if (!observer_mode_is_observer()) {
        return;
    }
    $wp_admin_bar->remove_node('new-content');
}

/**
 * Remove Edit and Quick Edit row actions for observer
 */
add_filter('post_row_actions', 'observer_mode_row_actions_cleanup', 10, 2);
add_filter('page_row_actions', 'observer_mode_row_actions_cleanup', 10, 2);
function observer_mode_row_actions_cleanup($actions, $post) {
    if (!observer_mode_is_observer()) {
        return $actions;
    }
    unset($actions['edit'], $actions['inline hide-if-no-js']);
    return $actions;
}

/**
 * Block access to post-new.php so they cannot create new posts/pages
 */
add_action('load-post-new.php', 'observer_mode_block_post_new');
function observer_mode_block_post_new() {
    if (!observer_mode_is_observer()) {
        return;
    }
    wp_die(
        __('Observer mode is enabled. Creating new content is not allowed.', 'observer-mode'),
        __('Observer Mode', 'observer-mode'),
        array('response' => 403)
    );
}

/**
 * Block wp_insert_post for Observer Admin so they cannot save edits
 * This hits classic editor and the core post insertion path.
 */
add_filter('wp_insert_post_data', 'observer_mode_block_wp_insert_post_data', 10, 2);
function observer_mode_block_wp_insert_post_data($data, $postarr) {

    if (!observer_mode_is_observer()) {
        return $data;
    }

    // Only block in admin context to avoid weird background operations
    if (!is_admin()) {
        return $data;
    }

    wp_die(
        __('Observer mode is enabled. Changes cannot be saved.', 'observer-mode'),
        __('Observer Mode', 'observer-mode'),
        array('response' => 403)
    );
}

/**
 * Block Gutenberg REST saves for posts and pages
 */
add_filter('rest_pre_insert_post', 'observer_mode_block_rest_insert', 10, 3);
add_filter('rest_pre_insert_page', 'observer_mode_block_rest_insert', 10, 3);
function observer_mode_block_rest_insert($prepared_post, $reques_

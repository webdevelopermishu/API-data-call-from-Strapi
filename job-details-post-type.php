<?php
/**
 * Plugin Name: Job Details Meta Handler
 * Description: Automatically registers meta fields for the Job Details custom post type and integrates them into the REST API.
 * Version: 1.2
 * Author: Towfique Ar Rahman
 * Author URI: https://webappdevelop.com/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Register Custom Post Type: Job Details
function create_job_details_post_type() {
    $labels = array(
        'name'               => _x('Job Details', 'Post Type General Name', 'textdomain'),
        'singular_name'      => _x('Job Detail', 'Post Type Singular Name', 'textdomain'),
        'menu_name'          => __('Job Details', 'textdomain'),
        'name_admin_bar'     => __('Job Detail', 'textdomain'),
        'archives'           => __('Job Archives', 'textdomain'),
        'attributes'         => __('Job Attributes', 'textdomain'),
        'all_items'          => __('All Jobs', 'textdomain'),
        'add_new_item'       => __('Add New Job', 'textdomain'),
        'add_new'            => __('Add New', 'textdomain'),
        'edit_item'          => __('Edit Job', 'textdomain'),
        'view_item'          => __('View Job', 'textdomain'),
        'search_items'       => __('Search Job', 'textdomain'),
        'not_found'          => __('Not found', 'textdomain'),
        'not_found_in_trash' => __('Not found in Trash', 'textdomain'),
    );

    $args = array(
        'label'              => __('Job Detail', 'textdomain'),
        'description'        => __('Post Type for Job Details', 'textdomain'),
        'labels'             => $labels,
        'supports'           => array('title', 'editor', 'thumbnail'),
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 5,
        'has_archive'        => true,
        'show_in_rest'       => true,
    );

    register_post_type('job-details', $args);
}
add_action('init', 'create_job_details_post_type');

function create_google_jobs_post_type() {
    $labels = array(
        'name'               => _x('Google Jobs', 'Post Type General Name', 'textdomain'),
        'singular_name'      => _x('Google Job', 'Post Type Singular Name', 'textdomain'),
        'menu_name'          => __('Google Jobs', 'textdomain'),
        'name_admin_bar'     => __('Google Job', 'textdomain'),
        'all_items'          => __('All Google Jobs', 'textdomain'),
        'add_new_item'       => __('Add New Google Job', 'textdomain'),
        'add_new'            => __('Add New', 'textdomain'),
        'edit_item'          => __('Edit Google Job', 'textdomain'),
        'view_item'          => __('View Google Job', 'textdomain'),
        'search_items'       => __('Search Google Job', 'textdomain'),
        'not_found'          => __('Not found', 'textdomain'),
        'not_found_in_trash' => __('Not found in Trash', 'textdomain'),
    );

    $args = array(
        'label'              => __('Google Job', 'textdomain'),
        'description'        => __('Post Type for Google Jobs', 'textdomain'),
        'labels'             => $labels,
        'supports'           => array('title', 'editor'),
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 6,
        'has_archive'        => true,
        'show_in_rest'       => true,
    );

    register_post_type('google-jobs', $args);
}
add_action('init', 'create_google_jobs_post_type');


// Define meta fields
function get_job_meta_fields() {
    return [
        'job_id'              => 'Job ID',
        'document_id'         => 'Document ID',
        'company_name'        => 'Company Name',
        'company_logo'        => 'Company Logo URL',
        'location'            => 'Location',
        'job_url'             => 'Job URL',
        'number_of_applicants'=> 'Number of Applicants',
        'flexibility'         => 'Flexibility',
        'experience_level'    => 'Experience Level',
        'contract_type'       => 'Contract Type',
        'salary_range'        => 'Salary Range', // New meta field for Salary Range
        'published_at'        => 'Published At',
    ];
}

// Register meta fields
function register_job_meta_fields() {
    $meta_fields = get_job_meta_fields();

    foreach ($meta_fields as $key => $label) {
        register_post_meta('job-details', $key, [
            'type'         => 'string',
            'single'       => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
        ]);
    }
}
add_action('init', 'register_job_meta_fields');

function register_google_jobs_meta_fields() {
    $fields = [
        'google_job_id'  => 'Google Job ID',
        'document_id'    => 'Document ID',
        'company'        => 'Company Name',
        'location'       => 'Location',
        'job_url'        => 'Job URL',
        'salary_min'     => 'Minimum Salary',
        'salary_max'     => 'Maximum Salary',
        'job_type'       => 'Job Type',
        'published_at'   => 'Published At',
    ];

    foreach ($fields as $key => $label) {
        register_post_meta('google-jobs', $key, [
            'type'              => 'string',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'sanitize_text_field',
        ]);
    }
}
add_action('init', 'register_google_jobs_meta_fields');

// Add Meta Box for Job Details
function add_job_details_meta_box() {
    add_meta_box(
        'job_details_meta_box',
        __('Job Details', 'textdomain'),
        'display_job_details_meta_box',
        'job-details',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_job_details_meta_box');

// Display Meta Box
function display_job_details_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'job_details_nonce');
    $meta_fields = get_job_meta_fields();

    foreach ($meta_fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<label for="' . $key . '">' . $label . ':</label><br>';
        echo '<input type="text" id="' . $key . '" name="' . $key . '" value="' . esc_attr($value) . '" size="50" /><br><br>';
    }
}

// Save Meta Box Data
function save_job_details_meta_box_data($post_id) {
    if (!isset($_POST['job_details_nonce']) || !wp_verify_nonce($_POST['job_details_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    $meta_fields = get_job_meta_fields();

    foreach ($meta_fields as $key => $label) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }
}
add_action('save_post', 'save_job_details_meta_box_data');

<?php
/**
 * Plugin Name: REST API for Jobs
 * Description: Fetch job data from an external API and insert it into the Job Details post type.
 * Version: 1.2
 * Author: Towfique Ar Rahman
 * Author URI: https://webappdevelop.com/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Hook into WordPress initialization to fetch and insert jobs
add_action('init', 'fetch_and_insert_jobs');

function fetch_and_insert_jobs() {
    // External API URL
    $api_url = 'https://colorful-cherry-497ddb593b.strapiapp.com/api/featured-jobs';

    // Bearer token for authentication
    $bearer_token = 'c16a20cca32069995e35f496a70cf96831db315f83ef53e94a77aa0479ad23781953a216ba60a7737508a08bb79ef6b88ce6b612855d5115d47a9347b15dd4e6436b82e41703b781a466d158b1d9ef58911a804af92d6d72cbf7e08affd7efbc01f02ad02ef540574e8b327ad14fc3cf43a1e2f533f4447517770bc3df65430e';

    // Fetch data from the external API
    $response = wp_remote_get($api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $bearer_token,
        ),
    ));

    if (is_wp_error($response)) {
        error_log('Failed to fetch jobs: ' . $response->get_error_message());
        return;
    }

    $jobs = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($jobs['data']) || empty($jobs['data'])) {
        error_log('No job data found in API response.');
        return;
    }

    foreach ($jobs['data'] as $job) {
        // Validate job data
        if (empty($job['id']) || empty($job['title'])) {
            continue; // Skip invalid job entries
        }

        // Check if the job already exists in the 'job-details' post type
        $existing_jobs = get_posts(array(
            'post_type'   => 'job-details',
            'meta_key'    => 'job_id',
            'meta_value'  => $job['id'],
            'numberposts' => 1,
        ));

        if (empty($existing_jobs)) {
            // Insert a new job post
            $post_id = wp_insert_post(array(
                'post_title'   => sanitize_text_field($job['title']),
                'post_content' => isset($job['description']) ? sanitize_textarea_field($job['description']) : '',
                'post_status'  => 'publish',
                'post_type'    => 'job-details',
            ));

            // Add metadata to the post
            if (!is_wp_error($post_id)) {
                update_post_meta($post_id, 'job_id', sanitize_text_field($job['id']));
                update_post_meta($post_id, 'document_id', isset($job['documentId']) ? sanitize_text_field($job['documentId']) : '');
                update_post_meta($post_id, 'company_name', isset($job['company_name']) ? sanitize_text_field($job['company_name']) : '');
                update_post_meta($post_id, 'company_logo', isset($job['company_logo']) ? esc_url_raw($job['company_logo']) : '');
                update_post_meta($post_id, 'location', isset($job['location']) ? sanitize_text_field($job['location']) : '');
                update_post_meta($post_id, 'job_url', isset($job['url']) ? esc_url_raw($job['url']) : '');
                update_post_meta($post_id, 'number_of_applicants', isset($job['number_of_applicant']) && is_numeric($job['number_of_applicant']) ? intval($job['number_of_applicant']) : '');
                update_post_meta($post_id, 'flexibility', isset($job['flexibility']) ? sanitize_text_field($job['flexibility']) : '');
                update_post_meta($post_id, 'experience_level', isset($job['experience_level']) ? sanitize_text_field($job['experience_level']) : '');
                update_post_meta($post_id, 'contract_type', isset($job['contract_type']) ? sanitize_text_field($job['contract_type']) : '');
                update_post_meta($post_id, 'salary_range', isset($job['salary_range']) ? sanitize_text_field($job['salary_range']) : 'Not Specified');
                update_post_meta($post_id, 'published_at', isset($job['publishedAt']) ? sanitize_text_field($job['publishedAt']) : current_time('mysql'));
            }
        }
    }
}

function fetch_and_insert_google_jobs() {
    $api_url = 'https://colorful-cherry-497ddb593b.strapiapp.com/api/google-jobs';
    $bearer_token = 'c16a20cca32069995e35f496a70cf96831db315f83ef53e94a77aa0479ad23781953a216ba60a7737508a08bb79ef6b88ce6b612855d5115d47a9347b15dd4e6436b82e41703b781a466d158b1d9ef58911a804af92d6d72cbf7e08affd7efbc01f02ad02ef540574e8b327ad14fc3cf43a1e2f533f4447517770bc3df65430e';

    $response = wp_remote_get($api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $bearer_token,
        ),
    ));

    if (is_wp_error($response)) {
        error_log('Failed to fetch Google jobs: ' . $response->get_error_message());
        return;
    }

    $jobs = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($jobs['data']) || empty($jobs['data'])) {
        error_log('No Google job data found in API response.');
        return;
    }

    foreach ($jobs['data'] as $job) {
        if (empty($job['id']) || empty($job['title'])) {
            continue;
        }

        $existing_jobs = get_posts(array(
            'post_type'   => 'google-jobs',
            'meta_key'    => 'google_job_id',
            'meta_value'  => $job['id'],
            'numberposts' => 1,
        ));

        if (empty($existing_jobs)) {
            $post_id = wp_insert_post(array(
                'post_title'   => sanitize_text_field($job['title']),
                'post_content' => isset($job['description']) ? sanitize_textarea_field($job['description']) : '',
                'post_status'  => 'publish',
                'post_type'    => 'google-jobs',
            ));

            if (!is_wp_error($post_id)) {
                update_post_meta($post_id, 'google_job_id', sanitize_text_field($job['id']));
                update_post_meta($post_id, 'document_id', sanitize_text_field($job['documentId']));
                update_post_meta($post_id, 'company', sanitize_text_field($job['company']));
                update_post_meta($post_id, 'location', sanitize_text_field($job['location']));
                update_post_meta($post_id, 'job_url', esc_url_raw($job['job_url']));
                update_post_meta($post_id, 'salary_min', sanitize_text_field($job['min_amount']));
                update_post_meta($post_id, 'salary_max', sanitize_text_field($job['max_amount']));
                update_post_meta($post_id, 'job_type', sanitize_text_field($job['job_type']));
                update_post_meta($post_id, 'published_at', sanitize_text_field($job['publishedAt']));
            }
        }
    }
}
add_action('init', 'fetch_and_insert_google_jobs');


// Expose custom meta fields in REST API
function add_custom_fields_to_rest_api($response, $post, $request) {
    if ($post->post_type !== 'job-details') {
        return $response;
    }

    $fields = [
        'job_id',
        'document_id',
        'company_name',
        'company_logo',
        'location',
        'job_url',
        'number_of_applicants',
        'flexibility',
        'experience_level',
        'contract_type',
        'salary_range',
        'published_at',
    ];

    foreach ($fields as $field) {
        $response->data[$field] = get_post_meta($post->ID, $field, true);
    }

    return $response;
}
add_filter('rest_prepare_job-details', 'add_custom_fields_to_rest_api', 10, 3);

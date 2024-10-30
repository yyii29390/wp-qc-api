<?php
/**
 * Plugin Name: Quadcell API Plugin
 * Plugin URI: http://localhost/quadcell-api
 * Description: Quadcell API plugin.
 * Version: 1.3.1.27
 * Author: Quadcell Communications Ltd
 * Author URI: http://www.quadcell.com
 * License: GPL2
 */

// Include the API commands and encryption functions
include_once plugin_dir_path(__FILE__) . 'api-commands.php';
include_once plugin_dir_path(__FILE__) . 'src/encryption-functions.php';
include_once plugin_dir_path(__FILE__) . 'src/version-update.php';
include_once plugin_dir_path(__FILE__) . 'src/quadcell-sim-records.php';
include_once plugin_dir_path(__FILE__) . 'src/quadcell-plan-code.php';
require_once plugin_dir_path(__FILE__) . 'src/quadcell-package-code.php';
require_once plugin_dir_path(__FILE__) . 'src/quadcell-api-mapping-functions.php';
require_once plugin_dir_path(__FILE__) . 'src/quadcell-product-mapping.php';

// Initialize the updater
$current_version = '1.2';
$update_url = 'http://localhost/quadcell-api-update';
$plugin_slug = __FILE__;

new Quadcell_API_Updater($current_version, $update_url, $plugin_slug);


/**
function quadcell_api_clear_plan_to_api_mappings() {
    delete_option('quadcell_api_plan_to_api_mappings');
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p>Plan to API mappings have been cleared.</p></div>';
    });
}

// Uncomment the following line to clear the mappings
add_action('admin_init', 'quadcell_api_clear_plan_to_api_mappings');
*/


// Add admin menu
add_action('admin_menu', 'quadcell_api_create_menu');
function verify_nonce($action): void
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], $action)) {
        error_log('Invalid nonce'); // Log invalid nonce error
        wp_send_json_error(array('message' => 'Invalid nonce'), 400);
        exit;
    }
}
function quadcell_api_create_menu()
{
    add_menu_page(
        'Quadcell API Settings',
        'Quadcell API',
        'manage_options',
        'quadcell-api-settings',
        'quadcell_api_settings_page'
    );
    add_action('admin_init', 'quadcell_api_register_settings');
}

register_activation_hook(__FILE__, 'quadcell_create_sim_records_table');
register_activation_hook(__FILE__, 'quadcell_create_plancode_table');
register_activation_hook(__FILE__, 'quadcell_create_package_code_table');
register_activation_hook(__FILE__, 'create_qc_api_mapping_table');
register_activation_hook(__FILE__, 'quadcell_create_product_map_table');



function quadcell_api_register_settings()
{
    // Register connection settings
    register_setting('quadcell-api-connection-group', 'quadcell_api_auth_key');
    register_setting('quadcell-api-connection-group', 'quadcell_api_url');
    register_setting('quadcell-api-connection-group', 'quadcell_api_keys', array(
        'type' => 'array',
        'default' => array(),
    ));
    register_setting(' ', 'quadcell_api_product_mappings', array(
        'type' => 'array',
        'default' => array(),
    ));
    register_setting('quadcell-api-plan-to-api-group', 'quadcell_api_plan_to_api_mappings', array(
        'type' => 'array',
        'default' => array(),
    ));
}

function quadcell_api_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Quadcell API Settings</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=quadcell-api-settings&tab=connection"
                class="nav-tab <?php echo get_current_tab() == 'connection' ? 'nav-tab-active' : ''; ?>">Connection</a>
            <a href="?page=quadcell-api-settings&tab=api_fields"
                class="nav-tab <?php echo get_current_tab() == 'api_fields' ? 'nav-tab-active' : ''; ?>">API Fields</a>
            <a href="?page=quadcell-api-settings&tab=product_mapping"
                class="nav-tab <?php echo get_current_tab() == 'product_mapping' ? 'nav-tab-active' : ''; ?>">Product
                Mapping</a>
            <a href="?page=quadcell-api-settings&tab=plan_to_api"
                class="nav-tab <?php echo get_current_tab() == 'plan_to_api' ? 'nav-tab-active' : ''; ?>">API Profile</a>
            <a href="?page=quadcell-api-settings&tab=order_processing"
                class="nav-tab <?php echo get_current_tab() == 'order_processing' ? 'nav-tab-active' : ''; ?>">Order
                Processing</a>
            <a href="?page=quadcell-api-settings&tab=sim_records"
                class="nav-tab <?php echo get_current_tab() == 'sim_records' ? 'nav-tab-active' : ''; ?>">SIM Records</a>
            <a href="?page=quadcell-api-settings&tab=plan_code"
                class="nav-tab <?php echo get_current_tab() == 'plan_code' ? 'nav-tab-active' : ''; ?>">Plan Code</a>
            <a href="?page=quadcell-api-settings&tab=package_code"
                class="nav-tab <?php echo get_current_tab() == 'package_code' ? 'nav-tab-active' : ''; ?>">Package Code</a>
        </h2>
        <form method="post" action="options.php">
            <?php
            if (get_current_tab() == 'api_fields') {
                settings_fields('quadcell-api-fields-group');
                do_settings_sections('quadcell-api-fields-group');
                quadcell_api_fields_section();
            } else if (get_current_tab() == 'product_mapping') {
                settings_fields('quadcell-api-product-mapping-group');
                do_settings_sections('quadcell-api-product-mapping-group');
                quadcell_api_product_mapping_section();
            } else if (get_current_tab() == 'plan_to_api') {
                settings_fields('quadcell-api-plan-to-api-group');
                do_settings_sections('quadcell-api-plan-to-api-group');
                quadcell_api_plan_to_api_section();
            } else if (get_current_tab() == 'order_processing') {
                quadcell_api_order_processing_section();
            } else if (get_current_tab() == 'sim_records') {
                quadcell_api_sim_records_section();
            } else if (get_current_tab() == 'plan_code') {
                quadcell_api_plan_code_section();
            } else if (get_current_tab() == 'package_code') {
                quadcell_api_package_code_section();
            } else {
                settings_fields('quadcell-api-connection-group');
                do_settings_sections('quadcell-api-connection-group');
                quadcell_api_connection_section();
            }
            submit_button();
            ?>
        </form>
        <?php if (get_current_tab() == 'plan_to_api'): ?>
            <form method="post">
                <input type="hidden" name="quadcell_api_clear_plan_to_api" value="1">
                <?php submit_button('Clear Plan to API Mappings'); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

function get_current_tab()
{
    return isset($_GET['tab']) ? $_GET['tab'] : 'connection';
}



function quadcell_api_connection_section()
{
    $keys = get_option('quadcell_api_keys', array());
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Auth Key</th>
            <td><input type="text" name="quadcell_api_auth_key"
                    value="<?php echo esc_attr(get_option('quadcell_api_auth_key')); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row">URL</th>
            <td><input type="text" name="quadcell_api_url"
                    value="<?php echo esc_attr(get_option('quadcell_api_url')); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row">API Keys</th>
            <td>
                <ul id="quadcell-api-keys-list">
                    <?php foreach ($keys as $index => $key): ?>
                        <li>
                            <input type="text" name="quadcell_api_keys[]" value="<?php echo esc_attr($key); ?>" />
                            <button type="button" class="button remove-key">Remove</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="button" id="add-key">Add Key</button>
            </td>
        </tr>
    </table>
    <script>
        jQuery(document).ready(function ($) {
            $('#add-key').click(function () {
                $('#quadcell-api-keys-list').append(
                    '<li><input type="text" name="quadcell_api_keys[]" value="" /><button type="button" class="button remove-key">Remove</button></li>'
                );
            });

            $(document).on('click', '.remove-key', function () {
                $(this).parent().remove();
            });
        });
    </script>
    <?php
}

function quadcell_api_fields_section()
{
    global $api_commands;

    ?>
    <h3>API Commands and Parameters</h3>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>Command</th>
                <th>Parameters</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($api_commands as $command => $fields): ?>
                <tr>
                    <td><?php echo $command; ?></td>
                    <td>
                        <table class="widefat fixed" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Required</th>
                                    <th>Type</th>
                                    <th>Max Length</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fields as $field_name => $properties): ?>
                                    <tr>
                                        <td><?php echo $field_name; ?></td>
                                        <td><?php echo $properties['required'] ? 'Yes' : 'No'; ?></td>
                                        <td><?php echo $properties['type']; ?></td>
                                        <td><?php echo $properties['max_length']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

// Add this function to create the "Order Processing" tab
function quadcell_api_order_processing_section()
{
    // Ensure WooCommerce is active
    if (!class_exists('WooCommerce')) {
        echo '<div class="notice notice-error"><p>WooCommerce is not active. Please activate WooCommerce to use this feature.</p></div>';
        return;
    }

    // Fetch completed orders
    $args = array(
        'status' => 'completed',
        'limit' => -1,
    );

    $orders = wc_get_orders($args);
    $product_mappings = get_option('quadcell_api_product_mappings', array());
    $product_mappings_assoc = array();

    // Convert product mappings to associative array for easier lookup
    foreach ($product_mappings as $mapping) {
        $product_mappings_assoc[$mapping['product_code']] = $mapping['plan_code'];
    }

    echo '<h3>Order Processing</h3>';
    echo '<table class="widefat fixed" cellspacing="0">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Order Date</th>';
    echo '<th>Order ID</th>';
    echo '<th>Product SKU</th>';
    echo '<th>Quantity</th>';
    echo '<th>Plan Code</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($orders as $order) {
        $order_date = $order->get_date_created()->date('Y-m-d H:i:s');
        $order_id = $order->get_id();
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if (!$product)
                continue;

            $product_sku = $product->get_sku();
            $quantity = $item->get_quantity();
            $plan_code = isset($product_mappings_assoc[$product_sku]) ? $product_mappings_assoc[$product_sku] : 'N/A';

            echo '<tr>';
            echo '<td>' . esc_html($order_date) . '</td>';
            echo '<td>' . esc_html($order_id) . '</td>';
            echo '<td>' . esc_html($product_sku) . '</td>';
            echo '<td>' . esc_html($quantity) . '</td>';
            echo '<td>' . esc_html($plan_code) . '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
}


function quadcell_api_import_csv()
{
    if (!isset($_FILES['import_file'])) {
        echo '<div class="notice notice-error is-dismissible"><p>No file uploaded.</p></div>';
        return;
    }

    $file = $_FILES['import_file']['tmp_name'];
    $handle = fopen($file, 'r');

    if ($handle === false) {
        echo '<div class="notice notice-error is-dismissible"><p>Could not open file.</p></div>';
        return;
    }

    $sim_records = get_option('quadcell_api_sim_records', array());
    $existing_imsis = array_column($sim_records, 'imsi');

    $row = 0;
    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        if ($row === 0) {
            // Skip header row
            $row++;
            continue;
        }

        $imsi = $data[0];
        $iccid = $data[1];
        $msisdn = $data[2];
        $action = strtoupper($data[3]);

        if ($action === 'A') {
            if (in_array($imsi, $existing_imsis)) {
                echo '<div class="notice notice-error is-dismissible"><p>Duplicate IMSI found: ' . esc_html($imsi) . '</p></div>';
                continue;
            }

            $sim_records[] = array(
                'imsi' => $imsi,
                'iccid' => $iccid,
                'msisdn' => $msisdn,
                'in_use' => false,
                'update_status' => 'New',
            );
            $existing_imsis[] = $imsi;
        } elseif ($action === 'U') {
            $key = array_search($imsi, $existing_imsis);
            if ($key !== false) {
                $sim_records[$key]['iccid'] = $iccid;
                $sim_records[$key]['msisdn'] = $msisdn;
                $sim_records[$key]['update_status'] = 'Updated';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>IMSI not found for update: ' . esc_html($imsi) . '</p></div>';
            }
        }
    }

    fclose($handle);
    update_option('quadcell_api_sim_records', $sim_records);
    echo '<div class="notice notice-success is-dismissible"><p>CSV imported successfully.</p></div>';
}

function quadcell_api_download_template()
{
    $filename = 'sim_records_template.csv';
    $content = "IMSI,ICCID,MSISDN,ACTION\n454310860020001,852196105420001,852196105420001,A";

    header('Content-Description: File Transfer');
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($content));

    echo $content;
    exit;
}


function quadcell_api_call($endpoint, $data)
{
    $auth_key = get_option('quadcell_api_auth_key');
    $url = get_option('quadcell_api_url');

    // Remove empty fields
    $data = array_filter($data, function ($value) {
        return $value !== '';
    });

    // Add authKey to the beginning of the data array
    $data = array_merge(['authKey' => $auth_key], $data);

    $json_data = json_encode($data);
    $final_encrypted_data = quadcell_api_encrypt($json_data);

    $response = wp_remote_post("$url/$endpoint", array(
        'body' => $final_encrypted_data,
        'headers' => array(
            'Authorization' => "Bearer $auth_key",
            'Content-Type' => 'application/json'
        )
    ));

    if (is_wp_error($response)) {
        return array('error' => $response->get_error_message());
    }

    $response_body = wp_remote_retrieve_body($response);
    error_log("Response body: $response_body");


    // Check if the response is a valid JSON string
    $json_decoded = json_decode($response_body, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        error_log("Response is valid JSON: " . print_r($json_decoded, true));
        return array(
            'final_encrypted_data' => $final_encrypted_data,
            'encrypted' => $response_body,
            'decrypted' => $json_decoded
        );
    }

    $decrypted_body = quadcell_api_decrypt($response_body);

    return array(
        'final_encrypted_data' => $final_encrypted_data,
        'encrypted' => $response_body,
        'decrypted' => $decrypted_body
    );
}

function quadcell_api_ajax_handler()
{
    if (!isset($_POST['command']) || !isset($_POST['fields'])) {
        wp_send_json_error('Invalid request.');
        return;
    }

    $command = sanitize_text_field($_POST['command']);
    $fields = array_map('sanitize_text_field', $_POST['fields']);

    // Remove empty fields
    $fields = array_filter($fields, function ($value) {
        return $value !== '';
    });

    $auth_key = get_option('quadcell_api_auth_key');
    $fields = array_merge(['authKey' => $auth_key], $fields);

    $result = quadcell_api_call($command, $fields);

    if (isset($result['error'])) {
        wp_send_json_error($result['error']);
    } else {
        wp_send_json_success(array(
            'final_encrypted_data' => $result['final_encrypted_data'],
            'encrypted_data' => $result['encrypted'],
            'decrypted_data' => $result['decrypted']
        ));
    }
}
add_action('wp_ajax_quadcell_api_call', 'quadcell_api_ajax_handler');
add_action('wp_ajax_nopriv_quadcell_api_call', 'quadcell_api_ajax_handler');

// Handle clearing Plan to API mappings
if (isset($_POST['quadcell_api_clear_plan_to_api'])) {
    delete_option('quadcell_api_plan_to_api_mappings');
    add_action('admin_notices', function () {
        echo '<div class="notice notice-success is-dismissible"><p>Plan to API mappings cleared.</p></div>';
    });
}


// Enqueue scripts
function quadcell_api_frontend_enqueue_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('quadcell-api-script', plugin_dir_url(__FILE__) . 'quadcell-api.js', array('jquery'), null, true);

    wp_localize_script('quadcell-api-script', 'quadcellApiCommands', $GLOBALS['api_commands']);
    wp_localize_script('quadcell-api-script', 'quadcellApiSettings', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'url' => get_option('quadcell_api_url')
    ));

}
add_action('wp_enqueue_scripts', 'quadcell_api_frontend_enqueue_scripts');


function quadcell_api_mapping_enqueue_scripts()
{
    global $wpdb, $api_commands; // Ensure these variables are accessible

    wp_enqueue_script('quadcell-api-mapping-js', plugin_dir_url(__FILE__) . 'src/quadcell-api-mapping.js', array('jquery', 'jquery-ui-sortable'), null, true);

    wp_localize_script('quadcell-api-mapping-js', 'quadcellApiMapping', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('fetch_plan_code_info_nonce'),
        'api_commands' => $api_commands,  // Pass the API commands to JS
        'plan_codes' => $wpdb->get_results("SELECT planCode FROM {$wpdb->prefix}qc_plancode", ARRAY_A),
        'package_code' => $wpdb->get_results("SELECT package_code FROM {$wpdb->prefix}qc_package_code", ARRAY_A)
    ));
}
add_action('admin_enqueue_scripts', 'quadcell_api_mapping_enqueue_scripts');

function quadcell_api_frontend_form()
{
    ob_start();
    global $api_commands;

    ?>
    <div class="quadcell-api-form">
        <form id="quadcell-api-form">
            <div>
                <label for="quadcell-api-command">API Command:</label>
                <select id="quadcell-api-command" name="quadcell_api_command">
                    <option value="">Select Command</option>
                    <?php foreach ($api_commands as $command => $fields): ?>
                        <option value="<?php echo $command; ?>"><?php echo $command; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="quadcell-api-fields"></div>
            <button type="button" id="quadcell-api-submit">Post</button>
        </form>
        <div id="quadcell-api-message">
            <h4>Request Details</h4>
            <p><strong>Full URL:</strong> <span id="quadcell-api-full-url"></span></p>
            <p><strong>Non-encrypted JSON:</strong> <span id="quadcell-api-non-encrypted-json"></span></p>
            <p><strong>Encrypted JSON:</strong> <span id="quadcell-api-encrypted-json"></span></p>
        </div>
        <div id="quadcell-api-result">
            <h4>Result</h4>
            <p id="quadcell-api-result-message"></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('quadcell_api_form', 'quadcell_api_frontend_form');
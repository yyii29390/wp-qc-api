<?php
// Ensure this file is only accessed via WordPress
defined('ABSPATH') or die('No script kiddies please!');

// Define your AJAX actions
add_action('wp_ajax_delete_package_code', 'delete_package_code');
add_action('wp_ajax_add_package_code', 'add_package_code');
add_action('wp_ajax_load_package_code', 'load_package_code');
add_action('wp_ajax_update_package_code', 'update_package_code');

function quadcell_create_package_code_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_package_code';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        applicable_IMSI varchar(5) NOT NULL,
        package_code varchar(6) NOT NULL,
        preset_Data_Volume varchar(20) NOT NULL,
        validity_Mode varchar(20) NOT NULL,
        FUP_Mode varchar(30) NOT NULL,
        roaming_Region varchar(50) NOT NULL,
        roaming_Country varchar(50) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    error_log("Executing dbDelta with SQL: $sql");
    dbDelta($sql);
    error_log("dbDelta execution completed");

    // Check if the table was created successfully
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        error_log("Failed to create table: $table_name");
    } else {
        error_log("Table created successfully: $table_name");
    }
}
function verify_nonce_package_code()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'quadcell_package_code_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'), 400);
        exit;
    }
}
// register_activation_hook(__FILE__, 'quadcell_create_package_code_table');
function add_package_code()
{


    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_package_code';

    $data = array(
        'applicable_IMSI' => sanitize_text_field($_POST['applicable_IMSI']),
        'package_Code' => sanitize_text_field($_POST['package_Code']),
        'preset_Data_Volume' => sanitize_text_field($_POST['preset_Data_Volume']),
        'validity_Mode' => sanitize_text_field($_POST['validity_Mode']),
        'FUP_Mode' => sanitize_text_field($_POST['FUP_Mode']),
        'roaming_Region' => sanitize_text_field($_POST['roaming_Region']),
        'roaming_Country' => sanitize_text_field($_POST['roaming_Country']),

    );

    $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s');

    if ($wpdb->insert($table_name, $data, $format)) {
        $data['id'] = $wpdb->insert_id;
        wp_send_json_success(array('message' => 'package_code added successfully', 'package_code' => $data));
    } else {
        wp_send_json_error(array('message' => 'Failed to add package_code '), 500);
    }
}
function delete_package_code()
{

    verify_nonce_package_code();
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_package_code';

    $id = intval($_POST['id']);

    if ($wpdb->delete($table_name, array('id' => $id), array('%d'))) {
        wp_send_json_success(array('message' => 'Package code deleted successfully'));
    } else {
        wp_send_json_error(array('message' => 'Failed to delete Package code '), 500);
    }
}

function load_package_code()
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_package_code';
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if ($results) {
        ob_start();
        foreach ($results as $package_code) {
            echo '<tr data-id="' . esc_attr($package_code['id']) . '">';
            echo '<td>' . esc_html($package_code['id']) . '</td>';
            echo '<td>' . esc_html($package_code['applicable_IMSI']) . '</td>';
            echo '<td>' . esc_html($package_code['package_Code']) . '</td>';
            echo '<td>' . esc_html($package_code['preset_Data_Volume']) . '</td>';
            echo '<td>' . esc_html($package_code['validity_Mode']) . '</td>';
            echo '<td>' . esc_html($package_code['FUP_Mode']) . '</td>';
            echo '<td>' . esc_html($package_code['roaming_Region']) . '</td>';
            echo '<td>' . esc_html($package_code['roaming_Country']) . '</td>';
            echo '<td>';
            echo '<button class="delete-package-code-button button" data-id="' . esc_attr($package_code['id']) . '">Delete</button> ';
            echo '<button class="edit-package-code-button button" data-id="' . esc_attr($package_code['id']) . '">Edit</button>';
            echo '</td>';
            echo '</tr>';
        }
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    } else {
        wp_send_json_error(array('message' => 'No records found'), 404);
    }
}
function update_package_code()
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_package_code';

    $id = intval($_POST['id']);
    $data = array(
        'applicable_IMSI' => sanitize_text_field($_POST['applicable_IMSI']),
        'package_Code' => sanitize_text_field($_POST['package_Code']),
        'preset_Data_Volume' => sanitize_text_field($_POST['preset_Data_Volume']),
        'validity_Mode' => sanitize_text_field($_POST['validity_Mode']),
        'FUP_Mode' => sanitize_text_field($_POST['FUP_Mode']),
        'roaming_Region' => sanitize_text_field($_POST['roaming_Region']),
        'roaming_Country' => sanitize_text_field($_POST['roaming_Country']),
    );

    error_log('Update data: ' . print_r($data, true)); // Log received data

    $where = array('id' => $id);
    $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s');
    // $where_format = array('%d');

    if ($wpdb->update($table_name, $data, $where, $format)) {
        wp_send_json_success(array('message' => 'Package code updated successfully', 'sim_record' => $data));

    } else {
        wp_send_json_error(array('message' => 'Failed to update Package code'), 500);
    }
}


function quadcell_api_package_code_section()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_package_code';

    // Fetch all Package codes
    $package_codes = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    ?>
    <div class="wrap">
        <h1>Package Code Management</h1>

        <!-- Form to add a new Package code -->
        <form id="add-package-code-form">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Applicable IMSI</th>
                    <td><input type="text" id="applicable_IMSI" name="applicable_IMSI" value="" maxlength="5" type="number"
                            required />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Package Code</th>
                    <td><input type="text" id="package_code" name="package_code" value="" required maxlength="6" required />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Preset Data Volume</th>
                    <td><input type="text" id="preset_Data_Volume" name="preset_Data_Volume" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Validity Mode</th>
                    <td><input type="text" id="validity_Mode" name="validity_Mode" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">FUP Mode</th>
                    <td><input type="text" id="FUP_Mode" name="FUP_Mode" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Roaming Region</th>
                    <td><input type="text" id="roaming_Region" name="roaming_Region" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Roaming Country</th>
                    <td><input type="text" id="roaming_Country" name="roaming_Country" value="" required /></td>
                </tr>
            </table>
            <button type="button" id="add-package-code-button" class="button button-primary">Add Package Code</button>
            <button type="button" id="update-package-code-button" class="button button-primary" style="display:none;">Update
                Package code</button>
            <button type="button" id="cancel-edit-button" class="button" style="display:none;">Cancel</button>
        </form>

        <!-- Table to display all Package codes -->
        <h2>Existing Package Codes</h2>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Applicable IMSI</th>
                    <th>Package Code</th>
                    <th>Preset Data Volume</th>
                    <th>Validity Mode</th>
                    <th>FUP Mode</th>
                    <th>Roaming Region</th>
                    <th>Roaming Country</th>
                </tr>
            </thead>
            <tbody id="package-codes-table-body">
                <?php foreach ($package_codes as $package_code): ?>
                    <tr>
                        <td><?php echo esc_html($package_code['id']); ?></td>
                        <td><?php echo esc_html($package_code['applicable_IMSI']); ?></td>
                        <td><?php echo esc_html($package_code['package_code']); ?></td>
                        <td><?php echo esc_html($package_code['preset_Data_Volume']); ?></td>
                        <td><?php echo esc_html($package_code['validity_Mode']); ?></td>
                        <td><?php echo esc_html($package_code['FUP_Mode']); ?></td>
                        <td><?php echo esc_html($package_code['roaming_Region']); ?></td>
                        <td><?php echo esc_html($package_code['roaming_Country']); ?></td>
                        <td>
                            <button class="delete-package-code-button button"
                                data-id="<?php echo esc_attr($package_code['id']); ?>">Delete</button>
                            <button class="edit-package-code-button button"
                                data-id="<?php echo esc_attr($package_code['id']); ?>">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}



// Enqueue scripts
function quadcell_package_code_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('quadcell-package-code', plugin_dir_url(__FILE__) . 'quadcell-package-code.js', array('jquery'), null, true);
    wp_localize_script('quadcell-package-code', 'quadcellPackageCode', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('quadcell_package_code_nonce'),
    ));
}
add_action('admin_enqueue_scripts', 'quadcell_package_code_scripts');
?>
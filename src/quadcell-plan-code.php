<?php
// Ensure this file is only accessed via WordPress
defined('ABSPATH') or die('No script kiddies please!');

// Define your AJAX actions
add_action('wp_ajax_delete_plan_code', 'delete_plan_code');
add_action('wp_ajax_add_plan_code', 'add_plan_code');
add_action('wp_ajax_load_plan_code', 'load_plan_code');
add_action('wp_ajax_update_plan_code', 'update_plan_code');

function quadcell_create_plancode_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_plancode';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        applicable_IMSI varchar(5) NOT NULL,
        planCode varchar(6) NOT NULL,
        roaming_Region varchar(50) NOT NULL,
        mobile_Service varchar(50) NOT NULL,
        roaming_Profile varchar(20) NOT NULL,
        validity_Mode varchar(255) NOT NULL,
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

// register_activation_hook(__FILE__, 'quadcell_create_plancode_table');
function add_plan_code()
{


    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_plancode';

    $data = array(
        'applicable_IMSI' => sanitize_text_field($_POST['applicable_IMSI']),
        'planCode' => sanitize_text_field($_POST['planCode']),
        'roaming_Region' => sanitize_text_field($_POST['roaming_Region']),
        'mobile_Service' => sanitize_text_field($_POST['mobile_Service']),
        'roaming_Profile' => sanitize_text_field($_POST['roaming_Profile']),
        'validity_Mode' => sanitize_text_field($_POST['validity_Mode']),

    );

    $format = array('%s', '%s', '%s', '%s', '%s', '%s');

    if ($wpdb->insert($table_name, $data, $format)) {
        $data['id'] = $wpdb->insert_id;
        wp_send_json_success(array('message' => 'Plancode added successfully', 'plancode' => $data));
    } else {
        wp_send_json_error(array('message' => 'Failed to add plancode '), 500);
    }
}
function delete_plan_code()
{


    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_plancode';

    $id = intval($_POST['id']);

    if ($wpdb->delete($table_name, array('id' => $id), array('%d'))) {
        wp_send_json_success(array('message' => 'Plan code deleted successfully'));
    } else {
        wp_send_json_error(array('message' => 'Failed to delete Plan code '), 500);
    }
}

function load_plan_code()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_plancode';
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if ($results) {
        ob_start();
        foreach ($results as $plancode) {
            echo '<tr data-id="' . esc_attr($plancode['id']) . '">';
            echo '<td>' . esc_html($plancode['id']) . '</td>';
            echo '<td>' . esc_html($plancode['applicable_IMSI']) . '</td>';
            echo '<td>' . esc_html($plancode['planCode']) . '</td>';
            echo '<td>' . esc_html($plancode['roaming_Region']) . '</td>';
            echo '<td>' . esc_html($plancode['mobile_Service']) . '</td>';
            echo '<td>' . esc_html($plancode['roaming_Profile']) . '</td>';
            echo '<td>' . esc_html($plancode['validity_Mode']) . '</td>';
            echo '<td>';
            echo '<button class="delete-plan-code-button button" data-id="' . esc_attr($plancode['id']) . '">Delete</button> ';
            echo '<button class="edit-plan-code-button button" data-id="' . esc_attr($plancode['id']) . '">Edit</button>';
            echo '</td>';
            echo '</tr>';
        }
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    } else {
        wp_send_json_error(array('message' => 'No records found'), 404);
    }
}
function update_plan_code()
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_plancode';

    $id = intval($_POST['id']);
    $data = array(
        'applicable_IMSI' => sanitize_text_field($_POST['applicable_IMSI']),
        'planCode' => sanitize_text_field($_POST['planCode']),
        'roaming_Region' => sanitize_text_field($_POST['roaming_Region']),
        'mobile_Service' => sanitize_text_field($_POST['mobile_Service']),
        'roaming_Profile' => sanitize_text_field($_POST['roaming_Profile']),
        'validity_Mode' => sanitize_text_field($_POST['validity_Mode'])
    );

    error_log('Update data: ' . print_r($data, true)); // Log received data

    $where = array('id' => $id);
    $format = array('%s', '%s', '%s', '%s', '%s', '%s');
    // $where_format = array('%d');

    if ($wpdb->update($table_name, $data, $where, $format)) {
        wp_send_json_success(array('message' => 'Plan code updated successfully', 'sim_record' => $data));
    } else {
        wp_send_json_error(array('message' => 'Failed to update Plan code'), 500);
    }
}

function quadcell_api_plan_code_section()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_plancode';

    // Fetch all plan codes
    $plan_codes = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    ?>
    <div class="wrap">
        <h1>Plan Code Management</h1>

        <!-- Form to add a new plan code -->
        <form id="add-plan-code-form">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Applicable IMSI</th>
                    <td><input type="text" id="applicable_IMSI" name="applicable_IMSI" value="" maxlength="5" type="number"
                            required />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Plan Code</th>
                    <td><input type="text" id="planCode" name="planCode" value="" required maxlength="6" required />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Roaming Region</th>
                    <td><input type="text" id="roaming_Region" name="roaming_Region" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Mobile Service</th>
                    <td><input type="text" id="mobile_Service" name="mobile_Service" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Roaming Profile</th>
                    <td><input type="text" id="roaming_Profile" name="roaming_Profile" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Validity Mode</th>
                    <td><input type="text" id="validity_Mode" name="validity_Mode" value="" required /></td>
                </tr>
            </table>
            <button type="button" id="add-plan-code-button" class="button button-primary">Add Plan Code</button>
            <button type="button" id="update-plan-code-button" class="button button-primary" style="display:none;">Update
                Plan code</button>
            <button type="button" id="cancel-edit-button" class="button" style="display:none;">Cancel</button>
        </form>

        <!-- Table to display all plan codes -->
        <h2>Existing Plan Codes</h2>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Applicable IMSI</th>
                    <th>Plan Code</th>
                    <th>Roaming Region</th>
                    <th>Mobile Service</th>
                    <th>Roaming Profile</th>
                    <th>Validity Mode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="plan-codes-table-body">
                <?php foreach ($plan_codes as $plan_code): ?>
                    <tr>
                        <td><?php echo esc_html($plan_code['id']); ?></td>
                        <td><?php echo esc_html($plan_code['applicable_IMSI']); ?></td>
                        <td><?php echo esc_html($plan_code['planCode']); ?></td>
                        <td><?php echo esc_html($plan_code['roaming_Region']); ?></td>
                        <td><?php echo esc_html($plan_code['mobile_Service']); ?></td>
                        <td><?php echo esc_html($plan_code['roaming_Profile']); ?></td>
                        <td><?php echo esc_html($plan_code['validity_Mode']); ?></td>
                        <td>
                            <button class="delete-plan-code-button button"
                                data-id="<?php echo esc_attr($plan_code['id']); ?>">Delete</button>
                            <button class="edit-plan-code-button button"
                                data-id="<?php echo esc_attr($plan_code['id']); ?>">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}



// Enqueue scripts
function quadcell_plan_code_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('quadcell-plan-code', plugin_dir_url(__FILE__) . 'quadcell-plan-code.js', array('jquery'), null, true);
    wp_localize_script('quadcell-plan-code', 'quadcellPlanCode', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('quadcell_plan_code_nonce'),
    ));
}
add_action('admin_enqueue_scripts', 'quadcell_plan_code_scripts');
?>
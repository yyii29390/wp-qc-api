<?php
// Ensure this file is only accessed via WordPress
defined('ABSPATH') or die('No script kiddies please!');

// Define your AJAX actions
add_action('wp_ajax_add_sim_record', 'add_sim_record');
add_action('wp_ajax_delete_sim_record', 'delete_sim_record');
add_action('wp_ajax_update_sim_record', 'update_sim_record');
add_action('wp_ajax_import_sim_records', 'import_sim_records');
add_action('wp_ajax_export_sim_records', 'export_sim_records');
add_action('wp_ajax_search_sim_records', 'search_sim_records');
add_action('wp_ajax_load_sim_records', 'load_sim_records');

function quadcell_create_sim_records_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        imsi varchar(255) NOT NULL,
        iccid varchar(255) NOT NULL,
        msisdn varchar(255) NOT NULL,
        in_use boolean DEFAULT 0 NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


// function verify_nonce(): void
// {
//     if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'quadcell_sim_records_nonce')) {
//         error_log('Invalid nonce'); // Log invalid nonce error
//         wp_send_json_error(array('message' => 'Invalid nonce'), 400);
//         exit;
//     }
// }

function add_sim_record()
{
    verify_nonce('quadcell_sim_records_nonce');
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';

    $data = array(
        'imsi' => sanitize_text_field($_POST['imsi']),
        'iccid' => sanitize_text_field($_POST['iccid']),
        'msisdn' => sanitize_text_field($_POST['msisdn']),
        'in_use' => intval($_POST['in_use'])
    );

    $format = array('%s', '%s', '%s', '%d');

    if ($wpdb->insert($table_name, $data, $format)) {
        $data['id'] = $wpdb->insert_id;
        wp_send_json_success(array('message' => 'SIM record added successfully', 'sim_record' => $data));
    } else {
        wp_send_json_error(array('message' => 'Failed to add SIM record'), 500);
    }
}

function delete_sim_record()
{
    verify_nonce('quadcell_sim_records_nonce');
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';

    $id = intval($_POST['id']);

    if ($wpdb->delete($table_name, array('id' => $id), array('%d'))) {
        wp_send_json_success(array('message' => 'SIM record deleted successfully'));
    } else {
        wp_send_json_error(array('message' => 'Failed to delete SIM record'), 500);
    }
}

function update_sim_record()
{
    verify_nonce('quadcell_sim_records_nonce');
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';

    $id = intval($_POST['id']);
    $data = array(
        'imsi' => sanitize_text_field($_POST['imsi']),
        'iccid' => sanitize_text_field($_POST['iccid']),
        'msisdn' => sanitize_text_field($_POST['msisdn']),
        'in_use' => intval($_POST['in_use'])
    );

    error_log('Update data: ' . print_r($data, true)); // Log received data

    $where = array('id' => $id);
    $format = array('%s', '%s', '%s', '%d');
    $where_format = array('%d');

    if ($wpdb->update($table_name, $data, $where, $format, $where_format)) {
        wp_send_json_success(array('message' => 'SIM record updated successfully', 'sim_record' => $data));
    } else {
        wp_send_json_error(array('message' => 'Failed to update SIM record'), 500);
    }
}

function import_sim_records()
{
    verify_nonce('quadcell_sim_records_nonce');
    if (isset($_FILES['import_file'])) {
        $file = $_FILES['import_file']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'qc_sim_records';

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $record = array(
                    'imsi' => sanitize_text_field($data[0]),
                    'iccid' => sanitize_text_field($data[1]),
                    'msisdn' => sanitize_text_field($data[2]),
                    'in_use' => intval($data[3])
                );

                $wpdb->insert($table_name, $record);
            }

            fclose($handle);
            wp_send_json_success(array('message' => 'SIM records imported successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to open the file'), 500);
        }
    } else {
        wp_send_json_error(array('message' => 'No file uploaded'), 400);
    }
}

function export_sim_records()
{
    verify_nonce('quadcell_sim_records_nonce');
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';
    $records = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if ($records) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=sim_records.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('IMSI', 'ICCID', 'MSISDN', 'In Use'));

        foreach ($records as $record) {
            fputcsv($output, $record);
        }

        fclose($output);
        exit;
    } else {
        wp_send_json_error(array('message' => 'No records found'), 404);
    }
}

function search_sim_records()
{
    verify_nonce('quadcell_sim_records_nonce');
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';

    $query = "SELECT * FROM $table_name WHERE 1=1";
    if (!empty($_GET['imsi'])) {
        $query .= $wpdb->prepare(" AND imsi LIKE %s", '%' . $wpdb->esc_like($_GET['imsi']) . '%');
    }
    if (!empty($_GET['iccid'])) {
        $query .= $wpdb->prepare(" AND iccid LIKE %s", '%' . $wpdb->esc_like($_GET['iccid']) . '%');
    }
    if (!empty($_GET['msisdn'])) {
        $query .= $wpdb->prepare(" AND msisdn LIKE %s", '%' . $wpdb->esc_like($_GET['msisdn']) . '%');
    }

    $results = $wpdb->get_results($query, ARRAY_A);

    if ($results) {
        ob_start();
        foreach ($results as $record) {
            echo '<tr data-id="' . esc_attr($record['id']) . '">';
            echo '<td>' . esc_html($record['imsi']) . '</td>';
            echo '<td>' . esc_html($record['iccid']) . '</td>';
            echo '<td>' . esc_html($record['msisdn']) . '</td>';
            echo '<td>' . ($record['in_use'] ? 'Yes' : 'No') . '</td>';
            echo '<td>';
            echo '<button class="delete-sim-record-button button" data-id="' . esc_attr($record['id']) . '">Delete</button> ';
            echo '<button class="edit-sim-record-button button" data-id="' . esc_attr($record['id']) . '">Edit</button>';
            echo '</td>';
            echo '</tr>';
        }
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    } else {
        wp_send_json_error(array('message' => 'No records found'), 404);
    }
}

function load_sim_records()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if ($results) {
        ob_start();
        foreach ($results as $record) {
            echo '<tr data-id="' . esc_attr($record['id']) . '">';
            echo '<td>' . esc_html($record['id']) . '</td>';
            echo '<td>' . esc_html($record['imsi']) . '</td>';
            echo '<td>' . esc_html($record['iccid']) . '</td>';
            echo '<td>' . esc_html($record['msisdn']) . '</td>';
            echo '<td>' . ($record['in_use'] ? 'Yes' : 'No') . '</td>';
            echo '<td>';
            echo '<button class="delete-sim-record-button button" data-id="' . esc_attr($record['id']) . '">Delete</button> ';
            echo '<button class="edit-sim-record-button button" data-id="' . esc_attr($record['id']) . '">Edit</button>';
            echo '</td>';
            echo '</tr>';
        }
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    } else {
        wp_send_json_error(array('message' => 'No records found'), 404);
    }
}

function quadcell_api_sim_records_section()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';

    // Fetch all SIM records
    $sim_records = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    ?>
<div class="wrap">
    <h1>SIM Record Management</h1>

    <!-- Form to add a new SIM record -->
    <form id="add-sim-record-form">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">IMSI</th>
                <td><input type="text" id="imsi" name="imsi" value="" required /></td>
            </tr>
            <tr valign="top">
                <th scope="row">ICCID</th>
                <td><input type="text" id="iccid" name="iccid" value="" required /></td>
            </tr>
            <tr valign="top">
                <th scope="row">MSISDN</th>
                <td><input type="text" id="msisdn" name="msisdn" value="" required /></td>
            </tr>
            <tr valign="top">
                <th scope="row">In Use</th>
                <td><input type="checkbox" id="in_use" name="in_use" /></td>
            </tr>
        </table>
        <button type="button" id="add-sim-record-button" class="button button-primary">Add SIM Record</button>
        <button type="button" id="update-sim-record-button" class="button button-primary" style="display:none;">Update
            SIM Record</button>
        <button type="button" id="cancel-edit-button" class="button" style="display:none;">Cancel</button>
    </form>

    <!-- Table to display all SIM records -->
    <h2>Existing SIM Records</h2>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>IMSI</th>
                <th>ICCID</th>
                <th>MSISDN</th>
                <th>In Use</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="sim-records-table-body">
            <?php foreach ($sim_records as $sim_record): ?>
            <tr>
                <td><?php echo esc_html($sim_record['id']); ?></td>
                <td><?php echo esc_html($sim_record['imsi']); ?></td>
                <td><?php echo esc_html($sim_record['iccid']); ?></td>
                <td><?php echo esc_html($sim_record['msisdn']); ?></td>
                <td><?php echo $sim_record['in_use'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <button class="delete-sim-record-button button"
                        data-id="<?php echo esc_attr($sim_record['id']); ?>">Delete</button>
                    <button class="edit-sim-record-button button"
                        data-id="<?php echo esc_attr($sim_record['id']); ?>">Edit</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
}


// Enqueue scripts
function quadcell_sim_records_scripts()
{
    wp_enqueue_script('jquery');
    // var_dump("test");
    // die();
    wp_enqueue_script('quadcell-sim-records', plugin_dir_url(__FILE__) . 'quadcell-sim-records.js', array('jquery'), null, true);
    wp_localize_script('quadcell-sim-records', 'quadcellSimRecords', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('quadcell_sim_records_nonce'),
    ));
}
add_action('admin_enqueue_scripts', 'quadcell_sim_records_scripts');
?>
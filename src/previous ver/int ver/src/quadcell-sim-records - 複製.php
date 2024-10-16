<?php
function quadcell_api_sim_records_section() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';
    $sim_records = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    
    echo '<div class="wrap">';
    echo '<h1>SIM Records</h1>';
    echo '<form id="sim-record-form">';
    echo '<input type="hidden" id="record_id" name="record_id" value="">';
    echo '<table class="form-table">';
    echo '<tr><th scope="row"><label for="imsi">IMSI</label></th><td><input type="text" id="imsi" name="imsi" required></td></tr>';
    echo '<tr><th scope="row"><label for="iccid">ICCID</label></th><td><input type="text" id="iccid" name="iccid" required></td></tr>';
    echo '<tr><th scope="row"><label for="msisdn">MSISDN</label></th><td><input type="text" id="msisdn" name="msisdn"></td></tr>';
    echo '<tr><th scope="row"><label for="in_use">In Use</label></th><td><input type="checkbox" id="in_use" name="in_use"></td></tr>';
    echo '</table>';
    echo '<button type="button" id="add-sim-record-button" class="button button-primary">Add SIM Record</button>';
    echo '</form>';
    echo '<h2>Existing SIM Records</h2>';
    echo '<table class="wp-list-table widefat fixed striped table-view-list">';
    echo '<thead><tr><th>ID</th><th>IMSI</th><th>ICCID</th><th>MSISDN</th><th>In Use</th><th>Actions</th></tr></thead>';
    echo '<tbody id="sim-records-table-body">';
    foreach ($sim_records as $record) {
        echo '<tr data-id="' . esc_attr($record['id']) . '">';
        echo '<td>' . esc_html($record['id']) . '</td>';
        echo '<td>' . esc_html($record['imsi']) . '</td>';
        echo '<td>' . esc_html($record['iccid']) . '</td>';
        echo '<td>' . esc_html($record['msisdn']) . '</td>';
        echo '<td>' . ($record['in_use'] ? 'Yes' : 'No') . '</td>';
        echo '<td>';
        echo '<button class="button edit-sim-record-button" data-id="' . esc_attr($record['id']) . '">Edit</button> ';
        echo '<button class="button delete-sim-record-button" data-id="' . esc_attr($record['id']) . '">Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

// AJAX handler to add, edit, and delete SIM records
add_action('wp_ajax_add_sim_record', 'quadcell_add_sim_record');
add_action('wp_ajax_edit_sim_record', 'quadcell_edit_sim_record');
add_action('wp_ajax_delete_sim_record', 'quadcell_delete_sim_record');

function quadcell_add_sim_record() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';
    $imsi = sanitize_text_field($_POST['imsi']);
    $iccid = sanitize_text_field($_POST['iccid']);
    $msisdn = sanitize_text_field($_POST['msisdn']);
    $in_use = isset($_POST['in_use']) ? 1 : 0;

    $wpdb->insert($table_name, compact('imsi', 'iccid', 'msisdn', 'in_use'));

    wp_send_json_success(['sim_record' => $wpdb->get_row("SELECT * FROM $table_name WHERE id = " . $wpdb->insert_id, ARRAY_A)]);
}

function quadcell_edit_sim_record() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';
    $id = absint($_POST['id']);
    $imsi = sanitize_text_field($_POST['imsi']);
    $iccid = sanitize_text_field($_POST['iccid']);
    $msisdn = sanitize_text_field($_POST['msisdn']);
    $in_use = isset($_POST['in_use']) ? 1 : 0;

    $wpdb->update($table_name, compact('imsi', 'iccid', 'msisdn', 'in_use'), ['id' => $id]);

    wp_send_json_success(['sim_record' => $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id", ARRAY_A)]);
}

function quadcell_delete_sim_record() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_sim_records';
    $id = absint($_POST['id']);
    
    $wpdb->delete($table_name, ['id' => $id]);

    wp_send_json_success();
}

// Enqueue scripts
function quadcell_sim_records_scripts() {
	wp_enqueue_script('jquery');
    wp_enqueue_script('quadcell-sim-records', plugin_dir_url(__FILE__) . 'quadcell-sim-records.js', array('jquery'), null, true);

    wp_localize_script('quadcell-sim-records', 'quadcellSimRecords', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('admin_enqueue_scripts', 'quadcell_sim_records_scripts');

?>

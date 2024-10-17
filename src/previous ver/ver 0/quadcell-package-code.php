<?php
// Ensure this file is only accessed via WordPress
defined('ABSPATH') or die('No script kiddies please!');

// Define your AJAX actions
add_action('wp_ajax_prepare_items', 'prepare_items');
add_action('wp_ajax_get_bulk_actions', 'get_bulk_actions');
add_action('wp_ajax_process_bulk_action', 'process_bulk_action');
add_action('wp_ajax_add_package_code', 'add_package_code');

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Package_Code_List_Table extends WP_List_Table {

    function __construct() {
        parent::__construct(array(
            'singular' => 'Package Code',
            'plural'   => 'Package Codes',
            'ajax'     => false
        ));
    }

    function get_columns() {
        return array(
            'cb'                => '<input type="checkbox" />',
            'applicable_IMSI'   => 'Applicable IMSI',
            'packCode'          => 'Package Code',
            'roaming_Profile'   => 'Roaming Profile',
        );
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="package_code[]" value="%s" />', $item['id']);
    }

    function column_default($item, $column_name) {
        return $item[$column_name];
    }

    function get_sortable_columns() {
        return array(
            'applicable_IMSI'   => array('applicable_IMSI', false),
            'packCode'          => array('packCode', false),
            'roaming_Profile'   => array('roaming_Profile', false),
        );
    }

    function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'qc_package_code';

        $per_page = 10;
        $current_page = $this->get_pagenum();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));

        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name LIMIT %d OFFSET %d", $per_page, ($current_page - 1) * $per_page), ARRAY_A);
    }

    function get_bulk_actions() {
        return array(
            'delete' => 'Delete'
        );
    }

    function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'qc_package_code';
            $ids = isset($_REQUEST['package_code']) ? $_REQUEST['package_code'] : array();

            if (is_array($ids)) {
                $ids = implode(',', array_map('intval', $ids));
            }

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }
}

function quadcell_api_package_code_section() {
    $package_code_list_table = new Package_Code_List_Table();
    $package_code_list_table->prepare_items();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Package Code Management</h1>
        <a href="#" class="page-title-action" id="add-new-package-code">Add New</a>
        <form id="add-package-code-form" style="display:none;">
            <table class="form-table">
                <tr>
                    <th><label for="applicable_IMSI">Applicable IMSI</label></th>
                    <td><input type="text" id="applicable_IMSI" name="applicable_IMSI" required /></td>
                </tr>
                <tr>
                    <th><label for="packCode">Package Code</label></th>
                    <td><input type="text" id="packCode" name="packCode" required /></td>
                </tr>
                <tr>
                    <th><label for="roaming_Profile">Roaming Profile</label></th>
                    <td><input type="text" id="roaming_Profile" name="roaming_Profile" required /></td>
                </tr>
            </table>
            <button type="submit" class="button button-primary">Save</button>
            <button type="button" class="button" id="cancel-add-package-code">Cancel</button>
        </form>
        <form method="post">
            <input type="hidden" name="page" value="package_code_management" />
            <?php
            $package_code_list_table->search_box('search', 'search_id');
            $package_code_list_table->display();
            ?>
        </form>
    </div>
    <?php
}

function create_qc_package_code_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_package_code';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        applicable_IMSI varchar(20) NOT NULL,
        packCode varchar(20) NOT NULL,
        roaming_Profile varchar(20) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_qc_package_code_table');

function add_package_code() {
    check_ajax_referer('add_package_code_nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_package_code';

    $data = array(
        'applicable_IMSI' => sanitize_text_field($_POST['applicable_IMSI']),
        'packCode'        => sanitize_text_field($_POST['packCode']),
        'roaming_Profile' => sanitize_text_field($_POST['roaming_Profile'])
    );

    $format = array('%s', '%s', '%s');

    if ($wpdb->insert($table_name, $data, $format)) {
        $data['id'] = $wpdb->insert_id;
        wp_send_json_success(array('message' => 'Package code added successfully', '' => $data));
    } else {
        wp_send_json_error(array('message' => 'Failed to add package code'));
    }
}
add_action('wp_ajax_add_package_code', 'add_package_code');


function quadcell_package_code_enqueue_scripts() {
    wp_enqueue_script('quadcell-package-code-js', plugin_dir_url(__FILE__) . 'quadcell-package-code.js', array('jquery'), null, true);
    wp_localize_script('quadcell-package-code-js', 'quadcellPackageCode', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('add_package_code_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'quadcell_package_code_enqueue_scripts');

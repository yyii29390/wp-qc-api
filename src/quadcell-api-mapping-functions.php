<?php
require __DIR__ . '/../../WooCommerce/vendor/autoload.php';

// use Automattic\WooCommerce\Client;
// global $woocommerce;
// $woocommerce = new Client(
//     'http://localhost',
//     'ck_51147d942897ee8f1b1c0b3d022db22b0fde5318',
//     'cs_1034b1c72d241c7888fcfadb238892f2ed385a2f',
//     [
//         'wp_api' => true,
//         'version' => 'wc/v3'
//     ]
// );
// print_r($woocommerce->get('products'));

// $endpoint= "http://localhost/wp-json/wc/v3/products";


$ch = curl_init();
$url = "http://localhost/wp-json/wc/v3/products";

// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_HTTPGET, 1);
// curl_setopt($ch, CURLOPT_USERPWD, "ck_c052794d681d572c1a0bb7d3c52ffd28ab815d1a" . ":" . "cs_c3e7a8e9a34d731f09ba7f85bd906c660cfd856f");
// this will handle gzip content
// $result = curl_exec($ch);
// curl_close($ch);
// print $result;





// Create the API mapping table upon plugin activation
function create_qc_api_mapping_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_api_mappings';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        profile_name varchar(100) NOT NULL,
        api_command varchar(50) NOT NULL,
        parameters longtext NOT NULL,
        sequence int(11) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_qc_api_mapping_table');

// Function to display the API mapping section
function quadcell_api_plan_to_api_section()
{
    global $wpdb, $api_commands;

    // $plan_codes_table = $wpdb->prefix . 'qc_plancode';
    $api_mappings_table = $wpdb->prefix . 'qc_api_mappings';
    // $plan_codes = $wpdb->get_results("SELECT * FROM $plan_codes_table", ARRAY_A);
    $profiles = $wpdb->get_results("SELECT DISTINCT profile_name FROM $api_mappings_table", ARRAY_A);

    // Only set the initial selected profile if it is not already set by the user's choice
    $selected_profile = isset($_POST['selected_profile']) ? sanitize_text_field($_POST['selected_profile']) : (!empty($profiles) ? $profiles[0]['profile_name'] : '');

    ?>
    <h3>Manage API Command Profiles</h3>
    <form method="post" id="quadcell-api-profile-form">
        <label for="profile_name">Profile Name:</label>
        <select name="selected_profile" id="selected_profile">
            <option value="" selected>-</option> <!-- Default blank option -->
            <?php foreach ($profiles as $profile): ?>
                <option value="<?php echo esc_attr($profile['profile_name']); ?>">

                    <?php echo esc_html($profile['profile_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="button" id="add-new-profile">Add New Profile</button>
    </form>

    <div id="quadcell-api-plan-to-api-mappings-container">
        <form method="post" id="quadcell-api-mappings-form">
            <!-- Table structure for API mappings -->
            <table class="form-table" id="quadcell-api-plan-to-api-mappings-table">
                <thead>
                    <tr valign="top">
                        <th scope="row">API Command</th>
                        <th scope="row">Parameters</th>
                        <th scope="row" width="10px">Sequence</th>
                        <th scope="row">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamically filled using JavaScript -->
                </tbody>
            </table>
            <button type="button" class="button" id="add-plan-to-api-mapping">Add Mapping</button>
            <input type="submit" class="button button-primary" value="Save Mappings">
        </form>
    </div>
    <?php
}
// Handle AJAX request to fetch package code information
function fetch_package_code_info()
{
    check_ajax_referer('fetch_package_code_info_nonce', 'nonce');

    global $wpdb;
    $package_code = sanitize_text_field($_POST['package_code']);
    $table_name = $wpdb->prefix . 'qc_package_code';
    $package_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE package_code = %s", $package_code), ARRAY_A);

    if ($package_info) {
        wp_send_json_success(['$pack_info' => $package_info]);
    } else {
        wp_send_json_error(['message' => 'Plan code not found.']);
    }
}
add_action('wp_ajax_fetch_package_code_info', 'fetch_package_code_info');
// Handle AJAX request to fetch plan code information
function fetch_plan_code_info()
{
    check_ajax_referer('fetch_plan_code_info_nonce', 'nonce');

    global $wpdb;
    $plan_code = sanitize_text_field($_POST['plan_code']);
    $table_name = $wpdb->prefix . 'qc_plancode';
    $plan_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE planCode = %s", $plan_code), ARRAY_A);

    if ($plan_info) {
        wp_send_json_success(['plan_info' => $plan_info]);
    } else {
        wp_send_json_error(['message' => 'Plan code not found.']);
    }
}
add_action('wp_ajax_fetch_plan_code_info', 'fetch_plan_code_info');

// Handle AJAX request to add a new profile
function add_new_profile()
{
    check_ajax_referer('fetch_plan_code_info_nonce', 'nonce');

    global $wpdb;
    $profile_name = sanitize_text_field($_POST['profile_name']);

    // Check if the profile already exists
    $existing_profile = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}qc_api_mappings WHERE profile_name = %s",
        $profile_name
    ));

    if ($existing_profile) {
        wp_send_json_error(['message' => 'Profile already exists.']);
    } else {
        // Insert a dummy entry to create the profile
        $wpdb->insert(
            "{$wpdb->prefix}qc_api_mappings",
            ['profile_name' => $profile_name, 'api_command' => '', 'parameters' => '', 'sequence' => 0],
            ['%s', '%s', '%s', '%d']
        );

        wp_send_json_success(['message' => 'Profile created successfully.']);
    }
}
add_action('wp_ajax_add_new_profile', 'add_new_profile');

// Handle AJAX request to load profile mappings
function load_profile_mappings()
{
    check_ajax_referer('fetch_plan_code_info_nonce', 'nonce');

    global $wpdb;
    $profile_name = sanitize_text_field($_POST['profile_name']);
    $table_name = $wpdb->prefix . 'qc_api_mappings';

    // Debugging: Check the profile name being queried
    error_log("Loading mappings for profile: " . $profile_name);

    $mappings = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE profile_name = %s ORDER BY sequence ASC",
        $profile_name
    ), ARRAY_A);

    if ($mappings) {
        // Debugging: Output the retrieved mappings
        error_log("Retrieved mappings: " . print_r($mappings, true));
        wp_send_json_success(['mappings' => $mappings]);

    } else {
        error_log("No mappings found for this profile.");
        wp_send_json_error(['message' => 'No mappings found for this profile.']);
    }
}
add_action('wp_ajax_load_profile_mappings', 'load_profile_mappings');

// Handle AJAX request to save API mappings
function save_api_mappings()
{
    check_ajax_referer('fetch_plan_code_info_nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_api_mappings';

    // Decode the JSON data from JavaScript
    $mappings_data = json_decode(stripslashes($_POST['data']), associative: true);


    // Check for 'selected_profile'

    $selected_profile = !empty($mappings_data['selected_profile']) ? sanitize_text_field($mappings_data['selected_profile']) : '';

    // Debugging: Check what data is being received
    error_log("Saving mappings for profile: " . $selected_profile);
    error_log("Mappings data: " . print_r($mappings_data, true));

    // // Clear existing mappings for the selected profile
    $wpdb->delete($table_name, array('profile_name' => $selected_profile));

    // Extract mappings from data
    $api_mappings = isset($mappings_data['api_mappings']) ? $mappings_data['api_mappings'] : array();

    foreach ($api_mappings as $index => $mapping) {
        $api_command = isset($mapping['api_command']) ? $mapping['api_command'] : '';
        // $plan_code = isset($mapping['plan_code']) ? sanitize_text_field($mapping['plan_code']) : '';

        // Debugging: Check parameters before serialization
        error_log("Parameters before serialization: " . print_r($mapping['parameters'], true));

        // Ensure the parameters are an array before serializing
        $jsonParameters = isset($mapping['parameters']) && is_array($mapping['parameters']) ? $mapping['parameters'] : maybe_serialize([]);
        ;
        $parameters = json_encode($jsonParameters);

        $sequence = isset($mapping['sequence']) ? intval($mapping['sequence']) : ($index + 1); // Ensure sequence starts from 1

        if (!empty($api_command) && !empty($selected_profile)) { // Ensure profile name is provided
            $wpdb->insert(
                $table_name,
                array(
                    'profile_name' => $selected_profile,
                    'api_command' => $api_command,
                    'parameters' => $parameters,
                    'sequence' => $sequence,
                ),
                array('%s', '%s', '%s', '%d')
            );
        }
    }

    // Debugging: Check if the data was saved correctly
    $saved_mappings = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE profile_name = %s ORDER BY sequence ASC",
        $selected_profile
    ), ARRAY_A);
    error_log("Saved mappings for profile: " . print_r($saved_mappings, true));

    wp_send_json_success(['message' => 'Mappings saved successfully.']);
}
add_action('wp_ajax_save_api_mappings', 'save_api_mappings');
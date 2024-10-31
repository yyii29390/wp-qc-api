<?pHp
// Ensure this file is only accessed via WordPress
defined('ABSPATH') or die('No script kiddies please!');
add_action('wp_ajax_add_product_mapping', 'add_product_mapping');

function quadcell_create_product_map_table()
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_product_map';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_name varchar(100) NOT NULL,
        profile_name varchar(100) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $ch = curl_init();
    $url = "http://localhost/wp-json/wc/v3/products";

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
// add_action('init', 'load_product');
function load_product()
{

    // if (!class_exists('WooCommerce')) {
    //     return 'WooCommerce is not active.';
    // }

    // $curl = curl_init();
    // $consumer_key = "ck_e18431e50352f8c5920ebbc2608f39882b954baf";
    // $consumer_secret = "cs_811ca38c18c06fabfd5f3422c15e438f9381540d";

    // curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'https://localhost/wordpress/wp-json/wc/v3/products',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
    //     CURLOPT_USERPWD => $consumer_key . ':' . $consumer_secret,
    // ));

    // $response = curl_exec($curl);

    // if (curl_errno($curl)) {
    //     error_log('cURL error: ' . curl_error($curl));
    //     curl_close($curl);
    //     return;
    // }
    // curl_close($curl);
    // $products = json_decode($response, true);
    // var_dump($products);
    // var_dump(1234);



    // if (!class_exists('WooCommerce')) {
    //     return 'WooCommerce is not active.';
    // }
    // $consumer_key = ck_e18431e50352f8c5920ebbc2608f39882b954baf;
    // $consumer_secret = cs_811ca38c18c06fabfd5f3422c15e438f9381540d;

    // $curl = curl_init();
    // curl_setopt($curl, CURLOPT_URL, 'https://localhost/wordpress/wp-json/wc/v3/products');
    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($curl, CURLOPT_ENCODING, '');
    // curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    // curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    // curl_setopt($curl, CURLOPT_HTTPHEADE, array(
    //     'Authorization' => 'Basic ' . base64_encode($consumer_key . ':' . $consumer_secret)
    // ), );
    // curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'https://localhost/wordpress/wp-json/wc/v3/products',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => 'GET',
    //     CURLOPT_HTTPHEADER => array(
    //         'Authorization' => 'Basic ' . base64_encode($consumer_key . ':' . $consumer_secret)
    //     ),
    // ));

    // Retrieve all products from WooCommerce





    // curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'https://localhost/wordpress/wp-json/wc/v3',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => 'GET',
    // ));



    // $curl = curl_init();

    // curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'https://localhost/wordpress/wp-json/wc/v3/products',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// CURLOPT_HTTPGET => 1,
    //     CURLOPT_HTTPHEADER => array(
    //         'Authorization: Basic Y2tfZTE4NDMxZTUwMzUyZjhjNTkyMGViYmMyNjA4ZjM5ODgyYjk1NGJhZjpjc184MTFjYTM4YzE4YzA2ZmFiZmQ1ZjM0MjJjMTVlNDM4ZjkzODE1NDBk'
    //     ),
    // ));

    // $response = curl_exec($curl);

    // curl_close($curl);
    // echo $response;
    // $url = 'https://localhost/wordpress/wp-json/wc/v3/products123';


    // $response = wp_remote_get($url, array(
    //     'headers' => array(
    //         'Authorization: Basic Y2tfZTE4NDMxZTUwMzUyZjhjNTkyMGViYmMyNjA4ZjM5ODgyYjk1NGJhZjpjc184MTFjYTM4YzE4YzA2ZmFiZmQ1ZjM0MjJjMTVlNDM4ZjkzODE1NDBk'
    //     )
    // ));

    // if (is_wp_error($response)) {
    //     error_log('Error fetching products: ' . $response->get_error_message());
    //     return;
    // }

    // $products = json_decode(wp_remote_retrieve_body($response), true);

    // if (!empty($products)) {
    //     foreach ($products as $product) {
    //         echo esc_html($product['name']) . '<br>';
    //     }
    // }



    // $response = curl_exec($curl);

    // curl_close($curl);
    // $products = json_decode($response, true);
    // echo $products;
    // var_dump($products);
    // die();
    // if ($products) {
    //     ob_start();
    //     foreach ($products as $product) {
    //         echo '<option>' . esc_attr($product['name']) . '</option>';

    //     }
    //     $html = ob_get_clean();
    //     wp_send_json_success(array('html' => $html));
    // } else {
    //     wp_send_json_error(array('message' => 'No records found'), 404);
    // }
}
// function add_product_mapping()
// {
//     if (isset($_POST['submit'])) {
//         global $wpdb;
//         $table_name = $wpdb->prefix . 'qc_product_map';
//         $data = array(
//             '$product' => sanitize_text_field($_POST['product']),
//             '$profile_id' => sanitize_text_field($_POST['profile_id'])
//         );

//         $format = array('%s', '%d');
//         if ($wpdb->insert($table_name, $data, $format)) {
//             wp_send_json_success(['message' => 'Mappings saved successfully.']);
//         } else {
//             wp_send_json_success(['message' => $wpdb + "- insert error"]);
//         }
//     }
// }
// ;







// add_action('wp_ajax_nopriv_load_product', 'load_product');
function save_product_mapping()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_product_map';
    $data = array(
        'product_name' => sanitize_text_field($_POST['product_name']),
        'profile_name' => sanitize_text_field($_POST['profile_name'])
    );
    $format = array('%s', '%s');
    if ($wpdb->insert($table_name, $data, $format)) {
        wp_send_json_success(['message' => 'Mappings saved successfully.']);
    } else {
        wp_send_json_success(['message' => $wpdb + "- insert error"]);
    }
}
add_action('wp_ajax_save_product_mapping', 'save_product_mapping');

function load_product_mapping()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_product_map';
    $jsonResults = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if ($jsonResults) {
        ob_start();
        foreach ($jsonResults as $result) {

            echo '<tr id="product-map-' . esc_attr($result['id']) . '">';
            echo '<td id="' . esc_attr($result['id']) . '">' . esc_attr($result['id']) . '</td>';
            echo '<td id="' . esc_attr($result['product_name']) . '">' . esc_attr($result['product_name']) . '</td>';
            echo '<td id="' . esc_attr($result['profile_name']) . '">' . esc_attr($result['profile_name']) . '</td>';

            echo '<td><input type="submit" id="edit-product-map-' . esc_attr($result['id']) . '" class="button button-primary" value="Edit">
                <button id="del-product-map-' . esc_attr($result['id']) . '"  class="button button-primary">delete</button>
            </td>
            </tr>';

        }
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    } else {
        // Handle case where no results are found

        wp_send_json_error(array('message' => 'No records found'), 404);

    }

}
add_action('wp_ajax_load_product_mapping', 'load_product_mapping');


function quadcell_api_product_mapping_section()
{
    ?>
    </form>
    <h3>Product Mapping</h3>
    <div style="margin-bottom:10px">
        <form id="product-map-form" method="post">
            <th>
                <label for="product_name">
                    WC Product
                </label>
            </th>
            <th>
                <select name="product_name" id="product_name">
                    <option id="default-product-option" value="">-</option>
                    <?php
                    $args = array(
                        'status' => 'publish',
                    );

                    $products = wc_get_products($args);

                    foreach ($products as $product) {

                        echo '<option value="' . ($product->get_data())['id'] . "_" . ($product->get_data())['name'] . ($product->get_data())['sku'] . '">' . ($product->get_data())['id'] . "_" . ($product->get_data())['name'] . ($product->get_data())['sku'] . '</option>';
                    }
                    ?>
                </select>
            </th>

            <th>
                <label for="profile_name">
                    Profile
                </label>
            </th>
            <th>
                <select name="profile_name" id="profile_name">
                    <option id="default-profile-option">
                        -
                    </option>
                    <?php
                    global $wpdb;
                    $api_mappings_table = $wpdb->prefix . 'qc_api_mappings';
                    $profiles = $wpdb->get_results("SELECT DISTINCT profile_name FROM $api_mappings_table", ARRAY_A);
                    foreach ($profiles as $profile) {
                        echo '<option value="' . $profile['profile_name'] . '">' . $profile['profile_name'] . ($product->get_data())['sku'] . '</option>';
                    }
                    ?>
                </select>
            </th>

            <th>
                <input type="submit" class="button button-primary" value="Add Processing">
                </tr>

        </form>
    </div>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>id</th>
                <th>Product</th>
                <th>Profile</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="product-map-table-body">
            <?php ?>
        </tbody>

    </table>


    <?php
}

function quadcell_products_scripts()
{

    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'quadcell_products',
        plugin_dir_url(__FILE__) . 'quadcell-product-mapping.js',
        array('jquery'),
        null,
        true
    );
    wp_localize_script('quadcell_products', 'quadcellProducts', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('quadcell_product'),
    ));
}
add_action('admin_enqueue_scripts', 'quadcell_products_scripts')
    ?>
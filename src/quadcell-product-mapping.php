<?pHp
// Ensure this file is only accessed via WordPress
defined('ABSPATH') or die('No script kiddies please!');


function quadcell_create_product_map_table()
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'qc_product_map';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product varchar(100) NOT NULL,
        profile_id int NOT NULL,
        PRIMARY KEY  (id)
       FOREIGN KEY (profile_id) REFERENCES wp_qc_api_mappings(id)
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









// add_action('wp_ajax_nopriv_load_product', 'load_product');
function quadcell_api_product_mapping_section()
{
    ?>
    <h3>Product Mapping</h3>
    <table>

        <th>
            WC Product
        </th>
        <th>
            <select>
                <option value="">-</option>
                <?php
                $args = array(
                    'status' => 'publish',
                );

                $products = wc_get_products($args);
                foreach ($products as $product) {
                    echo '<option value="' . ($product->get_data())['id'] . '">' . ($product->get_data())['name'] . ($product->get_data())['sku'] . '</option>';
                }
                ?>
            </select>
        </th>

        <th>
            Profile
        </th>
        <th>
            <select>
                <option>
                    -
                </option>
                <?php

                ?>
            </select>
        </th>
        <th>API:</th>
        <th style="text-align:left">
            <div> 1.Addsub</div>
            <div> 2.QuerySub</div>
            <div> 3.QuerySub</div>
        <th>
            <button type="button" class="button">Add Processing</button>
        </th>
        </tr>

    </table>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>id</th>
                <th>Product</th>
                <th>Profile</th>
            </tr>
        </thead>

    </table>

    <div id="product-option">1324</div>
    <?pHp
}


// function quadcell_products_scripts()
// {

//     wp_enqueue_script('jquery');
//     // var_dump("test");
//     // die();
//     wp_enqueue_script('quadcell_products', plugin_dir_url(__FILE__) . 'quadcell-product-mapping.js', array('jquery'), null, true);
//     wp_localize_script('quadcell_products', 'quadcellProducts', array(
//         'ajax_url' => admin_url('admin-ajax.php'),
//         'nonce' => wp_create_nonce('quadcell_product'),
//     ));
// }
// add_action('admin_enqueue_scripts', 'quadcell_products_scripts')
?>
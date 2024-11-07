<?php 
// require_once plugin_dir_path( __FILE__ ) . '/encryption-functions.php';

function quadcell_api_order_processing_section()
{
    // Ensure WooCommerce is active
    // if (!class_exists('WooCommerce')) {
    //     echo '<div class="notice notice-error"><p>WooCommerce is not active. Please activate WooCommerce to use this feature.</p></div>';
    //     return;
    // }

    // // Fetch completed orders
    // $args = array(
    //     'status' => 'completed',
    //     'limit' => -1,
    // );

    // $orders = wc_get_orders($args);
    // $product_mappings = get_option('quadcell_api_product_mappings', array());
    // $product_mappings_assoc = array();

    // Convert product mappings to associative array for easier lookup
    // foreach ($product_mappings as $mapping) {
    //     $product_mappings_assoc[$mapping['product_code']] = $mapping['plan_code'];
    // }
    ?>
    <h3>Order Processing</h3>
    <form id="order-processing-form-1">
        <table class="form-table">
        <label for="orderendpoint">Endpoint</label>
        <input id="orderendpoint" name="orderendpoint" type="text" required>
            <label for="orderimsi">IMSI</label>
            <input id="orderimsi" name="orderimsi" type="text" required>
            <input id="query-form-submit" class="btn btn-primary" type="submit" value="Submit">
        </table>    
    </form>
    <input id="test"class="btn btn-primary" type="button" value="Input">
    <?php
    // Prevent direct access to the file
        // if (!defined('ABSPATH')) {
        //     exit; // Exit if accessed directly
        // }

    // Enqueue scripts


    // AJAX handler
 
    // add_action('wp_ajax_provisioning_data', 'provisioning_data_handle_ajax'); // For non-logged in users
    // add_action('wp_ajax_nopriv_provisioning_data', 'provisioning_data_handle_ajax');
    // function provisioning_data_handle_ajax()
    // {
    //     // URL of the third-party API
    //     $api_url = 'http://api.quadcell.com:8080/v2/qrysub'; // Replace with the actual API URL

    //     // Get data from AJAX request
    //     $data_to_post = $_POST['data'];
    //     // $encrypt_data = quadcell_api_encrypt($data_to_post);
    //     var_dump($data_to_post);
    //     die();
    //     // Prepare the arguments for the POST request
    //     $args = array(
    //         'body' => json_encode($data_to_post), // Encode data as JSON
    //         'headers' => array(
    //             'Content-Type' => 'application/json', // Set the content type to JSON
    //         ),
    //     );

    //     // Make the POST request
    //     $response = wp_remote_post($api_url, $args);

    //     if (is_wp_error($response)) {
    //         // wp_send_json_error('Failed to post data');
         
    //     } else {

    //    ;
    //         wp_send_json_success(json_decode($response));
    //         var_dump($response);
    //         die();
    
    //     }

    //     // wp_die(); // This is required to terminate immediately and return a proper response
    // }
    // add_action('wp_ajax_provisioning_data', 'provisioning_data_handle_ajax');
    // add_action('wp_ajax_nopriv_provisioning_data', 'provisioning_data_handle_ajax');
    // echo '<table class="widefat fixed" cellspacing="0">';
    // echo '<thead>';
    // echo '<tr>';
    // echo '<th>Order Date</th>';
    // echo '<th>Order ID</th>';
    // echo '<th>Product SKU</th>';
    // echo '<th>Quantity</th>';
    // echo '<th>Plan Code</th>';
    // echo '</tr>';
    // echo '</thead>';
    // echo '<tbody>';

    // foreach ($orders as $order) {
    //     $order_date = $order->get_date_created()->date('Y-m-d H:i:s');
    //     $order_id = $order->get_id();
    //     foreach ($order->get_items() as $item) {
    //         $product = $item->get_product();
    //         if (!$product)
    //             continue;

    //         $product_sku = $product->get_sku();
    //         $quantity = $item->get_quantity();
    //         $plan_code = isset($product_mappings_assoc[$product_sku]) ? $product_mappings_assoc[$product_sku] : 'N/A';

    //         echo '<tr>';
    //         echo '<td>' . esc_html($order_date) . '</td>';
    //         echo '<td>' . esc_html($order_id) . '</td>';
    //         echo '<td>' . esc_html($product_sku) . '</td>';
    //         echo '<td>' . esc_html($quantity) . '</td>';
    //         echo '<td>' . esc_html($plan_code) . '</td>';
    //         echo '</tr>';
    //     }
    // }

    // echo '</tbody>';
    // echo '</table>';
}
function get_decryption_key($key_index) {
    $keys = array("F24D971DA7174DA9AA0252F861447177725A02B6274A44E7","498B731F89B14501AAAE8BA77DBD57E85EA6CF6CEE914868","0C9B507A39F14363BCDE00AEE8FB95AE149A92F359AE42DE","B0F49A91EDFE4A3F9F0AB860ED1EB006A76DA99594FF445F","64167CB3F30E44D1ABF6F62C800D98C9F2E882A0004746F0");//get_option('quadcell_api_keys', array());// need to change to variable

    if ($key_index < 0 || $key_index >= count($keys)) {
        error_log("Invalid key index: $key_index");
        return false;
    }

    $key = bin2hex($keys[$key_index]);
    if (strlen($key) !== 48) {
        error_log("Invalid key length: " . strlen($key) . ". Key must be 48 hex characters (24 bytes) long.");
        return false;
    }
  
    return $key;
}

function quadcell_api_encrypt($data) {
    // Randomly select a key index
    
    $keys = array("F24D971DA7174DA9AA0252F861447177725A02B6274A44E7","498B731F89B14501AAAE8BA77DBD57E85EA6CF6CEE914868","0C9B507A39F14363BCDE00AEE8FB95AE149A92F359AE42DE","B0F49A91EDFE4A3F9F0AB860ED1EB006A76DA99594FF445F","64167CB3F30E44D1ABF6F62C800D98C9F2E882A0004746F0");//get_option('quadcell_api_keys', array());// need to change to variable

    if (empty($keys)) {
        error_log("No keys available for encryption.");
        return false;
    }
    $key_index = 3;//rand(0, count($keys) - 1);
    
    // $key = get_decryption_key($key_index);
    $key = $keys[$key_index];
    // if ($key === false) {
    //     error_log("Encryption failed: invalid key.");
    //     return false;
    // }
    
    $data = (json_decode($data,true));
   
    unset($data['authKey']);
    
    $hexData= bin2hex(json_encode($data));

    // Padding
    $blockSize = 8;
    $padSize = ($blockSize - (strlen($hexData) % $blockSize)) % $blockSize;
    $hextext = $hexData . str_repeat("FF", $padSize);
    
    // Encrypt data
   
    $cipher = openssl_encrypt($hextext, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
    $cipher = bin2hex($cipher);
   
    if ($cipher === false) {
        error_log("Encryption failed: " . openssl_error_string());
        return false;
    }
    
    // Encrypted MAC
    $lastByte = substr($cipher, -2);
    $MAC = $lastByte . str_repeat("FF", 7);
    $encryptedMAC = openssl_encrypt($MAC, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
    $encryptedMAC = bin2hex($encryptedMAC);

    if ($encryptedMAC === false) {
        error_log("MAC encryption failed: " . openssl_error_string());
        return false;
    }

    // Header
    // $header = pack('nC', strlen($hextext) + 9, $key_index + 1);
    $bodyLen = strlen($hextext) + 9;
    $key_index = $key_index + 1;
    $bodyLenWith0 = str_pad("$bodyLen",4,"0",STR_PAD_LEFT);
    
    $header =  $bodyLenWith0 . "0".$key_index;
 
    $preEncoded = $header . $cipher . $encryptedMAC;
    // var_dump($preEncoded);
    // die();
    // Result
//     var_dump("encryptedMAC : ".$encryptedMAC);
//     var_dump("cipher : ".$cipher);
//     var_dump("header : ".$header);
// var_dump("preEncoded : ".$preEncoded);
// die();
    $final = openssl_encrypt($preEncoded, 'DES-EDE3', $key, OPENSSL_RAW_DATA );
   
    $final = bin2hex($final);
    
    error_log("Final Encrypted Data: $preEncoded");

    return $preEncoded;
}


function quadcell_api_decrypt($data) {
    $header = substr($data, 0, 6);
    $key_index = hexdec(substr($header, 4, 2)) - 1;

    $key = get_decryption_key($key_index);
    if ($key === false) {
        return false;
    }

    $encrypted_body_hex = substr($data, 6, -16);
    $mac_hex = substr($data, -16);

    error_log("Header: $header");
    error_log("Encrypted Body Hex: $encrypted_body_hex");
    error_log("MAC Hex: $mac_hex");

    $encrypted_body = hex2bin($encrypted_body_hex);
    if ($encrypted_body === false) {
        error_log("Hex to bin conversion failed");
        return false;
    }
    error_log("Hex to bin: " . bin2hex($encrypted_body));

    $decrypted_body = openssl_decrypt($encrypted_body, 'DES-EDE3', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
    if ($decrypted_body === false) {
        error_log("Decryption failed: " . openssl_error_string());
        return false;
    }

    // Remove padding (strip trailing 0xFF bytes)
    $i = 1;
    if (substr($decrypted_body, -1) !== "\xFF") {
        $decrypted_body = $decrypted_body;
    } else {
        while ($i < strlen($decrypted_body)) {
            if (substr($decrypted_body, -($i + 1), 1) === "\xFF") {
                $i++;
            } else {
                break;
            }
        }
        $decrypted_body = substr($decrypted_body, 0, -$i);
    }

    error_log("Decrypted Body: $decrypted_body");

    return $decrypted_body;
}


function quadcell_api_call($endpoint, $data)
{ 
    // $auth_key = "SYtest21";//get_option('quadcell_api_auth_key');
    $url = "http://api.quadcell.com:8080/v2";//get_option('quadcell_api_url'); // need to change to variable

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
            // 'Authorization' => "Bearer $auth_key",
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

    verify_nonce('quadcell_order_processing_nonce');

    if (!isset($_POST['command']) || !isset($_POST['fields'])) {
        wp_send_json_error('Invalid request.');
        return;
    }
    ;
    $command = sanitize_text_field($_POST['command']);
    $fields = array_map('sanitize_text_field', $_POST['fields']);
    
    // Remove empty fields
    $fields = array_filter($fields, function ($value) {
        return $value !== '';
    });

    $auth_key = "SYtest21";//get_option('quadcell_api_auth_key');// need to change to variable
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
add_action('wp_ajax_quadcell_api_ajax_handler', 'quadcell_api_ajax_handler');


function quadcell_provisioning_enqueue_scripts()
{

    wp_register_script('quadcell-order-processing', plugin_dir_url(__FILE__) . 'quadcell-order-processing.js', array('jquery'), null, true);

    // Localize the script with new data
    wp_localize_script('quadcell-order-processing', 'quadcellOrderProcessing', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('quadcell_order_processing_nonce'),
    ));

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'quadcell-order-processing' );
}
add_action('admin_enqueue_scripts', 'quadcell_provisioning_enqueue_scripts');

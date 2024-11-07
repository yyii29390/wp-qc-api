<?php
function get_decryption_key($key_index) {
    $keys = get_option('quadcell_api_keys', array());

    if ($key_index < 0 || $key_index >= count($keys)) {
        error_log("Invalid key index: $key_index");
        return false;
    }

    $key = $keys[$key_index];
    if (strlen($key) !== 48) {
        error_log("Invalid key length: " . strlen($key) . ". Key must be 48 hex characters (24 bytes) long.");
        return false;
    }
    return hex2bin($key);
}

function quadcell_api_encrypt($data) {
    // Randomly select a key index

    $keys = get_option('quadcell_api_keys', array());

    if (empty($keys)) {
        error_log("No keys available for encryption.");
        return false;
    }
    $key_index = rand(0, count($keys) - 1);
    $key = get_decryption_key($key_index);

    if ($key === false) {
        error_log("Encryption failed: invalid key.");
        return false;
    }

    // Padding
    $blockSize = 8;
    $padSize = ($blockSize - (strlen($data) % $blockSize)) % $blockSize;
    $hextext = $data . str_repeat("\xFF", $padSize);

    // Encrypt data
    $cipher = openssl_encrypt($hextext, 'DES-EDE3', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
    if ($cipher === false) {
        error_log("Encryption failed: " . openssl_error_string());
        return false;
    }

    // Encrypted MAC
    $lastByte = substr($cipher, -1);
    $MAC = $lastByte . str_repeat("\xFF", 7);
    $encryptedMAC = openssl_encrypt($MAC, 'DES-EDE3', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
    if ($encryptedMAC === false) {
        error_log("MAC encryption failed: " . openssl_error_string());
        return false;
    }

    // Header
    $header = pack('nC', strlen($hextext) + 9, $key_index + 1);

    // Result
    $finalbytes = $header . $cipher . $encryptedMAC;
    $hex_string = bin2hex($finalbytes);
    error_log("Final Encrypted Data: $hex_string");

    return $hex_string;
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
?>
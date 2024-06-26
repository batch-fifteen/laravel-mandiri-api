<?php

    function getAuthSignature(){
        $timestamp = new DateTime();
        $timestamp->setTimeZone(new DateTimeZone('Asia/Jakarta'));
        $x_timestamp = $timestamp->format('c');
        // Specify private key location, passphrase, data, and hash algorithm
        $apiRelPortalPath = __DIR__.'/../../../../storage/app/certificates/API_Portal.pem'; 
        // $apiAbsPortalPath = realpath($apiRelPortalPath);
        // Password .pem file
        $password = 'a123';

        // Generate signature
        $data = '05b00320-012a-4434-be51-3135639e8e30|'.$x_timestamp;
        $rsa_algorithm = OPENSSL_ALGO_SHA256;

        // Load private key file
        $fp = fopen($apiRelPortalPath, 'r');
        $privatekey_file = fread($fp, 8192);
        fclose($fp);
        $privatekey = openssl_pkey_get_private($privatekey_file, $password);

        // Sign data
        openssl_sign($data, $signature, $privatekey, $rsa_algorithm);
        $x_signature = base64_encode($signature);
        return [$x_timestamp, $x_signature];
    }

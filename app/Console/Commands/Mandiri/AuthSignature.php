<?php

    function getAuthSignature(){
        $timestamp = new DateTime();
        $timestamp->setTimeZone(new DateTimeZone('Asia/Jakarta'));
        $x_timestamp = $timestamp->format('c');
        // Specify private key location, passphrase, data, and hash algorithm
        $apiRelPortalPath = __DIR__.'/../../../../storage/app/certificates/privatekey.pem'; 
        // $apiAbsPortalPath = realpath($apiRelPortalPath);
        // Password .pem file
        $password = 'jatielok123';

        // Generate signature
        $data = 'dde72790-6a6d-4e35-b586-ffac8e1030fb|'.$x_timestamp;
        $rsa_algorithm = OPENSSL_ALGO_SHA256;

        // Load private key file
        $fp = fopen($apiRelPortalPath, 'r');
        $privatekey_file = fread($fp, 8192);
        fclose($fp);
        $privatekey = openssl_pkey_get_private($privatekey_file, $password);

        // Sign data
        openssl_sign($data, $signature, $privatekey, $rsa_algorithm);
        $x_signature = base64_encode($signature);
        echo "X-Signature: " . $x_signature . "\n" . "X-Timestamp: " . $x_timestamp . "\n";
        return [$x_timestamp, $x_signature];
    }

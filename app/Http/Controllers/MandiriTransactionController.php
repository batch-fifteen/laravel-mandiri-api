<?php
namespace App\Http\Controllers;

require __DIR__.'/../../../vendor/autoload.php';
require __DIR__.'/../../Console/Commands/Mandiri/AuthSignature.php';




use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Date;

$ClientSecret = "b319a510-c02a-49dc-817e-16c76c65eaac";
$XPartnerId = "SANDBOX";

class MandiriTransactionController extends Controller
{
    
    public function getAuth(){
        $url = "https://sandbox.bankmandiri.co.id/openapi/auth/v2.0/access-token/b2b";
        $grantType = "client_credentials";
        $ClientId = "05b00320-012a-4434-be51-3135639e8e30";

        list($x_timestamp, $x_signature) = getAuthSignature();
        
        $response = Http::withHeaders([
            'X-CLIENT-KEY' => $ClientId ,
            'X-TIMESTAMP' => $x_timestamp, 
            'X-SIGNATURE' => $x_signature])->post($url, 
        ['grantType' => $grantType]);
        
        $responseJson = $response->json();
        return $responseJson;
    }

    public function getTransactionsHistory($accountNumber, $fromDateTime, $toDateTime, $pageNumber){
        $url = 'https://sandbox.bankmandiri.co.id/openapi/transactions/v2.1/bank-statement';

        
        $response = $this->getAuth();
        $accessToken = $response['accessToken'];

        $fdt = (new DateTime($fromDateTime))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('c');
        $tdt = (new DateTime($toDateTime))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('c');
        
        //==========================================================================
        $data_json = sprintf('{
            "accountNo": "%s",
            "fromDateTime":"%s",
            "toDateTime":"%s",
            "additionalInfo":
            {
                "pageNumber":"%s"
                }
                }', $accountNumber, $fdt, $tdt, $pageNumber);
    
        $data_json_minify = json_encode(json_decode($data_json));
    
        //Example Generate X-Timestamp
        $timestamp = new DateTime();
        $timestamp->setTimeZone(new DateTimeZone('Asia/Jakarta'));
        $x_timestamp = $timestamp->format('c');
    
        //Example Result SHA256 + Hex Encode + Lower Case
        $bin_sha256 = hash('sha256', $data_json_minify, true);
        $hex_encode = bin2hex($bin_sha256);
        $str_lower = strtolower($hex_encode);
    
        //Example HTTP Method
        $http_method = 'POST';
    
        //Endpoint URL
        $end_point_url = '/openapi/transactions/v2.1/bank-statement';
    
        //Example Client Secret. must be adjusted based on partner client secret
        $client_secret = 'b319a510-c02a-49dc-817e-16c76c65eaac';
    
        $x_external_id = '20000113';
        $channel_id = 62456;
    
        //Example SHA512 Process
        $full_data = $http_method . ':' . $end_point_url . ':' . $accessToken . ':' . $str_lower . ':' . $x_timestamp;
        $bin_sha512 = hash_hmac('sha512', $full_data, $client_secret, true);
        $base64 = base64_encode($bin_sha512);
    
        //X-SIGNATURE RESULT
        // echo  'jsonmini: '.$data_json_minify."\n";
        // echo 'full data: '.$full_data."\n";
        // echo 'x-signature: '.$base64."\n";
        // echo 'x-external-id: '.$x_external_id."\n";
        // return [$x_timestamp, $base64, $x_external_id, $channel_id];
        //============================================================================
        
        $response = Http::withHeaders([
            'Content-Type'=>'application/json',
            'Authorization'=>'Bearer '.$accessToken,
            'X-TIMESTAMP'=>$x_timestamp,
            'X-SIGNATURE'=>$base64,
            'X-PARTNER-ID'=>'SANDBOX',
            'X-EXTERNAL-ID'=>$x_external_id,
            'CHANNEL-ID'=>$channel_id
        ])->post($url,[
            'accountNo'=>$accountNumber,
            'fromDateTime'=>$fdt,
            'toDateTime'=>$tdt,
            'additionalInfo'=>[
                'pageNumber'=>$pageNumber]
        ]);

        $responseJson = $response->json();
        return $responseJson;


    }
    
}

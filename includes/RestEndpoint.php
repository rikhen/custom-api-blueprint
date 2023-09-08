<?php
namespace Webshr\CustomAPI;

use WP_Error;
use WP_REST_Response;

class RestEndpoint {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_api_endpoint']);
    }

    /**
     * Registers the API endpoint for fetching data from an external API.
     *
     * @return void
     */
    public function register_api_endpoint() {
        register_rest_route('webshr/v1', '/fetch-external-api', array(
            'methods' => 'POST',
            'callback' => [$this, 'fetch_external_api_data'],
            'permission_callback' => '__return_true'
        ));
    }

    /**
     * Callback function for the API endpoint.
     *
     * @param array $req The request object.
     *
     * @return WP_Error|WP_REST_Response Returns an error if the API key is empty or cURL request fails. Otherwise, returns the response from the external API.
     */
    function fetch_external_api_data($req) {

    $example_value = $req["exampleValue"];
    $data_encryption = new DataEncryption();

     // Decrypt the API key stored in WordPress options
    $api_key = $data_encryption->decrypt(get_option( 'api_key' ));

     // Check if API key is empty and return an error if it is
    if(empty($api_key)){
        return new WP_Error( 'error', 'Please enter an API key in settings to use this feature', array( 'status' => 403 ) );
    }

    // Test mode - returns the decrypted API key as a response. DO NOT leave this uncommented on production
     $test_mode = $req["testMode"];
     if($test_mode){
        $res = new WP_REST_Response("Your API key is: {$api_key}");
        $res->set_status(200);
    
       return $res;
    }

    $curl = curl_init();

    // Make a cURL request to external API using the decrypted API key
    curl_setopt_array($curl, [
        // URL for example purposes only, this request will fail as-is even if API key valid - replace with your own request
        CURLOPT_URL => "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?key={$api_key}&option={$example_value}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    // Return an error if cURL request fails
    if ($err) {

        return new WP_Error( 'error', 'Invalid Request', array( 'status' => 404 ) );

    } else {

        $res = new WP_REST_Response($response);
        $res->set_status(200);
    
        return $res;
    }
    }
}
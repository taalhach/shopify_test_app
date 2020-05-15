<?php
require "vendor/autoload.php";
function make_post_request($base_uri,$url,$options=null){
    $client = new \GuzzleHttp\Client(["base_uri" => $base_uri]);
    $response = $client->post($url, $options);
    return json_decode($response->getBody(),true);
}
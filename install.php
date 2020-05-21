<?php
if (isset($_GET['shop'])){
    $shop=$_GET['shop'];
    $api_key='bd2e0dd0801b0b7f726a1525188d32d8';
    $api_secret='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';
    $scopes='read_orders,read_products';
//    $redirect_uri = "http://shopifytest.iserver.purelogics.net/test_app/generate_token.php";
    $redirect_uri = "http://localhost/shopify/test_app/generate_token.php";
    $install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
    header("Location: " . $install_url);
    die();

}else{
    echo 'shop is not defined';
}

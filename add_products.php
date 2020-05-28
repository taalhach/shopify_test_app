<?php
require_once 'constants.php';

$query='SELECT id,shop_url,token from shop_tokens where is_active =1;';
$shopsData=$db->query($query);
$date = date('Y-m-d H:i:s');
$dbCount=0;
$mtfCount=0;
if (count($shopsData)>0){
    foreach ($shopsData as $shop){
        try {
            $sc= new ShopifyClient($shop['shop_url'],$shop['token'],API_KEY,API_SECRET);
            $loop=true;
            $next_page='';
            $prev_page='';
            do{
                if ($next_page=''){
                    $products=$sc->call('GET','/admin/products.json?limit=250');
                }else{
                    $products=$sc->call('GET','/admin/products.json?limit=250&page_info='.$next_page);
                }
                    $headers=$sc->getHeaders();

                    if(isset($headers['link']) && $headers['link'] != ''){

                        $pages_info = $sc->getPageInfo($headers['link']);

                        if(isset($pages_info)){
                            $next_page = $pages_info['next_page'];
                            $prev_page= $pages_info['prev_page'];
                        }else{
                            $next_page = '';
                            $prev_page = '';
                        }

                    }else{
                        $loop = false;
                    }
                    if($next_page == ''){
                        $loop = false;
                    }
                    if (count($products)>0){
                        foreach ($products as $product){
                            $metadata=$sc->call('GET','/admin/api/2020-04/products/'.$product['id'].'/metafields.json');
//                            print_r($metadata);
                            $mtdExists=false;
                            foreach ($metadata as $mtd){
                                if ($mtd['key']==TAG){
                                    $mtdExists=true;
                                }
                            }
                            if (!$mtdExists){
//                                echo 'going to request <br>';
                                $metadata=$sc->call('POST','/admin/api/2020-04/products/'.$product['id'].'/metafields.json',array('metafield'=>array(
                                    'namespace'=>'custom_namespace',
                                    'key'=>TAG,
                                    'value'=>TAG,
                                    'value_type'=>'string'
                                )));
                                $mtfCount++;
//                                print_r($metadata);
                            }
                            $variants=count($product['variants']);
                            $db->where('product_id',$product['id']);
                            $db->where('shop_id',$shop['shop_url']);
                            $productsDB=$db->get(TABLE_SHOP_PRODUCTS);
                            if(count($productsDB)==0){
                                for ($i=0;$i<$variants;$i++){
                                    $db->where('product_id', $product['id']);
                                    $db->where('variant_id', $product["variants"][$i]['id']);
                                    $db->where('shop_id', $shop['id']);
                                    $variantDB = $db->get(TABLE_SHOP_PRODUCTS);
                                    if (!count($variantDB)>0){
                                        $image = '';
                                        if (isset($product['images']) && count($product['images']) > 0) {
                                            foreach ($product['images'] as $images)
                                            {
                                                if(isset($images['variant_ids']) && count($images['variant_ids']) > 0 )
                                                {
                                                    if (in_array($product["variants"][$i]['id'], $images['variant_ids'])) {
                                                        $image = $images['src'];
                                                        break;
                                                    }else{
                                                        $image = isset($product['images'][0]) ? $product['images'][0]['src'] : '';
                                                    }
                                                }else{
                                                    $image = isset($product['images'][0]) ? $product['images'][0]['src'] : '';
                                                }
                                            }
                                        }
                                        if (isset($product['published_at']) && $product['published_at'] != '') {
                                            // $published_at = $product['published_at'];
                                            $published_at = gmdate('Y-m-d H:i:s',strtotime($product['published_at']));
                                            $published = '1';
                                        } else {
                                            $published_at = '0000-00-00 00:00:00';
                                            $published = '0';
                                        }
                                        print_r($product['variants'][$i]);
                                        $nProd = array(
                                            'product_id' => $product['id'],
                                            'product_title' => $product['title'] . ' ' . $product["variants"][$i]['title'],
                                            'product_sku' => $product["variants"][$i]['sku'],
                                            'variant_iwd' => $product["variants"][$i]['id'],
                                            'product_image' => $image,
                                            'shop_id' => $shop['id'],
                                            'product_price' => $product["variants"][$i]['price'],
                                            'product_handle' => $product["handle"],
                                            'inventory_management' => $product["variants"][$i]['inventory_management'],
                                            'inventory_policy' => $product["variants"][$i]['inventory_policy'],
                                            'inventory_quantity' => $product["variants"][$i]['inventory_quantity'],
                                            'created_at' => $date,
                                            'updated_at' => $date,
                                            'published_at' => $published_at,
                                            'is_published' => $published
                                        );
                                        $db->insert(TABLE_SHOP_PRODUCTS, $nProd);
                                        $dbCount++;
                                    }
                                }

                            }
                        }
                    }else{
                        $loop=false;
                    }
            }while($loop);

        }catch (Exception $e){
            $loop=false;
        }
    }
}


echo "$dbCount products are stored <br> $mtfCount products attached with matafields";

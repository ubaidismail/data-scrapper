<?php

/**
 * Plugin Name: Data Scrapper
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: Data Scrapper.

 * Version: 1.0
 * Author: Cloudtach
 * Author URI: https://cloudtach.com/
 */
if (!defined('ABSPATH')) {
    exit;
}

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

require 'vendor/autoload.php';

define('ds_proxy_PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));

register_activation_hook(__FILE__, 'wp_ds_activation_hook');
function wp_ds_activation_hook()
{


    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'ebay_listings';

    $sql = "CREATE TABLE `$table_name` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `title` text(255) NOT NULL,
        `description` text(255) NOT NULL,
        `date` text(255) NOT NULL,
        `add_id` text(255) NOT NULL,
        `image_URL` text(255) NOT NULL,
        `location` text(255) NOT NULL,
        `list_items` text(255) NOT NULL,
        `long_desctiption` text(255) NOT NULL,
        `md5_code` text(255) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 $charset_collate";

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
}

function DS_enqueue_links()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-ajax-script', plugin_dir_url(__FILE__) . 'assets/js/admin-ajax.js');
    wp_localize_script(
        'custom-ajax-script',
        'scrapper_ajax',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        )
    );
}
add_action('admin_enqueue_scripts', 'DS_enqueue_links');

if (is_admin()) {
    include(plugin_dir_path(__FILE__) . '/admin/admin-settings.php');
    require_once(plugin_dir_path(__FILE__) . '/admin/class-wp-proxy.php');
    require_once(plugin_dir_path(__FILE__) . '/admin/compare-listings.php');

    function ds_proxy()
    {
        return ds_proxy::instance();
    }

    $GLOBALS['ds_proxy'] = ds_proxy();
}
add_action('wp_ajax_ebay_scrapper_func', 'ebay_scrapper_func');
function ebay_scrapper_func()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ebay_listings';
    $select_query = $wpdb->get_results("SELECT * FROM $table_name");

    global $wpdb;

    $url = $_POST['data_url'];
    $is_url = filter_var($url, FILTER_VALIDATE_URL);
    if (!$is_url) {
        echo 'Please enter correct URL';
        wp_die();
    }
    if(!empty($select_query)){
        echo 'Please Delete existing data';
        wp_die();
    }
    $httpClient = new \GuzzleHttp\Client();
    $response = $httpClient->get($url);
    $htmlString = (string) $response->getBody();

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);
    $xpath = new DOMXPath($doc);
    $titles = $xpath->evaluate("//ul//li//article//h2/a");
    $desc = $xpath->evaluate("//p[@class='aditem-main--middle--description']");
    $addId = $xpath->evaluate("//article");
    $img = $xpath->evaluate("//article//div[@class='imagebox srpimagebox']/img");
    $single_pg_link =  $xpath->evaluate("//ul//li//article//h2/a");
    $date = $xpath->evaluate("//article//div[@class='aditem-main--top']/div[@class='aditem-main--top--right']");
    $location_data = $xpath->evaluate("//article//div[@class='aditem-main--top']/div[@class='aditem-main--top--left']");
    
    foreach ($titles as $t) {
        $title[] =  $t->textContent . PHP_EOL;
        
    }
    foreach ($date as $dt) {
        $date_array[] =  $dt->textContent . PHP_EOL;
        
    }
    foreach ($location_data as $locality) {
        $location[] =  $locality->textContent . PHP_EOL;
    }
    foreach($single_pg_link as $a){
        $anchor = trim($a->getAttribute('href'));
        $link = 'https://www.ebay-kleinanzeigen.de'.  $anchor;
        $getresp = $httpClient->get($link);
        $htmlString_single_pg = (string) $getresp->getBody();

        libxml_use_internal_errors(true);
        $doc_sing_pg = new DOMDocument();
        $doc_sing_pg->loadHTML($htmlString_single_pg);
        $xpath_single_pg = new DOMXPath($doc_sing_pg);
        $desc_sing_pg = $xpath_single_pg->evaluate("//article//p[@id='viewad-description-text']");
        $listItems = $xpath_single_pg->evaluate("//article//div[@class='splitlinebox l-container-row']//ul/li");
       
        foreach($desc_sing_pg as $des){
            $long_desc[] = $des->textContent . PHP_EOL;
        }
        // listitems
        foreach($listItems as $list){
            $list_item[]  = $list->textContent . PHP_EOL;
        }
        
    }

    foreach ($desc as $d) {
        $description[] = $d->textContent . PHP_EOL;
    }
    foreach ($addId as $add) {
        $add_id[] = trim($add->getAttribute('data-adid'));

    }
    foreach ($img as $i) {
        $img_all1[] = trim($i->getAttribute('src'));
        
    }
    $table_name = $wpdb->prefix . 'ebay_listings';
    
    $encode_title = json_encode($title);
    $encode_desc = json_encode($description);
    $encode_add_id = json_encode($add_id);
    $encode_img_all = json_encode($img_all1);
    $encode_date = json_encode($date_array);
    $encode_locatione = json_encode($location);
    $encode_list_item = json_encode($list_item);
    $encode_long_desc = json_encode($long_desc);

    $mdf_of_add_id = md5($encode_add_id);

    // echo $encode_title;
    if(!empty($encode_title)){
    $insert_data = $wpdb->insert($table_name, array(
        'title' => $encode_title,
        'description' => $encode_desc,
        'date' => $encode_date,
        'add_id' => $encode_add_id, // ... and so on
        'image_URL' => $encode_img_all, // ... and so on
        'location' => $encode_locatione, // ... and so on
        'list_items' => $encode_list_item, // ... and so on
        'long_desctiption' => $encode_long_desc, // ... and so on
        'md5_code' => $mdf_of_add_id, // ... and so on
    ));
		 if($insert_data){
        echo 'ok';
		}else{
			echo 'Something went wrong';
		}
	}else{
		echo 'Error in fetching data please try again later';
	}
   
}

add_action('wp_ajax_ebay_compare_func' , 'ebay_compare_func');
function ebay_compare_func(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'ebay_listings';
    $old_md5 = $wpdb->get_results("SELECT md5_code FROM $table_name");
    
    // foreach($select_query as $d){
    //     $a[] = $d->add_id;
    //     }
      
    $url = $_POST['data_url'];
    $httpClient = new \GuzzleHttp\Client();
    $response = $httpClient->get($url);
    $htmlString = (string) $response->getBody();

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);
    $xpath = new DOMXPath($doc);
    // $titles = $xpath->evaluate("//ul//li//article//h2/a");
    // $desc = $xpath->evaluate("//p[@class='aditem-main--middle--description']");
    $addId = $xpath->evaluate("//article");
    // $img = $xpath->evaluate("//article//div[@class='imagebox srpimagebox']/img");
    // $single_pg_link =  $xpath->evaluate("//ul//li//article//h2/a");
    // $date = $xpath->evaluate("//article//div[@class='aditem-main--top']/div[@class='aditem-main--top--right']");
    // $location_data = $xpath->evaluate("//article//div[@class='aditem-main--top']/div[@class='aditem-main--top--left']");

    foreach ($addId as $add) {
        $add_id[] = trim($add->getAttribute('data-adid'));
    }
    // print_r($add_id);
    $encode_add_id = json_encode($add_id);
    $md5_add_id = md5($encode_add_id);
    // echo $encode_add_id;
    if($md5_add_id == $old_md5[0]->md5_code){
        echo 'Data is same';
        wp_die();
    }else{
        echo 'Some Changes in the Data';
        wp_die();
    }
    
}

add_action('wp_ajax_insert_data_to_post_type_func' , 'insert_data_to_post_type_func');
function insert_data_to_post_type_func(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'ebay_listings';
    $select_query =  $wpdb->get_results("SELECT * FROM $table_name LIMIT 1");
    foreach($select_query as $sq){
        $heading = $sq->title;
        $x = json_decode($heading);
        
        $des = $sq->long_desctiption;
        $y = json_decode($des);
        $z = json_decode($sq->add_id);
        
        foreach($x as $tx){
            $title[] = $tx;
        }
        foreach($y as $lng_des){
            $description[] = $lng_des;
        }
        
        foreach($z as $add){
            $add_id[] = $add;
        }
        $i = -1;
        foreach($add_id as $a){
            $i++;
            
            // wp_die();
             $post_id = wp_insert_post(array (
            'post_type' => 'post',
            'post_title' => $title[$i],
            'post_content' => empty($description[$i])? '...' : $description[$i],
            'post_status' => 'publish',
            'comment_status' => 'closed',   // if you prefer
            'meta_input' => $a,
         ));    
         if( $post_id){
                echo 'Data inserted';
                wp_die();
            }else{
                echo 'Something went wrong';
                wp_die();
         }
        }
       
        
    }

}
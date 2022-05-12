<?php
/**
 * Plugin Name: Data Scrapper
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: Data Scrapper.

 * Version: 1.0
 * Author: Cloudtach
 * Author URI: https://cloudtach.com/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
require 'vendor/autoload.php';

define( 'ds_proxy_PLUGIN_NAME', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

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
    require_once( plugin_dir_path(__FILE__) . '/admin/class-wp-proxy.php');

    function ds_proxy() {
        return ds_proxy::instance();
    }
    
    $GLOBALS['ds_proxy'] = ds_proxy();

}
add_action('wp_ajax_ebay_scrapper_func', 'ebay_scrapper_func');
function ebay_scrapper_func()
{

    $url = $_POST['data_url'];
    $is_url = filter_var($url, FILTER_VALIDATE_URL);
    if (!$is_url) {
        echo 'Please enter correct URL';
        wp_die();
    }
    $httpClient = new \GuzzleHttp\Client();
    // $response = $httpClient->get('https://www.ebay-kleinanzeigen.de/s-bestandsliste.html?userId=50265443');
    $response = $httpClient->get($url);

    $htmlString = (string) $response->getBody();
    //add this line to suppress any warnings
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);
    $xpath = new DOMXPath($doc);
    $titles = $xpath->evaluate("//ul//li//article//h2/a");
    $desc = $xpath->evaluate("//p[@class='aditem-main--middle--description']");
    $addId = $xpath->evaluate("//article");
    $img = $xpath->evaluate("//article//div[@class='imagebox srpimagebox']/img");

    foreach ($titles as $t) {
        $title =  $t->textContent . PHP_EOL;   
    }
    
    foreach ($desc as $d) {
        $description = $d->textContent . PHP_EOL;
    }
    foreach ($addId as $add) {
        $add_id = trim($add->getAttribute('data-adid'));
    }
    // print_r($add_id);
    foreach($img as $i){
        $img_all1 = trim($i->getAttribute('src'));
        $image_name = basename($img_all1);
        
        $postId = wp_insert_post(array(
        'post_title'=> $title,
        'post_type'=> 'post', 
        'post_status'=> 'publish',
        'post_content'=> $description,
      ));
      
      
        $upload = wp_upload_bits( $image_name , null, file_get_contents($img_all1, FILE_USE_INCLUDE_PATH));
        
        // check and return file type
        $imageFile = $upload['file'];
        $wpFileType = wp_check_filetype($imageFile, null);
        
        // Attachment attributes for file
        $attachment = array(
        'post_mime_type' => $wpFileType['type'],  // file type
        'post_title' => sanitize_file_name($imageFile),  // sanitize and use image name as file name
        'post_content' => '',  // could use the image description here as the content
        'post_status' => 'inherit'
        );
        
        // insert and return attachment id
        $attachmentId = wp_insert_attachment( $attachment, $imageFile, $postId );
        
        // insert and return attachment metadata
        $attachmentData = wp_generate_attachment_metadata( $attachmentId, $imageFile);
        
        // update and return attachment metadata
        wp_update_attachment_metadata( $attachmentId, $attachmentData );
        
        // finally, associate attachment id to post id
        $success = set_post_thumbnail( $postId, $attachmentId );
        
        // was featured image associated with post?
        if($success){
        
        $message = $image_name. ' has been added as featured image to post.';
        echo $message;
        
        } else {
        
        $message = $image_name . ' has NOT been added as featured image to post.';
        echo $message;
        
        }
      }
    
}


add_action('wp_ajax_DS_mobile_de_scrapper_func', 'DS_mobile_de_scrapper_func');
function DS_mobile_de_scrapper_func()
{
    $url = $_POST['data_url'];
    $is_url = filter_var($url, FILTER_VALIDATE_URL);
    if (!$is_url) {
        echo 'Please enter correct URL';
        wp_die();
    }
    $httpClient = new \GuzzleHttp\Client();
    try {
        // $response = $httpClient->get($url);

        // $htmlString = (string) $response->getBody();
        // libxml_use_internal_errors(true);
        // $doc = new DOMDocument();
        // $doc->loadHTML($htmlString);
        // $xpath = new DOMXPath($doc);
        // $titles = $xpath->evaluate("/html");

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: _abck=F10FA09954BE3F2BC307D7379AC9544D~-1~YAAQHFfIF9hSmZiAAQAAcSaRowd4yvccxVI3SU4QvDk0L8PSmiV955OHPD9ndyprQIEwpzDKmumtHTW9SOGfGvk1eZ58HamJa3BsApC+BQlaoHcegi3MoEb68QbR9XBiD39itQ2bGzzbX8zhIthhr2CPfEDoNXRrljk/TIr8sbjEPCv5sxQKHDre8CcLZGKYhqKvRuCMe5HgG2zZFqF2hI5b+Frfe4RzgHsnz7rLEG2Hw7PEEKiDIoknsu1OAOksYecqX1UmYsDl970wBen/Rxy16fTsq4ei4S1OgLJ0LHK2LJ2cPUsR6CERCgfRm3vLFhKOBMHa++5l17dK2Za49e/soyixgAGakIKInTY7EW80AE/Ro/whCw==~-1~-1~-1; ak_bmsc=8F06B6F756A6BACB5389AE35247B81D3~000000000000000000000000000000~YAAQHFfIF9lSmZiAAQAAciaRow+QFCb1jHPof5WMHeOBVs5lkHUZqvM/XhP4i+fWavp0zbeNUGeNn02IxVZbTOXfqXu9Jfnj6JY6y0uJmDYVGUFduGXH7rUqrTCzMQz77plJYj6x+XQMndg85kwKxKZF/oixvZK+yzvtMtz4+cxnNK8/HEFSIkxmYZUaCrNqQJ0Lc3V3jTPWKg01NQ8sgr/pLhwR84u0Ll9ovRUymzDJTNuYFktbyvEZOkmEfLzAxvZg9O2j/xH95ERt1ETLUg1yBEPCSlyFKQEezOZi/g/hc2eMPtLyBH4/Jn+s4fNN2UfsJFtpzCfVReJU02qvQAfJUw5sXUrD0P+4HRU5CTpE0Qv1Rl/rghaul2vx; bm_mi=469936210CBBBB4ED62D386B5C158139~YAAQHFfIFyx3nJiAAQAAwJvTow9DhuxPTDa047CltAR+MqB0r2kFJXPvACvaW6+U7Eic9bMTmcjRSt5p0iVkLLeg+f9FEQ7H9vJtv4slC3JFoL9jefFL0Fl41MJjSAgAliGQxYYat4HjZyzqQSeyKDuDXn3lwMmtKMpbI29N5kC8pLgiawBNmLSzKxcyNYHAeCPfwxIJuY300tA5l8eB6Yv26oL0LATB9lKNwiPd9wlKD1wneDJ7bed0g5ByCk1J+uMQ6sbI4zPXOrz+1e7MkPbBwPeINsasACqvBMSWKR/GyjAj430bp1v5lrAkmbSCLdjA4rRke2B8+WP93sX2ORtlWFg/3RAg1A==~1; bm_sv=172DD2EE4F2625F1F271656D0240FB4A~YAAQHFfIFy13nJiAAQAAwJvTow9zhF1O5fLfCZ1w8Hx2ghFWvuz5PQtUjQse3YnCcPvn8gGvQkos1XL3evjZ2x1VFnBCu5pN5Z7rn3ggJD3zK8+KpkeiUeWo+aQoB35h2uTyYpBCzkTzl8QIOSZ5bMC3XMOZf7wW+s5Kv80ge5NbudcCkoTsYsbytY78+5AWfoUQby2L2JdMvRgVhAFEmZ9OyqBWE5sxkhqP06Ll9vJlS827waVLUD0KcmxQUJM=~1; bm_sz=35E70E1DF5779F74B0651C8684332675~YAAQHFfIF9tSmZiAAQAAciaRow+hHNaNXshB3li6C+7/DYrMZbQTWawNZG9R7xxEgsoMBLXmj0St0nl7dEE3/yTFeU9OPmiAxc0AS9xCoISKSl7psDiUYko8sr90faUB703aUaVu+YjSlCWzKDNxz8XkdYUa28/kZFwmxVGQnNsjLZfz1AFFbpfn3ZYnsXZz4E2uBlzmYUZimNKD2v+Vgq8m6Yh7FA6jOpvuYjBJAiWd+1cMLI3sj7m+p0QIOFLtT5da4u3YEPvus1Tm6ORJ2Oew5x91VJFj0zG8Coya9FDXLw==~3290417~3753525'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        // print_r($titles);
    } catch (ClientException $e) {
        echo 'Message: ' .$e->getMessage();
    } catch (RequestException $e) {
        echo 'Message: ' .$e->getMessage();
    }
    
}
//3
add_action('wp_ajax_DS_immobilienscout24_scrapper_func', 'DS_immobilienscout24_scrapper_func');
function DS_immobilienscout24_scrapper_func(){
    
    $url3 = $_POST['data_url'];
    $is_url3 = filter_var($url3, FILTER_VALIDATE_URL);
    if (!$is_url3) {
        echo 'Please enter correct URL';
        wp_die();
    }
    $httpClient3 = new \GuzzleHttp\Client();
    $response3 = $httpClient3->get($url3);
    $htmlString3 = (string) $response3->getBody();
    //add this line to suppress any warnings
    libxml_use_internal_errors(true);
    $doc3 = new DOMDocument();
    $doc3->loadHTML($htmlString3);
    $xpath3 = new DOMXPath($doc3);
    $images = $xpath3->evaluate("//div[@class='grid gutter-m']//div[@class='vendor-object']//a//div/img");
    foreach($images as $imgs){
        var_dump($imgs);
    }
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'salcode_add_plugin_page_settings_link');
function salcode_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'options-general.php?page=ds-settings' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}


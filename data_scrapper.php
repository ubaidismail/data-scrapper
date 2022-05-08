<?php
/**
 * Plugin Name: Data Scrapper
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: Data Scrapper.

 * Version: 1.0
 * Author: Cloudtach
 * Author URI: http://www.mywebsite.com
 */
require 'vendor/autoload.php';
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
    // print_r($addId);
    // return;

    foreach ($titles as $t) {
        // $extractedTitles[] = $title->textContent.PHP_EOL;
        echo $t->textContent . PHP_EOL;
    }
    foreach ($desc as $d) {
        // $extractedTitles[] = $title->textContent.PHP_EOL;
        echo $d->textContent . PHP_EOL;
    }
    foreach ($addId as $add) {
        $add_id[] = trim($add->getAttribute('data-adid'));
    }
    // print_r($add_id);
    foreach($img as $i){
        $img_all1[] = trim($i->getAttribute('src'));
        
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
    // $response = $httpClient->get('https://www.ebay-kleinanzeigen.de/s-bestandsliste.html?userId=50265443');
    $response = $httpClient->get($url);

    $htmlString = (string) $response->getBody();
    //add this line to suppress any warnings
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);
    $xpath = new DOMXPath($doc);
    $titles = $xpath->evaluate("/html");
    echo $titles;
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


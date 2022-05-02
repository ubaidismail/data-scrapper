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
function ldvr_enqueue_links(){
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-ajax-script', plugin_dir_url(__FILE__) . 'assets/js/admin-ajax.js');
    wp_localize_script( 'custom-ajax-script', 'scrapper_ajax',
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        )
    );
} 
add_action('admin_enqueue_scripts', 'ldvr_enqueue_links');

if (is_admin()) {
    include(plugin_dir_path(__FILE__) . '/admin/admin-settings.php');
}
add_action( 'wp_ajax_ebay_scrapper_func', 'ebay_scrapper_func' );
function ebay_scrapper_func(){
    
        
        
        $url = $_POST['data_url'];
        $is_url = filter_var($url, FILTER_VALIDATE_URL);
        if(!$is_url){
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
        // $desc = $xpath->evaluate("//article/p");

            
            foreach ($titles as $t) {
                // $extractedTitles[] = $title->textContent.PHP_EOL;
                echo $t->textContent.PHP_EOL;
            }
    }


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
use GuzzleHttp\RequestOptions;

require 'vendor/autoload.php';

define('ds_proxy_PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));

register_activation_hook(__FILE__, 'wp_ds_activation_hook');
function wp_ds_activation_hook()
{


    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name1 = $wpdb->prefix . 'ebay_listings';
    // $table_name2 = $wpdb->prefix . 'ebay_listings_permenent';

    $sql1 = "CREATE TABLE `$table_name1` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `title` text(255) NOT NULL,
        `description` text(255) NOT NULL,
        `date` text(255) NOT NULL,
        `add_id` text(255) NOT NULL,
        `image_URL` text(255) NOT NULL,
        `gallery_urls` text(255) NOT NULL,
        `location` text(255) NOT NULL,
        `list_items` text(255) NOT NULL,
        `long_desctiption` text(255) NOT NULL,
        `price` text(255) NOT NULL,
        `md5_code` text(255) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 $charset_collate";

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name1'") != $table_name1) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);
    }
}
function delete_students_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ebay_listings';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
    delete_option("DS_plugin_db_version");
}
register_deactivation_hook( __FILE__, 'delete_students_table' );
function DS_enqueue_links()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-ajax-script', plugin_dir_url(__FILE__) . 'assets/js/admin-ajax.js');
    wp_enqueue_style('custom-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_localize_script(
        'custom-ajax-script',
        'scrapper_ajax',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        )
    );
}
add_action('admin_enqueue_scripts', 'DS_enqueue_links');

function DS_frontend(){
    wp_enqueue_script('jquery');
}
add_action('init' , 'DS_frontend');

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
    $table_name2 = $wpdb->prefix . 'ebay_listings_permenent';
    $select_query = $wpdb->get_results("SELECT * FROM $table_name");

    global $wpdb;

    $url = $_POST['data_url'];
    $is_url = filter_var($url, FILTER_VALIDATE_URL);
    if (!$is_url) {
        echo 'Please enter correct URL';
        wp_die();
    }
   
    $PROXY_TYPE = ds_proxy()->options['type'];
    $PROXY_USER = ds_proxy()->options['username'];
    $PROXY_PASS = ds_proxy()->options['password'];
    $PROXY_IP = ds_proxy()->options['proxy_host'];
    $PROXY_PORT = ds_proxy()->options['proxy_port'];

    $httpClient = new \GuzzleHttp\Client(
        [
            'proxy' => $PROXY_USER . ':' . $PROXY_PASS . '@' . $PROXY_IP . ':' . $PROXY_PORT, //use without "socks5://" scheme
            'verify' => true, // used only for SSL check , u can set false too for not check
            'curl' => [CURLOPT_PROXYTYPE => 7],

        ]
    );

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
    foreach ($single_pg_link as $a) {
        $anchor = trim($a->getAttribute('href'));
        $link = 'https://www.ebay-kleinanzeigen.de' .  $anchor;
        $getresp = $httpClient->get($link);
        $htmlString_single_pg = (string) $getresp->getBody();

        libxml_use_internal_errors(true);
        $doc_sing_pg = new DOMDocument();
        $doc_sing_pg->loadHTML($htmlString_single_pg);
        $xpath_single_pg = new DOMXPath($doc_sing_pg);
        $desc_sing_pg = $xpath_single_pg->evaluate("//article//p[@id='viewad-description-text']");
        $listItems = $xpath_single_pg->evaluate("//article//div[@class='splitlinebox l-container-row']//ul/li");
        $galler_imgs = $xpath_single_pg->evaluate("//article//div[@class='galleryimage-element']/img");
        $price = $xpath_single_pg->evaluate("//article//div[@class='contentbox--vip boxedarticle no-shadow l-container-row']/meta");

        foreach ($desc_sing_pg as $des) {
            $long_desc[] = $des->textContent . PHP_EOL;
        }
        // listitems
        foreach ($listItems as $list) {
            $list_item[]  = $list->textContent . PHP_EOL;
        }
        //galler_images
        foreach ($galler_imgs as $gallery) {
            $gallery_all_images[] = trim($gallery->getAttribute('src'));
        }
        foreach ($price as $pr) {
            $obje_price[] = trim($pr->getAttribute('content'));
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
    $encode_gallery_imgs = json_encode($gallery_all_images);
    $encode_obje_price = json_encode($obje_price);
    print_r($encode_obje_price);
    // exit;
    $mdf_of_add_id = md5($encode_add_id);

    // echo $encode_title;
    if (!is_null($encode_title)) {
        $insert_data = $wpdb->insert($table_name, array(
            'title' => $encode_title,
            'description' => $encode_desc,
            'date' => $encode_date,
            'add_id' => $encode_add_id, // ... and so on
            'image_URL' => $encode_img_all, // ... and so on
            'gallery_urls' => $encode_gallery_imgs, // ... and so on
            'location' => $encode_locatione, // ... and so on
            'list_items' => $encode_list_item, // ... and so on
            'long_desctiption' => $encode_long_desc, // ... and so on
            'price' => $encode_obje_price, // ... and so on
            'md5_code' => $mdf_of_add_id, // ... and so on
        ));

        if ($insert_data) {
            echo 'ok';
        } else {
            echo 'Something went wrong in inserting data';
        }
    } else {
        echo 'Error in fetching data please try again later';
    }
}

add_action('wp_ajax_ebay_compare_func', 'ebay_compare_func');
function ebay_compare_func()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ebay_listings';
    $old_md5 = $wpdb->get_results("SELECT md5_code FROM $table_name");

    $url = $_POST['data_url'];
    $PROXY_TYPE = ds_proxy()->options['type'];
    $PROXY_USER = ds_proxy()->options['username'];
    $PROXY_PASS = ds_proxy()->options['password'];
    $PROXY_IP = ds_proxy()->options['proxy_host'];
    $PROXY_PORT = ds_proxy()->options['proxy_port'];

    $httpClient = new \GuzzleHttp\Client(
        [
            'proxy' => $PROXY_USER . ':' . $PROXY_PASS . '@' . $PROXY_IP . ':' . $PROXY_PORT, //use without "socks5://" scheme
            'verify' => true, // used only for SSL check , u can set false too for not check
            'curl' => [CURLOPT_PROXYTYPE => 7],

        ]
    );
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
    foreach ($single_pg_link as $a) {
        $anchor = trim($a->getAttribute('href'));
        $link = 'https://www.ebay-kleinanzeigen.de' .  $anchor;
        $getresp = $httpClient->get($link);
        $htmlString_single_pg = (string) $getresp->getBody();

        libxml_use_internal_errors(true);
        $doc_sing_pg = new DOMDocument();
        $doc_sing_pg->loadHTML($htmlString_single_pg);
        $xpath_single_pg = new DOMXPath($doc_sing_pg);
        $desc_sing_pg = $xpath_single_pg->evaluate("//article//p[@id='viewad-description-text']");
        $listItems = $xpath_single_pg->evaluate("//article//div[@class='splitlinebox l-container-row']//ul/li");
        $galler_imgs = $xpath_single_pg->evaluate("//article//div[@class='galleryimage-element']/img");
        $price = $xpath_single_pg->evaluate("//article//div[@class='contentbox--vip boxedarticle no-shadow l-container-row']/meta");

        foreach ($desc_sing_pg as $des) {
            $long_desc[] = $des->textContent . PHP_EOL;
        }
        // listitems
        foreach ($listItems as $list) {
            $list_item[]  = $list->textContent . PHP_EOL;
        }
        foreach ($galler_imgs as $gallery) {
            $gallery_all_images[] = trim($gallery->getAttribute('src'));
        }
        foreach ($price as $pr) {
            $obje_price[] = trim($pr->getAttribute('content'));
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


    $encode_title = json_encode($title);
    $encode_desc = json_encode($description);
    $encode_img_all = json_encode($img_all1);
    $encode_date = json_encode($date_array);
    $encode_locatione = json_encode($location);
    $encode_list_item = json_encode($list_item);
    $encode_long_desc = json_encode($long_desc);
    $encode_gallery_all_images = json_encode($gallery_all_images);
    $encode_add_id = json_encode($add_id);
    $encode_obje_price = json_encode($obje_price);

    // print_r($add_id);
    $md5_add_id = md5($encode_add_id);
    // echo $encode_add_id;
    foreach ($old_md5 as $md_hash) {
        if ($md5_add_id == $md_hash->md5_code) {
            echo 'Data is same';
        } else {
            $update_data = $wpdb->update(
                $table_name,
                array(
                    'title' => $encode_title,
                    'description' => $encode_desc,
                    'date' => $encode_date,
                    'add_id' => $encode_add_id, // ... and so on
                    'image_URL' => $encode_img_all, // ... and so on
                    'gallery_urls' => $encode_gallery_all_images, // ... and so on
                    'location' => $encode_locatione, // ... and so on
                    'list_items' => $encode_list_item, // ... and so on
                    'long_desctiption' => $encode_long_desc, // ... and so on
                    'price' => $encode_obje_price, // ... and so on
                    'md5_code' => $md5_add_id, // ... and so on

                ),
                array('md5_code' => $md_hash->md5_code)
            );
            if ($update_data) {
                echo 'Some Changes in the Data';
            }

        }
    }
    wp_die();
}

add_action('wp_ajax_insert_data_to_post_type_func', 'insert_data_to_post_type_func');
function insert_data_to_post_type_func()
{
   
    global $wpdb;
    $table_name = $wpdb->prefix . 'ebay_listings';
    $select_query =  $wpdb->get_results("SELECT * FROM $table_name LIMIT 1");
    $user_id_get = $_POST['userID'];
    foreach ($select_query as $sq) {
        
        $heading = $sq->title;
        $x = json_decode($heading);

        $des = $sq->long_desctiption;
        $y = json_decode($des);
        $z = json_decode($sq->add_id);
        $imgs = json_decode($sq->image_URL);
        $date = json_decode($sq->date);
        $location = json_decode($sq->location);
        $list_items = json_decode($sq->list_items);
        $price = json_decode($sq->price);

        foreach ($x as $tx) {
            $title[] = $tx;
        }
        foreach ($y as $lng_des) {
            $description[] = $lng_des;
        }

        foreach ($z as $add) {
            $add_id[] = $add;
        }
        foreach ($date as $dt) {
            $eb_date[] = $dt;
        }
        foreach ($location as $loc) {
            $locality[] = $loc;
        }
        foreach ($list_items as $lit) {
            $listings[] = $lit;
        }
        foreach ($price as $p) {
            $pricing[] = $p;
        }
        $i = -1;
      
        foreach ($imgs as $sd) {
          
            $i++;
            $image_name = basename($sd);
           
            $advert_posts = array(
                'post_title' => $title[$i],
                'post_content' => $description[$i] . $listings[$i],
                'post_status' => 'publish',
                'post_author' => $user_id_get,
                'post_type' => 'advert',
                'comment_status' => 'open',
            );
            $post_id = wp_insert_post($advert_posts);
            $category_id = 877;
            $taxonomy = 'advert-category';
            wp_set_object_terms( $post_id, array( $category_id ), $taxonomy , true );
            add_post_meta( $post_id , 'advert_sale_price' , $pricing[$i]);
            add_post_meta( $post_id , 'advert_type' , 1);
            add_post_meta( $post_id , 'advert_negotiable' , 1);
            add_post_meta( $post_id , 'advert_location' , $locality[$i]);
            
            $upload = wp_upload_bits($image_name, null, file_get_contents($sd, FILE_USE_INCLUDE_PATH));
            $imageFile = $upload['file'];
            $wpFileType = wp_check_filetype($imageFile, null);
          
            // rwmb_set_meta( $post_id, 'advert_gallery', $imageFile, $args = [] );
            $attachment = array(
                'post_mime_type' => $wpFileType['type'],  // file type
                'post_title' => sanitize_file_name($imageFile),  // sanitize and use image name as file name
                'post_content' => '',  // could use the image description here as the content
                'post_status' => 'inherit'
            );
            $attachmentId = wp_insert_attachment($attachment, $imageFile, $post_id);

            // insert and return attachment metadata
            $attachmentData = wp_generate_attachment_metadata($attachmentId, $imageFile);

            // update and return attachment metadata
            wp_update_attachment_metadata($attachmentId, $attachmentData);
            // finally, associate attachment id to post id
            $success = set_post_thumbnail($post_id, $attachmentId);
            add_post_meta( $post_id , 'advert_gallery' , set_post_thumbnail($post_id, $attachmentId));
            
            if ($success) {
                echo 'Data and images have been inserted';
            } else {
                echo 'Something went wrong';
            }
        }
        wp_die();
    }
}

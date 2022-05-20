<?php

function DS_add_compare_listing_pg()
{
    add_options_page('Compare Listings', 'Compare Listings', 'manage_options', 'ds-compare', 'DS_compare_listings');
}
add_action('admin_menu', 'DS_add_compare_listing_pg');

function DS_compare_listings(){
    ?>
        <h1>Compare Exisiting Listings</h1>
    <div class="flex-fetch-admin">
        <div class="fetch-data-div">
            <form id="scrapper_compare_form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <h3><label for="">Ebay-kleinanzeigen.de</label></h3>
                <input type="text" placeholder="Enter URL" name="get_compare_URL" required>
                <input type="hidden" name="action" value="scrape_action1">
                <input type="submit" value="Scrape" name="scrape_web_compare">
            </form>
            <span class="compare-data1">Comparing Data...</span>
        </div>
    <?php
}
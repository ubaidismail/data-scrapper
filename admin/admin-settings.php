<style>
    .flex-fetch-admin {
    display: flex;
    justify-content: space-evenly;
}
</style>
<?php
function DATA_SRAPPER_add_settings_page() {
    add_options_page( 'Data Scrapper', 'Data Scrapper', 'manage_options', 'ds-settings', 'DATA_SRAPPER_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'DATA_SRAPPER_add_settings_page' );


function DATA_SRAPPER_render_plugin_settings_page(){
    ?>
    <h1>Enter URL To Fetch Data</h1>
    <div class="flex-fetch-admin">
        <div class="fetch-data-div">
            <form id="scrapper_form1" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                <h3><label for="">Ebay-kleinanzeigen.de</label></h3>
                <input type="text" placeholder="Enter URL" name="get_URL" required>
                <input type="hidden" name="action" value="scrape_action1">
                <input type="submit" value="Scrape" name="scrape_web_1">
            </form>
            <span class="fetching-data1">Fetchng Data...</span>
        </div>
        <div class="fetch-data-div">
            <form id="scrapper_form2" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
            <h3><label for="">Mobile.de</label></h3>
            
                <input type="text" placeholder="Enter URL" name="get_URL_2" required>
                <input type="hidden" name="action" value="scrape_action2">
                <input type="submit" value="Scrape" name="scrape_web_2">
            </form>
            <span class="fetching-data2">Fetchng Data...</span>
        </div>
        <div class="fetch-data-div">
        <form id="scrapper_form3" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <h3><label for="">Immobilienscout24.de</label></h3>
                <input type="text" placeholder="Enter URL" name="get_URL_3" required>
                <input type="hidden" name="action" value="scrape_action3">
                <input type="submit" value="Scrape" name="scrape_web_3">
                
            </form>
            <span class="fetching-data3">Fetchng Data...</span>
        </div>
    </div>
    <?php
}

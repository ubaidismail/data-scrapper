jQuery(document).ready(function($){
    $('.fetching-data1').hide();
    $('#scrapper_form1').on('submit' , function(e){
        e.preventDefault();
        $('.fetching-data1').show();
        let inputUrl = $('input[name="get_URL"]').val();

        var url_validate = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

            if(!url_validate.test(inputUrl)){
                alert('Enter a correct URL');
                $('.fetching-data1').hide();
                return;
            }


        $.ajax({
            type: 'POST',
            url: scrapper_ajax.ajaxurl,
            data:{
                action: 'ebay_scrapper_func',
                data_url: inputUrl,
            },success:function(data){
                $('.fetching-data1').hide();
                alert('success');
            },error:function(errorThrown){
                $('.fetching-data1').hide();
                console.error(errorThrown);
                alert('failure');
            }
        })
    })
    
})
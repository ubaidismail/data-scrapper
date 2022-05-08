jQuery(document).ready(function($){
    $('.fetching-data1').hide();
    $('.fetching-data2').hide();
    $('.fetching-data3').hide();
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
  
    //form2
    $('#scrapper_form2').on('submit' , function(e){
        e.preventDefault();      
        $('.fetching-data2').show();
        let inputUrl2 = $('input[name="get_URL_2"]').val();
        var url_validate = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

            if(!url_validate.test(inputUrl2)){
                alert('Enter a correct URL');
                $('.fetching-data2').hide();
                return;
            }

            $.ajax({
                type: 'POST',
                url: scrapper_ajax.ajaxurl,
                data:{
                    action: 'DS_mobile_de_scrapper_func',
                    data_url: inputUrl2,
                },success:function(data){
                    $('.fetching-data2').hide();
                    alert('success');
                },error:function(errorThrown){
                    $('.fetching-data2').hide();
                    console.error(errorThrown);
                    alert('failure');
                }
            })
    })

    //form3

    $('#scrapper_form3').on('submit' , function(e){
        e.preventDefault();      
        $('.fetching-data3').show();
        let inputUrl3 = $('input[name="get_URL_3"]').val();
        var url_validate = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

            if(!url_validate.test(inputUrl3)){
                alert('Enter a correct URL');
                $('.fetching-data3').hide();
                return;
            }

            $.ajax({
                type: 'POST',
                url: scrapper_ajax.ajaxurl,
                data:{
                    action: 'DS_immobilienscout24_scrapper_func',
                    data_url: inputUrl3,
                },success:function(data){
                    $('.fetching-data3').hide();
                    alert('success');
                },error:function(errorThrown){
                    $('.fetching-data3').hide();
                    console.error(errorThrown);
                    alert('failure');
                }
            })
    })
})
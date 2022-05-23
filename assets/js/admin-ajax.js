jQuery(document).ready(function($){
    $('.fetching-data1').hide();
    $('.fetching-data2').hide();
    $('.fetching-data3').hide();
    $('.compare-data1').hide();
    $('.inserting-data').hide();

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
                alert('Success');
            },error:function(errorThrown){
                $('.fetching-data1').hide();
                console.error(errorThrown);
                alert('failure');
            }
        })
    })

    //compare lisitngs

    $('#scrapper_compare_form').on('submit' , function(e){
        e.preventDefault();
        $('.compare-data1').show();
        let inputUrl = $('input[name="get_compare_URL"]').val();

        var url_validate = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

            if(!url_validate.test(inputUrl)){
                alert('Enter a correct URL');
                $('.compare-data1').hide();
                return;
            }


        $.ajax({
            type: 'POST',
            url: scrapper_ajax.ajaxurl,
            data:{
                action: 'ebay_compare_func',
                data_url: inputUrl,
            },success:function(data){
                $('.compare-data1').hide();
                alert(data);
            },error:function(errorThrown){
                $('.compare-data1').hide();
                console.error(errorThrown);
                alert('failure');
            }
        })
    })
  
  $('#get_selected_user_form').on('submit' , function(e){
    e.preventDefault();
    $('.inserting-data').show();
    var checkedValue = $('#get_selected_user_form input:checked').attr('date-userID');
    if(!checkedValue){
        $('.inserting-data').hide();
        alert('Please select user')
        return;
    }

      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: scrapper_ajax.ajaxurl,
        data:{
            action: 'insert_data_to_post_type_func',
            userID: checkedValue,
        },success:function(data){
            $('.inserting-data').hide();
            alert('Data inserted');
        },error:function(errorThrown){
            $('.inserting-data').hide();
            console.error(errorThrown);
            alert('failure');
        }
    })
  })
})
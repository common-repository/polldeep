jQuery(document).ready(function() {
    /**/
    var ajaxurl = jQuery('#ajaxurl').val();
    var merchantID = jQuery('#merchantID').val();
    var secretKey = jQuery('#secretKey').val();

    jQuery('#upload_image_button').click(function() { 
            formfield = jQuery('#upload_image').attr('name');
            tb_show('', 'media-upload.php?type=image&TB_iframe=true');
            return false;
        });

        window.send_to_editor = function(html) { 
            imgurl = jQuery('img',html).attr('src');
            if(!imgurl) {
                tb_remove(); 
                    alert('Please select a correct file type');
                    return false;
            }
            imgName = imgurl.slice(imgurl.lastIndexOf('/') + 1); 
            jQuery('#upload_image').val(imgurl);
            tb_remove(); 

            jQuery.ajax({ 
                type: 'POST',
                url: ajaxurl,
                maxFileSize: 5000000,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                // dataType: 'json',
                 data: {
                    action: 'polldeep_curlRequest',
                    'merchantID': merchantID,
                    'secretKey': secretKey,
                    'call': 'upload_file_to_polldeep',
                    'filename': imgName,
                    'myfile': imgurl
                },
                success: function(e, data) {   
                    var json = JSON.parse(e); 
                    input = jQuery(this);
                    if (json.success)    {    
                        jQuery('.preview-img').remove();
                        jQuery('#poll_background_image').remove();
                        console.log(jQuery('#poll_background_image_file').length);
                        jQuery("<div class='preview-wrapper'><span>&#10799;</span><img alt='"+json.success+"' class='preview-img' width='150' src='"+imgurl+"'/><input id='poll_background_image' type='hidden' name='poll_background_image' value='"+imgName+"'></div>" ).insertAfter( '#upload_image' );
                        jQuery('#poll_widget').css('background','url("'+imgurl+'") repeat scroll left top / 100% auto transparent');
                    } else if(json.unauthorized) {
                        alert('You do not have proper authorization to perform this activity.');
                    } else {
                        alert(json.failed);
                    }
                }
            });
        }

        /**/

    

    jQuery("#authenticateBtn").click(function(e) {
        e.preventDefault();
        var merchantID = jQuery('#merchantID').val();
        var secretKey = jQuery('#secretKey').val();

        if (merchantID == '') {
            jQuery('.notice').show();
            jQuery('.notice').find('p').text('Please enter merchant key');
        } else if (secretKey == '') {
            jQuery('.notice').show();
            jQuery('.notice').find('p').text('Please enter secret id');
        } else {
            jQuery('.notice').hide();
            var ajaxUrl = jQuery('#ajaxUrl').val();
            jQuery('#authenticateBtn').prop('disabled', true);
            jQuery('#authenticateBtn').val('please wait...');
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'polldeep_curlRequest',
                    'merchantID': merchantID,
                    'secretKey': secretKey,
                    'call': 'authenticateUser',
                },
                success: function(data) {
                    console.log(data);
                    return false;
                    var json = JSON.parse(data);
                    jQuery('#authenticateBtn').val('Authenticate');
                    jQuery('#authenticateBtn').prop('disabled', false);
                    if (json.status == "incorrect") {
                        jQuery('.notice').show();
                        jQuery('.notice').find('p').text(json.msg);
                        jQuery('.notice').show().delay(3000).fadeOut();
                    } else if (json.status == "succ") {
                        jQuery('.notice').show();
                        jQuery('.notice').find('p').text(json.msg);
                        jQuery('.notice').show().delay(3000).fadeOut();
                    }
                }
            })
        }
    });
    jQuery('#delete_all').click(function() {

        if (jQuery('.input-check-delete:checked').length > 0) {
            jQuery('.notice').hide();
            jQuery('#delete_all').val('Please Wait...');
            var deleteid = '';
            jQuery(':checkbox:checked').each(function(i) {

                if ((jQuery(':checkbox:checked').length - 1) == i) {
                    deleteid += jQuery(this).val();
                } else {
                    deleteid += jQuery(this).val() + ',';
                }
            });

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'polldeep_curlRequest',
                    'merchantID': merchantID,
                    'secretKey': secretKey,
                    'deleteid': deleteid,
                    'call': 'deleteAjax'
                },
                success: function(data) {
                    jQuery('#delete_all').val('Delete All');
                    var json = JSON.parse(data);
                    if (json.status == "succ") {
                        jQuery('.notice').show();
                        jQuery('.notice').find('p').text(json.msg);
                        location.reload();
                    } else {
                        jQuery('.notice').show();
                        jQuery('.notice').find('p').text(json.msg);
                        jQuery('.notice').show().delay(3000).fadeOut();
                    }
                }
            });
        } else {
            jQuery('.notice').show();
            jQuery('.notice').find('p').text('Please select polls...!!');
        }
    });

    jQuery('.deleteitem').click(function(e) {
        e.preventDefault();
        var id = jQuery(this).attr('data-id');
        jQuery(this).text('Please Wait...');
        jQuery('.notice').hide();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'polldeep_curlRequest',
                'merchantID': merchantID,
                'secretKey': secretKey,
                'deleteid': id,
                'call': 'deleteAjax'
            },
            success: function(data) {
                jQuery('.deleteitem').text('Delete');
                var json = JSON.parse(data);
                if (json.status == "succ") {
                    jQuery('.notice').show();
                    jQuery('.notice').find('p').text(json.msg);
                    location.reload();
                } else {
                    jQuery('.notice').show();
                    jQuery('.notice').find('p').text(json.msg);
                    jQuery('.notice').show().delay(3000).fadeOut();
                }
            }
        });
    });

    jQuery('.openclose').bind('click', function(e) {
        e.preventDefault();
        var id = jQuery(this).attr('data-id');
        var request = jQuery(this).attr('data-request');
        jQuery('.notice').hide();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'polldeep_curlRequest',
                'merchantID': merchantID,
                'secretKey': secretKey,
                'id': id,
                'request': request,
                'value': '-',
                'call': 'serverAjax'
            },
            success: function(data) {
                var json = JSON.parse(data);
                if (json.status == "succ") {
                    jQuery('.notice').show();
                    jQuery('.notice').find('p').text(json.msg);
                    jQuery('.notice').show().delay(3000).fadeOut();
                    jQuery('.openclose').each(function() {
                        if (jQuery(this).attr('data-id') == id) {
                            jQuery(this).text(json.text);
                            jQuery(this).attr('data-request', json.request);
                        }
                    })
                } else {
                    jQuery('.notice').show();
                    jQuery('.notice').find('p').text(json.msg);
                    jQuery('.notice').show().delay(3000).fadeOut();
                }
            }
        });
    });

    jQuery('.editSubmit').bind('click', function(e) {
        e.preventDefault();
        jQuery('.notice').hide();
        var form_data = new FormData(jQuery('.editPollForm')[0]);
        form_data.append('action', 'polldeep_curlRequest');
        form_data.append('call', 'editAjax');

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            contentType: false,
            processData: false,
            data: form_data,
            success: function(data) {
                var json = JSON.parse(data); 
                if (json.status == "succ") {
                    jQuery('.notice').show();
                    jQuery('.notice').find('p').text(json.msg);
                    jQuery('.notice').show().delay(3000).fadeOut();
                    location.reload();
                } else {
                    jQuery('.notice').show();
                    jQuery('.notice').find('p').text(json.msg);
                    jQuery('.notice').show().delay(3000).fadeOut();
                }
            }
        });
    });

    jQuery('.createSubmit').bind('click', function(e) { 
        e.preventDefault();
        jQuery('.notice').hide();
        var form_data = new FormData(jQuery('.createPollForm')[0]);
        form_data.append('action', 'polldeep_curlRequest');
        form_data.append('call', 'createAjax'); 
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            contentType: false,
            processData: false,
            data: form_data,

            success: function(data) { 
                var json = JSON.parse(data);     
                if (json.status == "succ") {
                    jQuery('.notice').show();
                    jQuery('.notice').find('p').text(json.msg);
                    jQuery('.notice').show().delay(3000).fadeOut();
                    window.location.replace(json.pollPath);
                } else {
                    jQuery('.notice').show();
                    jQuery('.notice').find('p').text(json.msg);
                    jQuery('.notice').show().delay(3000).fadeOut();
                }
            }

        });
    });

    jQuery('#view-btn').bind('click', function(e) { 
        e.preventDefault();
        var data = jQuery('.previewWD').html();
        localStorage.setItem('data', data);
        var data = localStorage.getItem('data');
        if (data != '') 
        {   
            var str = window.location.href;
            var str2 = "&action=";
            if(str.indexOf(str2) != -1)
            {
                 var res = str.replace("edit", "preview"); 
            }
            else
            {
                var res=str+'&action=preview';
            }
            var go_to_url =res;
            window.open(go_to_url, '_blank');
        }
    });
    
    jQuery('.pollshareBtn').click(function(e)
    { 
        e.preventDefault(); 
        jQuery(this).text('Please wait...');
        jQuery(this).prop('disabled', true);
        var id=jQuery('#unique_id').val();
        
        var recipients = '';
        jQuery('input[name="recipients[]"]').each(function(i){
          
           if((jQuery('input[name="recipients[]"]').length-1)==i)
           {
               recipients += jQuery(this).val(); 
           }
           else
           {
               recipients += jQuery(this).val()+','; 
           }
        });
        var comment=jQuery('#comment').text(); 
        jQuery.ajax
        ({ 
            type : 'POST',
            url: ajaxurl,
            data: {
                action:'polldeep_curlRequest',
                'merchantID':merchantID,
                'secretKey':secretKey,
                'id':id,
                'call':'pollshareAjax',
                'recipients':recipients,
                'comment':comment,
                }, 
            success: function(data) 
            {  
                jQuery('.create-btn').text('Email');
                jQuery('.create-btn').prop('disabled', false);
                jQuery('.notice').show();
                jQuery('.notice').find('p').text('An email has been sent to Recipients');
                jQuery('.notice').show().delay(3000).fadeOut(); 
                location.reload();
            }
         });  
    });
    jQuery('.get_statsmap').click(function(e){ 
        e.preventDefault(); 
        var id=jQuery('#pollid').val();
        var request=jQuery(this).attr('data-request'); 
        var value=jQuery(this).attr('data-value'); 
        jQuery.ajax
        ({  
            type : 'POST',
            url: ajaxurl,
            data: {
            action:'polldeep_curlRequest',
            'merchantID':merchantID,
            'secretKey':secretKey,
            'id':id,
            'request':request,
            'value':value,
            'call':'serverAjax'
            },
            success: function(data) 
            {  
                var json = JSON.parse(data);
                    
                jQuery('#country_list').after(json.html);          
                jQuery('#country_list').hide();     
                jQuery('.back').bind('click', function(e){ 
                        e.preventDefault(); 
                        jQuery('#ajax').remove();
                        jQuery('#country_list').show();
                });    
            }
         }); 
    }); 

});

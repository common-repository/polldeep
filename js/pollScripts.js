/**
 *  Premium Poll Script jQuery Application
 *  Copyright @KBRmedia - All rights Reserved
 */
jQuery(function() {

    /**
     * Sort Widgets
     **/
    jQuery("#sortable").sortable({
        placeholder: "input_placeholder",
        axis: 'y',
        start: function(event, ui) {
            before = ui.item.index();
        },
        update: function(event, ui) {
            after = ui.item.index();
            lafter = jQuery('#poll_answers li:eq(' + after + ')');
            lebefore = jQuery('#poll_answers li:eq(' + before + ')');

            lafter.replaceWith(lebefore);
            if (before > after)
                lebefore.after(lafter);
            else
                lebefore.before(lafter);
        }
    });
    /**
     * Update Font
     **/
    jQuery('select.choose_font').chosen().change(function(e, v) {
        var font = jQuery('select.choose_font option:selected').html();
        jQuery("#poll_widget").css("font-family", font + ", sans-serif");
    });
    /**
     * Update Background
     **/
    jQuery("#background").keyup(function(e) {
        if (jQuery(this).val().length < 5) return false;
        jQuery("#poll_widget").css("background-image", "url(" + jQuery(this).val() + ")");
    });
    /**
     * Update Themes
     **/
    jQuery(".themes li a").click(function(e) {
        e.preventDefault();
        var c = jQuery(this).attr("data-class"); 
        jQuery(".themes li a").removeClass("current");
        jQuery(this).addClass("current");
        jQuery("#poll_widget").removeClass();
        jQuery("#poll_widget").addClass(c);
        jQuery("#poll_theme_value").val(c);
    });
    /**
     * Embed Poll
     **/
    jQuery("#poll_embed").click(function(e) {
        e.preventDefault();
        jQuery("#extra_fields").fadeOut();
        jQuery("#poll_embed_holder").fadeToggle();
    });

    jQuery('#remove_custom_logo').click(function(e) {
        e.preventDefault();
        jQuery('#member-setting-form').append('<input type=\'hidden\' name=\'remove_custom_logo\' value=\'1\'>');
        jQuery('#member-setting-form').submit();
        //jQuery(this).text('Logo will be removed upon submission');
    });

    /**
     * Add Fields
     **/
    var ul_li_html = jQuery("#sortable li:first").html();
    var add_field_count = jQuery("#sortable li").length + 1;
    jQuery(document).on("click", "#add-field", function(e) {
        e.preventDefault();

        var add_field_count = jQuery("#sortable li").length + 1;

        jQuery("#sortable").append("<li id='poll_sort_" + add_field_count + "'>" + ul_li_html + "</li>");
        jQuery("#poll_sort_" + add_field_count + " .input-group").append('<span title="Remove Field" class="input-group-addon" onclick="jQuery(\'#poll_sort_' + add_field_count + '\').remove();jQuery(\'#poll-' + add_field_count + '\').remove();jQuery(\'#add-field\').removeClass(\'disabled\');"><i class=\'glyphicon glyphicon-remove\'></i></span>');
        jQuery("#poll_sort_" + add_field_count + " input").val('');
        jQuery("#poll_sort_" + add_field_count + " input").attr('name', 'option[' + add_field_count + ']');
        jQuery("#poll_sort_" + add_field_count + " .col-md-3").remove();
        jQuery("#poll_sort_" + add_field_count + " .col-md-9").addClass('col-md-12').removeClass('col-md-9');
        jQuery("#poll_answers").append("<li id='poll-" + add_field_count + "'><label><input type='radio' name='answer' value=''> <span>Answer " + add_field_count + "</span><div class = 'answer_label'></div></label></li>");
        icheck_reload();
        add_field_count++;
        //alert(max_count);
        if (add_field_count > max_count) {
            jQuery(this).addClass("disabled");
            return false;
        }
    });
    /**
     * Easy Tabs
     **/
    jQuery(".tabbed").hide();
    jQuery(".tabbed").filter(":first").fadeIn();
    jQuery(".tabs").click(function(e) {

        e.preventDefault();
        var id = jQuery(this).attr("data-id");
        jQuery(".tabs").parent("li").removeClass("active");
        jQuery(this).parent("li").addClass("active");
        jQuery(".tabbed").hide();
        jQuery("#" + id).fadeIn();
    });

    /* for tabbing while adding poll creation
     * /
     * */
    /**
     * Easy Tabs
     **/
    jQuery(".tabbed").hide();
    jQuery(".tabbed").filter(":first").fadeIn();
    jQuery(".mytabs").click(function(e) {
        e.preventDefault();
        var id = jQuery(this).attr("data-id");

        jQuery(".mytabs").removeClass("active");
        jQuery(this).addClass("active");
        jQuery(".tabbed").hide();

        jQuery("#" + id).fadeIn();
    });
    /**
     * Load iCheck
     **/
    icheck_reload();
    /**
     * Load Selectize
     **/
    jQuery('select').chosen();
    /**
     * Custom Radio Box
     **/
    jQuery(document).on('click', '.form_opt li a', function(e) {

        var href = jQuery(this).attr('href');
        var name = jQuery(this).parent("li").parent("ul").attr("data-id");
        var to = jQuery(this).attr("data-value");
        var callback = jQuery(this).parent("li").parent("ul").attr("data-callback");
        if (href == "#" || href == "") e.preventDefault();

        jQuery("input#" + name).val(to);
        jQuery(this).parent("li").parent("ul").find("a").removeClass("current");
        jQuery(this).addClass("current");
        if (callback !== undefined) {
            window[callback](to);
        }
    });
    /**
     * Create Answers
     **/
    jQuery("#widget_answers input[name='option[]']").each(function(e) {
        var index = jQuery("#widget_answers input").index(this) + 1;
        jQuery("#poll_answers.answers_create").append("<li id='poll-" + index + "'><label><input type='radio' name='answer' value=''> <span>Answer " + index + "</span><div class = 'answer_label'></div></label></li>");
        icheck_reload();
    });
    /**
     * Add Answers
     **/
    jQuery(document).on("keyup focusout", "#widget_answers input", function() {
        var v = jQuery(this).val().replace(/(<([^>]+)>)/ig, "");
        var index = jQuery("#widget_answers input").index(this) + 1;
        if (v.length < 1) return false;
        if (jQuery("#poll_answers #poll-" + index).length > 0) {
            jQuery("#poll_answers #poll-" + index).find("span").text(v);
        } else {
            jQuery("#poll_answers").append("<li id='poll-" + index + "'><label><input type='radio' name='answer' value='" + v + "'> <span>" + v + "</span></label></li>");
        }
        //icheck_reload();
    });
    /**
     * Show forgot password form
     **/
    jQuery(document).on('click', '#forgot-password', function() {
        //show_forgot_password();
        jQuery("#login_form").parent().slideUp("slow");
        jQuery("#forgot_form").parent().slideDown("slow");
        jQuery("#register_form").parent().slideUp("slow");
    });
    jQuery(".alert").click(function() {
        jQuery(this).fadeOut();
    });

    jQuery(document).ready(function() {
        if (location.hash == "#register") {
            show_register_form();
            jQuery(".breadcrumbs .container span").text("Get Started");
        } else if (location.hash == "#login") {
            show_login();
            jQuery(".breadcrumbs .container span").text("Login");
        } else if (location.hash == "#forgot") {
            show_forgot_password();
        } else if (location.hash == "") {
            show_login();
        }
    });

    function show_register_form() {
        jQuery("#forgot_form").parent().slideUp("slow");
        jQuery('#register_form').parent().slideDown("slow");
        jQuery('#login_form').parent().slideUp("slow");
    }

    function show_login() {
        jQuery("#forgot_form").parent().slideUp("slow");
        jQuery("#register_form").parent().slideUp("slow");
        jQuery("#login_form").parent().slideDown("slow");
    }

    jQuery(document).on("click", ".Show-blog-content", function() {
        jQuery(this).next('div.hidden-content').slideToggle();
    })
    /**
     * Show Password Field Function
     **/
    function show_forgot_password() {
        jQuery("#login_form").parent().slideUp("slow");
        jQuery("#forgot_form").parent().slideDown("slow");
        jQuery("#register_form").parent().slideUp("slow");
    }

    jQuery(document).on("click", "#login_link", function() {
        show_login();
        jQuery(".breadcrumbs .container span").text("Login");
    });

    jQuery(document).on("click", "#register_link", function() {
        show_register_form();
        jQuery(".breadcrumbs .container span").text("Get Started");
    });

    /**
     * Show login back form
     **/
    jQuery(document).on('click', '#back-to-login', function() {
        show_login();
    });

    function show_login_form() {

        jQuery("#forgot_form").slideUp("slow");
        jQuery("#login_form").slideDown("slow");
        window.location.replace("#");

        if (typeof window.history.replaceState == 'function') {
            history.replaceState({}, '', window.location.href.slice(0, -1));
        }
        return false
    }


    /**
     * Display Results
     **/
    jQuery("#view-results").click(function(e) {
        e.preventDefault();
        jQuery(".form_poll").hide();
        jQuery(".poll_results").show();
    });
    /**
     * Search for Polls
     **/
    jQuery("#poll_search_q").keyup(function(e) {
        e.preventDefault();
        var val = jQuery(this).val();
        var action = jQuery("#poll_search_form").attr("action");
        if (val.length > 3) {
            jQuery.ajax({
                type: "POST",
                url: action,
                data: "q=" + val + "&token=" + token,
                beforeSend: function() {
                    jQuery(".content").html("<img class='loader' src='" + appurl + "/static/loader.gif' style='margin:0 45%;border:0;' />");
                },
                complete: function() {
                    jQuery('img.loader').fadeOut("fast", function() {
                        jQuery(this).remove()
                    });
                },
                success: function(r) {
                    jQuery(".content").html(r);
                    jQuery(".list-group-item").removeClass("active");
                    icheck_reload();
                }
            });
        }
    });
    /**
     * Get Stats
     **/
    jQuery(document).on('click', ".get_stats", function(e) {
        e.preventDefault();
        var r = jQuery(this).attr("data-request");
        var id = jQuery(this).attr("data-id");
        var target = jQuery(this).attr("data-target");
        var val = jQuery(this).attr("data-value");
        var action = jQuery(this).attr("href");
        if (target == "this") {
            var holder = jQuery(this);
        } else {
            var holder = "#" + target;
        }
        jQuery.ajax({
            type: "POST",
            url: action,
            data: "request=" + r + "&token=" + token + "&id=" + id + "&value=" + val,
            beforeSend: function() {
                if (target != "this") {
                    jQuery(holder).hide();
                    jQuery(holder).parents(".panel").find(".panel-heading").append("<img class='loader' src='" + appurl + "/static/loader.gif' style='float:right' />");
                }
            },
            complete: function() {
                jQuery('img.loader').fadeOut("fast", function() {
                    jQuery(this).remove()
                });
            },
            success: function(r) {
                if (r !== "") {
                    if (r === "notpro") {
                        window.location = appurl;
                        return;
                    }
                    if (target == "this") {
                        jQuery(holder).replaceWith(r);
                    } else {
                        jQuery(holder).parent("div").append("<div id='ajax'>" + r + "<a href='#back' class='return btn btn-xs btn-primary'>&larr;</a></div>");
                        jQuery(".return").click(function(e) {
                            e.preventDefault();
                            jQuery("#ajax").remove();
                            jQuery(holder).fadeIn();
                        });
                    }
                }
            }
        });
    });
    if (jQuery(".poll_results").length !== 0) {
        //update_results();
    }
    // Select All
    jQuery(document).on('click', '#select_all', function(e) {
        e.preventDefault();
        jQuery('input').iCheck('toggle');
    });
    /**
     * Active Menu
     **/
    var path = location.pathname.substring(1) + location.search;
    if (path) {
        jQuery('.nav-sidebar a').removeClass("active");
        jQuery('.nav-sidebar a[hrefjQuery="' + path + '"]').addClass('active');
    }
    /**
     * OnClick Select
     **/
    jQuery(".onclick-select").on('click', function() {
        jQuery(this).select();
    })
    /**
     * Data Picker
     **/
    jQuery("#expires").datepicker({
        minDate: +1,
        dateFormat: 'yy-mm-dd'
    });
    /**
     * Show Languages
     **/
    jQuery("#show-language").click(function(e) {
        e.preventDefault();
        jQuery(".langs").fadeToggle();
    });
    jQuery(document).on('keyup focusout', '#questions', function() {
        var v = jQuery(this).val().replace(/(<([^>]+)>)/ig, "");
        if (v == "") return false;
        jQuery("#poll_widget #poll_question h3").text(v);
    });
}); // End jQuery Ready
/**
 * iCheck Load Function
 **/
function icheck_reload() {
    if (typeof icheck !== "undefined") {
        var c = icheck;
    } else {
        if (jQuery("input[type=checkbox],input[type=radio]").attr("data-class")) {
            var c = "-" + jQuery("input[type=checkbox],input[type=radio]").attr("data-class");
        } else {
            var c = "";
        }
    }
    jQuery('input').iCheck({
        checkboxClass: 'icheckbox_flat' + c,
        radioClass: 'iradio_flat' + c
    });
}

/**
 * Progress Bar Animate
 **/
function animate_progress() {
    jQuery(".progress-bar").each(function() {
        var p = jQuery(this).attr("aria-valuenow");
        jQuery(this).animate({
            width: p + "%"
        }, 1000, function() {
            jQuery(".slidedown").slideDown("slow");
        });
    });
}
/**
 * Get Results
 **/
function update_results(action, id, btn_id) {
    if (action.length == 0 || typeof action == undefined) action = jQuery(".poll_results").attr("data-action");
    if (id.length == 0 || typeof id == undefined) id = jQuery(".poll_results").attr("data-id");
    button_id = jQuery(btn_id).attr('id');
    jQuery.ajax({
        type: "POST",
        data: {
            poll_id: id,
            ajax: 'true',
            btn_ids: button_id
        },
        url: action,
        success: function(r) {
            // alert(r);

            jQuery("#show_ads").css('display', 'block');
            jQuery("#poll_widget #poll_form").remove();
            jQuery("#poll_widget").append(r);
            animate_progress();
        }
    });
}
/**
 * Custom Radio Box Callback
 **/
window.update_choice_type = function(v) {
    if (v === "0") {
        jQuery("#poll_answers input[type=checkbox]").attr("type", "radio");
        icheck_reload();
        return;
    } else if (v == "1") {
        jQuery("#poll_answers input[type=radio]").attr("type", "checkbox");
        icheck_reload();
        return;
    }
}
window.update_share = function(v) {
    if (v === "0") {
        jQuery("#poll_embed").fadeOut();
        return;
    } else if (v == "1") {
        jQuery("#poll_embed").fadeIn();
        return;
    }
}
window.update_results_button = function(v) {
    if (v === "0") {
        jQuery("#view_results_button").fadeOut();
        return;
    } else if (v == "1") {
        jQuery("#view_results_button").fadeIn();
        return;
    }
}

function reload_js(src) {
    jQuery('script[src="' + src + '"]').remove();
    jQuery('<script>').attr('src', src).appendTo('head');
}

/****** unserialize ********/
function unserialize(data) {
    var that = this,
        utf8Overhead = function(chr) {
            // https://phpjs.org/functions/unserialize:571#comment_95906
            var code = chr.charCodeAt(0);
            if (code < 0x0080) {
                return 0;
            }
            if (code < 0x0800) {
                return 1;
            }
            return 2;
        };
    error = function(type, msg, filename, line) {
        throw new that.window[type](msg, filename, line);
    };
    read_until = function(data, offset, stopchr) {
        var i = 2,
            buf = [],
            chr = data.slice(offset, offset + 1);

        while (chr != stopchr) {
            if ((i + offset) > data.length) {
                error('Error', 'Invalid');
            }
            buf.push(chr);
            chr = data.slice(offset + (i - 1), offset + i);
            i += 1;
        }
        return [buf.length, buf.join('')];
    };
    read_chrs = function(data, offset, length) {
        var i, chr, buf;

        buf = [];
        for (i = 0; i < length; i++) {
            chr = data.slice(offset + (i - 1), offset + i);
            buf.push(chr);
            length -= utf8Overhead(chr);
        }
        return [buf.length, buf.join('')];
    };
    _unserialize = function(data, offset) {
        var dtype, dataoffset, keyandchrs, keys, contig,
            length, array, readdata, readData, ccount,
            stringlength, i, key, kprops, kchrs, vprops,
            vchrs, value, chrs = 0,
            typeconvert = function(x) {
                return x;
            };

        if (!offset) {
            offset = 0;
        }
        dtype = (data.slice(offset, offset + 1))
            .toLowerCase();

        dataoffset = offset + 2;

        switch (dtype) {
            case 'i':
                typeconvert = function(x) {
                    return parseInt(x, 10);
                };
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
                break;
            case 'b':
                typeconvert = function(x) {
                    return parseInt(x, 10) !== 0;
                };
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
                break;
            case 'd':
                typeconvert = function(x) {
                    return parseFloat(x);
                };
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
                break;
            case 'n':
                readdata = null;
                break;
            case 's':
                ccount = read_until(data, dataoffset, ':');
                chrs = ccount[0];
                stringlength = ccount[1];
                dataoffset += chrs + 2;

                readData = read_chrs(data, dataoffset + 1, parseInt(stringlength, 10));
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 2;
                if (chrs != parseInt(stringlength, 10) && chrs != readdata.length) {
                    error('SyntaxError', 'String length mismatch');
                }
                break;
            case 'a':
                readdata = {};

                keyandchrs = read_until(data, dataoffset, ':');
                chrs = keyandchrs[0];
                keys = keyandchrs[1];
                dataoffset += chrs + 2;

                length = parseInt(keys, 10);
                contig = true;

                for (i = 0; i < length; i++) {
                    kprops = _unserialize(data, dataoffset);
                    kchrs = kprops[1];
                    key = kprops[2];
                    dataoffset += kchrs;

                    vprops = _unserialize(data, dataoffset);
                    vchrs = vprops[1];
                    value = vprops[2];
                    dataoffset += vchrs;

                    if (key !== i)
                        contig = false;

                    readdata[key] = value;
                }

                if (contig) {
                    array = new Array(length);
                    for (i = 0; i < length; i++)
                        array[i] = readdata[i];
                    readdata = array;
                }

                dataoffset += 1;
                break;
            default:
                error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
                break;
        }
        return [dtype, dataoffset - offset, typeconvert(readdata)];
    };

    return _unserialize((data + ''), 0)[2];
}

/*Custom Poll size functionalities*/
jQuery(document).ready(function() {   

    if(jQuery('#poll_top_of_bar_logos a img').attr('src') == false) {
        jQuery('#poll_top_of_bar_logos').addClass('theme-without-logo');
    } else {
        jQuery('#poll_top_of_bar_logos').removeClass('theme-without-logo');
    }

     var max_poll_width =     parseInt(jQuery('#custom_poll_text').attr('max'), 10);
     var min_poll_width =      parseInt(jQuery('#custom_poll_text').attr('min'), 10); 

     /*Function for disabling custom poll sizes if other than special accounts*/
         jQuery.map(['#custom_poll_size', '#custom_poll_size_300'], function(n) {
            if(jQuery(n).hasClass('popup-disabled')) {
                jQuery(n).parent('.iradio_flat').addClass('disabled');
                jQuery(n).attr('disabled', true);
            }
         }); 
     /*Function for disabling custom poll sizes if other than special accounts*/

    /*Function for handlig poll size change*/
    jQuery('.iradio_flat input ~ .iCheck-helper').on('click', function() { 
        var isCustomSIzeField = jQuery(this).closest('.iradio_flat').find('.custom_poll_size').length;
        var isDefaultPollSizeField =  jQuery(this).closest('.iradio_flat').find('.default_poll_size').length;
        var isDefaultPollSize300Field = jQuery(this).closest('.iradio_flat').find('.custom_poll_size_300').length;
        var prepareWidth = '';

        // console.log('isCustomSIzeField', isCustomSIzeField);
        // console.log('isDefaultPollSizeField', isDefaultPollSizeField);
        // console.log('isDefaultPollSize300Field', isDefaultPollSize300Field);


        if(isDefaultPollSizeField) {
            prepareWidth  = max_poll_width + 'px'; 
            jQuery('#poll_wrap').css('width', prepareWidth);
        } else if(isDefaultPollSize300Field) {
            prepareWidth = min_poll_width + 'px';
            jQuery('#poll_wrap').css('width', prepareWidth);
        } 


        if(isCustomSIzeField) {
           jQuery('.custom_poll_field').show();
        } else { 
          jQuery('.custom_poll_field').hide();
        } 
    });


    /*Function for handling custom poll size input*/
    var onChangeEvent = function(event) { 
       
        var textbox = parseInt(jQuery('#custom_poll_text').val(), 10) ;  
        if ((textbox < min_poll_width) || (textbox > max_poll_width) || isNaN(textbox)) { 
            jQuery('.error').show();
            jQuery(this).addClass('field_error');
            jQuery('.error').html("Make sure the input is between " + min_poll_width + "-" + max_poll_width + " px width"); 
        }   else {
            var selectedWidth = jQuery('#custom_poll_text').val(); 
            if(selectedWidth > 0){
                var prepareWidth = selectedWidth + 'px';
                jQuery('#poll_wrap').css('width', prepareWidth);
            }
            jQuery(this).removeClass('field_error');
            jQuery('.error').hide();

        } 
    }

    jQuery('#custom_poll_text').change(onChangeEvent).keyup(onChangeEvent); 




    jQuery('#custom_check ~ .iCheck-helper').on('click', function() {
        if( jQuery(this).prev().prop("checked") == true) {
           jQuery('.custom_fields_extra').show();
        } else {
          jQuery('.custom_fields_extra').hide();
        }
    });

    jQuery(document).on('click','.add_opt' ,function(){
             var parClass= jQuery(this).parent().parent().parent().attr("class"); 
             var tmp=parClass.split("_").pop();
             var curcount = parseInt(tmp, 10);  
             var n = jQuery('field-opt-text').length + 1; 
             var box_html = '<div><label class=\'col-sm-3\'></label><div class=\'col-sm-9 space-bot\'><input type=\'text\' id=\'box\' + n + \'\'  class=\'form-control custom_field_options\' name=\'custom_field['+curcount+'][options][]\' class=\'field-opt-text\' value=\'\'><span class=\'rem_opt\' ><i class=\'glyphicon glyphicon-minus\'></i></span> </div></div>'; 
             jQuery(this).closest('.form-group.field-options.options').append(box_html);
             return false;
    });

    jQuery(document).on('click','#add_custom_field',function(){
                var length_div = jQuery('.custom_fields_extra .custom_fields').children().length; 
                if(length_div == 1) { 
                    var tmpclass=jQuery(".custom_fields > div:first-child").attr("class");
                    var tt=tmpclass.split("_").pop();
                    var count = parseInt(tt, 10);
                    count++;
                } else { 
                    var tmpclass=jQuery(".custom_fields > div:last-child").attr("class");
                    var tt=tmpclass.split("_").pop();
                    var count = parseInt(tt, 10);
                    count++;
                }        
                var clone = jQuery(".custom_field_data").clone();
                clone.find('div.chosen-container').remove(); 
                clone.appendTo("#settings .all-custom-fields-wrap");    
                clone.find(".custom_field_options").val("");
                clone.find(".custom_field_name").val("");
                jQuery(".custom_fields > div:last-child").attr("class","custom_field_"+count);
                jQuery(".custom_field_"+count+" .field_type select").attr("id","field_type_"+count);
                jQuery(".custom_field_"+count+" .status select").attr("id","status_"+count);
                jQuery(".custom_field_"+count+" select").chosen();
                jQuery(".custom_field_"+count+" .remove-field").html("<i class='glyphicon glyphicon-remove'></i>");  
                jQuery(".custom_field_"+count+" .options input").attr("name","custom_field["+count+"][options][]");
                jQuery(".custom_field_"+count+" .custom_field_name").attr("name","custom_field["+count+"][field_name]");
                jQuery(".custom_field_"+count+" .custom_field_type").attr("name","custom_field["+count+"][field_type]");
                jQuery(".custom_field_"+count+" .custom_field_status").attr("name","custom_field["+count+"][status]"); 
            });

             jQuery(document).on('click','.rem_opt' ,function(){
                        jQuery(this).parent().css( 'background-color', '#FF6C6C' );
                        jQuery(this).parent().fadeOut('slow', function() {
                             jQuery(this).prev().remove();
                             jQuery(this).remove();
                            jQuery('.box-number').each(function(index){
                                jQuery(this).text( index + 1 );
                            });
                        });
                        return false;
             });
                    
            jQuery(document).on("click",".custom_fields .remove-field",function(){ 
                jQuery(this).parent().fadeOut("slow",function(){
                    jQuery(this).remove();
                });
            });

            /*Handling edit page on load*/
                 if(jQuery('#custom_check').attr('checked')) { 
                    jQuery('.custom_fields_extra').show();
                }
            /*Handling edit page on load*/



});

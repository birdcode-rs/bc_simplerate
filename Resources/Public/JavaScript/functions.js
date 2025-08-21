$(document).ready(function() {

    ratingSystemForm = $('.js-send-rate');
    ratingSystemHearts = $('.js-send-rate-col input[type="radio"]');
    showRateMessage = $('.simplerate_success');
    
    $noteFormFieldRequired = false;
    $noteFormField = $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][note]"]:not([type=hidden])');
    $cookieExist = false;
    $recodIdFormField = $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][recordid]"]');
    $rateFormField = $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][rate]"]');
    
    let isFeatureUser = ratingSystemForm.hasClass("user-feature");

    ratingSystemHearts.each(function() {
        $(this).on('change', function() {
            const inputRating = $(this).data('rating');

            ratingSystemHearts.each(function() {
                $(this).removeAttr('checked');
            });

            ratingSystemForm.attr('data-rated', `${inputRating}`);

            if ($noteFormField.length == 1) {
                $noteFormField.parent().addClass('active');
            }
        })
    });

    $(document).on('click', '.simplerate_success', function(e) {
        $(this).hide();
    });

    $(document).on('click', '.js-send-rate-submit', function(e) {
        if ($(".simplerate_form-col input:checked").length == 0) {
            return false;
        }
    });
 
    if ($noteFormField.length && $noteFormField.parent().attr('data-required') == 1) {
        $noteFormFieldRequired = true;
    }

    if (parseInt(ratingSystemForm.attr['data-rated']) > 0 && $noteFormField.length) {
        $noteFormField.addClass('active');
    }

    if (isFeatureUser) {
        tmpBcRateCookieValueFe = 0;
        ratingSystemForm.on("submit", function (e) {
            e.preventDefault();
            var form = $(e.target);
             
            if (parseInt(form.attr('data-rated')) > 0 && $noteFormFieldRequired == true && $noteFormField.length && $noteFormField.val().trim() === '') {
                $noteFormField.addClass('required-field');
                $noteFormField.on('change', function(){
                    $noteFormField.removeClass('required-field');
                });
                return false;
            } else if ($noteFormField.hasClass('required-field')) {
                $noteFormField.removeClass('required-field');
            }
     
            if ($.cookie('tmpBcRateCookieFe') == 1) {
                return false;
            }
    
            if (!$.cookie('tmpBcRateCookieFe')) {
                window.tmpBcRateCookieValueFe = 1;
                $.cookie('tmpBcRateCookieFe', [1]);
            } else if ($.cookie('tmpBcRateCookieFe') && $.cookie('tmpBcRateCookieFe') == 1) {
                return false;
            }
    
            $.ajax({
                async: 'true',
                type: 'POST',
                dataType: 'json',
                url: form.attr('action'),
                data: form.serialize(),
                success: function (data) {
                    if (typeof data === 'object') {
                        if (data.status == 'success') {
                            form.find('.js-rating-system-form-col input').removeAttr('checked');
                            form.find('#rating-'+ data.rate).attr('checked', 'checked');
     
                            // data.ratingResults.result
                            // data.ratingResults.roundedResult
                            // data.ratingResults.numberOfRates
                            if (data.ratingResults && $(".rating_record-data .rating_count").length) {
                                $('.rating_count').text($('.rating_count').text().replace($('.rating_count').text().match(/\d+/)[0], data.ratingResults.numberOfRates));
                                $('.average_rating strong').text($('.average_rating strong').text().replace($('.average_rating strong').text().match(/\d+/)[0], data.ratingResults.roundedResult));
                            } else {
                                $(".rating_record-data").html('<div class="rating_record-data"><span class="average_rating"><strong>'+data.ratingResults.roundedResult+'/'+5+'</strong></span></div>');
                            }
                            $('.data_rating').attr('data-rating', data.ratingResults.roundedResult);
     
                            var date = new Date();
                            var days = 365;
                            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                            showRateMessage.show();
                            
                            form.addClass('pointer-events-none');
                            form.find('button').attr('disabled', 'disabled');
                            form.find('.simplerate_form-col').addClass('rated').removeClass('not-rated');
                        } else if (data.status == 'error') {
                            if (data.message == 'missing_req_field_data') {
                                form.addClass('pointer-events-none');
                                form.find('button').attr('disabled', 'disabled');
                                form.find('.simplerate_form-col').addClass('rated').removeClass('not-rated');
                            } else {
                                form.parents('.rating-system').remove();
                            }
                        } else {
                            form.reset();
                        }
                    }
                    window.tmpBcRateCookieValueFe = 0;
                    $.removeCookie('tmpBcRateCookieFe');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(' An error occurred. Please refresh the page and try again.');
                    alert(xhr);
                    alert(ajaxOptions);
                    alert(thrownError);
                    //form.reset();
                    window.tmpBcRateCookieValueFe = 0;
                    $.removeCookie('tmpBcRateCookieFe');
                }
            });
        });
 
    } else {
        if ($.cookie('rateEntity') && $recodIdFormField.length  && $rateFormField.length) {
            $cookieExist = true;
            $cookieArray = $.cookie('rateEntity').split(',');
            $possibleValue = $recodIdFormField.val()+ "|" + $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][rate]"]:checked').val();
            if ($cookieArray.includes($possibleValue)) {
                $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][rate]"] + label').css('pointer-events','none');
            }
        }
    
        tmpBcRateCookieValue = 0;
        ratingSystemForm.on("submit", function (e) {
            e.preventDefault();
            var form = $(e.target);
             
            if (parseInt(form.attr('data-rated')) > 0 && $noteFormFieldRequired == true && $noteFormField.length && $noteFormField.val().trim() === '') {
                $noteFormField.addClass('required-field');
                $noteFormField.on('change', function(){
                    $noteFormField.removeClass('required-field');
                });
                return false;
            } else if ($noteFormField.hasClass('required-field')) {
                $noteFormField.removeClass('required-field');
            }
     
            if ($.cookie('tmpBcRateCookie') == 1) {
                return false;
            }
    
            if (!$.cookie('tmpBcRateCookie')) {
                window.tmpBcRateCookieValue = 1;
                $.cookie('tmpBcRateCookie', [1]);
            } else if ($.cookie('tmpBcRateCookie') && $.cookie('tmpBcRateCookie') == 1) {
                return false;
            }
    
            $.ajax({
                async: 'true',
                type: 'POST',
                dataType: 'json',
                url: form.attr('action'),
                data: form.serialize(),
                success: function (data) {
                    if (typeof data === 'object') {
                        if (data.status == 'success') {
                            form.find('.js-rating-system-form-col input').removeAttr('checked');
                            form.find('#rating-'+ data.rate).attr('checked', 'checked');
     
                            // data.ratingResults.result
                            // data.ratingResults.roundedResult
                            // data.ratingResults.numberOfRates
                            if (data.ratingResults && $(".rating_record-data .rating_count").length) {
                                $('.rating_count').text($('.rating_count').text().replace($('.rating_count').text().match(/\d+/)[0], data.ratingResults.numberOfRates));
                                $('.average_rating strong').text($('.average_rating strong').text().replace($('.average_rating strong').text().match(/\d+/)[0], data.ratingResults.roundedResult));
                                $('.data_rating').attr('data-rating', data.ratingResults.roundedResult);
                            } else {
                                $('.data_rating').attr('data-rating', data.ratingResults.roundedResult);
                                $(".rating_record-data").html('<div class="rating_record-data"><span class="average_rating"><strong>'+data.ratingResults.roundedResult+'/'+5+'</strong></span></div>');
                            }
                            
                            const cookieValue = `${data.recordId}|${data.rate}`;
                            const date = new Date();
                            date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 year
                            
                            let cookieArray = [];
                            
                            if ($.cookie('rateEntity')) {
                                cookieArray = $.cookie('rateEntity').split(',');
                            }
                            
                            if (!cookieArray.includes(cookieValue)) {
                                cookieArray.push(cookieValue);
                                $.cookie('rateEntity', cookieArray.join(','), { expires: date, path: '/' });
                            } else {
                                console.log('already rated');
                            }
      
                            showRateMessage.show();
                            form.addClass('pointer-events-none');
                            form.find('button').attr('disabled', 'disabled');
                            form.find('.simplerate_form-col').addClass('rated').removeClass('not-rated');
                        } else if (data.status == 'error') {
                            if (data.message == 'missing_req_field_data') {
                                form.addClass('pointer-events-none');
                                form.find('button').attr('disabled', 'disabled');
                                form.find('.simplerate_form-col').addClass('rated').removeClass('not-rated');
                            } else if (data.message == 'missing_req_field') {
                                form.parents('.rating-system').remove();
                                var eDate = new Date();
                                var eDays = 60;
                                eDate.setTime(eDate.getTime() + (eDays * 24 * 60 * 60 * 1000));
                                $.cookie('blockRateFor60', ['true'], {expires: eDate, path:'/'});
                            } else {
                                form.parents('.rating-system').remove();
                            }
                        } else  {
                            form.reset();
                        }
                    }
                    window.tmpBcRateCookieValue = 0;
                    $.removeCookie('tmpBcRateCookie');
                },
                error: function (xhr, status, error) {
                    alert(' An error occurred. Please refresh the page and try again.');
                    console.error("Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    //form.reset();
                    window.tmpBcRateCookieValue = 0;
                    $.removeCookie('tmpBcRateCookie');
                }
            });
        });
    }
});
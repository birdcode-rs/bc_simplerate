$(document).ready(function() {

    const ratingSystemForm = $('.js-send-rate');
    const ratingSystemHearts = $('.js-rating-system-form-col input[type="radio"]:checked');
    const showRateMessage = $('.simplerate_success');

    ratingSystemHearts.each(function() {
        $(this).on('change', function() {
            const inputRating = $(this).data('rating');

            ratingSystemHearts.each(function() {
                $(this).removeAttr('checked');
            })

            ratingSystemForm.attr('data-rated', `${inputRating}`);
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

    $cookieExist = false;
    $recodIdFormField = $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][recordid]"]');
    $rateFormField = $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][rate]"]');

    if ($.cookie('rateEntity') &&  $recodIdFormField.length  && $rateFormField.length) {
        $cookieExist = true;
        $cookieArray = $.cookie('rateEntity').split(',');
        $possibleValue =  $recodIdFormField.val()+ "|" + $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][rate]"]:checked').val();
        if ($cookieArray.includes($possibleValue)) {
            $('.simplerate_form [name="tx_bcsimplerate_pi1[rate][rate]"] + label').css('pointer-events','none');
        }
    }

    const tmpBcRateCookieValue = 0;
    ratingSystemForm.on("submit", function (e) {
        e.preventDefault();

        if ($.cookie('tmpBcRateCookie') == 1) {
            return false;
        }

        var form = $(e.target);
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

                        $cookieAddNewValue = data.recordId + "|" + data.rate;

                        var date = new Date();
                        var days = 365;
                        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));

                        if ($.cookie('rateEntity') !== undefined) {
                            $cookieArray = $.cookie('rateEntity').split(',');
                            if ($cookieArray.includes($cookieAddNewValue)) {
                                console.log('already rated')
                            } else {
                                $cookieArray += ','+$cookieAddNewValue;
                                $.cookie('rateEntity', [$cookieArray], {expires: date, path:'/'});
                            }
                        } else {
                            $.cookie('rateEntity', [$cookieAddNewValue], {expires: date, path:'/'});
                        }
  
                        showRateMessage.show();
                        form.addClass('pointer-events-none');
                        form.find('button').attr('disabled', 'disabled');
                        form.find('.simplerate_form-col').addClass('rated').removeClass('not-rated');
                    } else {
                        form.reset();
                    }
                }
                window.tmpBcRateCookieValue = 0;
                $.removeCookie('tmpBcRateCookie');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                form.reset();
                window.tmpBcRateCookieValue = 0;
                $.removeCookie('tmpBcRateCookie');
            }
        });
    });
});

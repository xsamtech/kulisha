/**
 * Custom scripts
 * 
 * Copyright (c) 2023 Xsam Technologies and/or its affiliates. All rights reserved.
 * 
 */
$(document).ready(function () {
    // ======================== BILIAP PLUGINS
    /* 
        UPLOAD CROPPED IMAGE
    */
    /* User avatar */
    $('.user-pic img').uploadImage('#cropModalUser', '#avatar', currentHost + '/api/user/update_avatar_picture/' + parseInt($('#userId').val()), 'user_id');

    /* 
        SWITCH THEME
    */
    $('#custom-style').switchTheme();

    /* 
        FORMAT NUMBERS THAT HAVE MORE THAN 3 DIGITS
    */
    $('#numberToFormat').numberFormatter();

    // ======================== OTHER PLUGINS
    // Auto-resize textarea
    autosize($('textarea'));
    // Summernote
    $('#summernote').summernote({
        tabsize: 2,
        height: 120,
    });

    /* jQuery Date picker */
    var currentLanguage = $('html').attr('lang');

    $('#register_birthdate').datepicker({
        dateFormat: currentLanguage.startsWith('fr') ? 'dd/mm/yy' : 'mm/dd/yy',
        onSelect: function () {
            $(this).focus();
        }
    });

    // ======================== MISCELLANEOUS
    // On select change, update user language
    $('#chooseLanguage .input-group select').on('change', function () {
        window.location = currentHost + '/language/' + $(this).val();
    });

    /* On select change, update de country phone code */
    $('#select_country').on('change', function () {
        var countryPhoneCode = $(this).val();

        $('#phone_code_text .text-value').text(countryPhoneCode);
    });

    /* On check, show/hide some blocs */
    // OFFER TYPE
    $('#donationType .form-check-input').each(function () {
        $(this).on('click', function () {
            if ($('#anonyme').is(':checked')) {
                $('#donorIdentity, #otherDonation').addClass('d-none');

            } else {
                $('#donorIdentity, #otherDonation').removeClass('d-none');
            }
        });
    });
    // TRANSACTION TYPE
    $('#paymentMethod .form-check-input').each(function () {
        $(this).on('click', function () {
            if ($('#bank_card').is(':checked')) {
                $('#phoneNumberForMoney').addClass('d-none');

            } else {
                $('#phoneNumberForMoney').removeClass('d-none');
            }
        });
    });

    /* Hover stretched link */
    $('.card-body + .stretched-link').each(function () {
        $(this).hover(function () {
            $(this).addClass('changed');

        }, function () {
            $(this).removeClass('changed');
        });
    });

    /* Mark all messages as read */
    $('#markAllRead').click(function (e) {
        e.preventDefault();

        $.ajax({
            headers: headers,
            type: 'PUT',
            contentType: 'application/json',
            url: currentHost + '/api/message/mark_all_read/' + parseInt($(this).attr('data-user-id')),
            success: function () {
                window.location.reload();
            },
            error: function (xhr, error, status_description) {
                console.log(xhr.responseJSON);
                console.log(xhr.status);
                console.log(error);
                console.log(status_description);
            }
        });
    });

    /* Run an ajax function every second */
    // setInterval(function () {
    // }, 1000);

    // Media queries management
    if ('matchMedia' in window) {
        if (window.matchMedia('(max-width: 1040px) and (min-width: 480px)').matches) {
        } else {
        }
    }

    // On window resize, rerun some functions
    $(window).on('resize', function () {
        // Media queries management
        if ('matchMedia' in window) {
            if (window.matchMedia('(max-width: 1040px) and (min-width: 480px)').matches) {
            } else {
            }
        }
    });
});

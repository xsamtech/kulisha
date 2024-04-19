/**
 * Custom script
 * 
 * Copyright (c) 2024 Xsam Technologies and/or its affiliates. All rights reserved.
 * 
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */

/**
 * If the window is webview, hide some elements
 */
if (isWebview1 || isWebview2) {
    $('.detect-webview').addClass('d-none');

} else {
    $('.detect-webview').removeClass('d-none');
}

/**
 * Set theme to light
 */
function themeLight() {
    document.documentElement.setAttribute('data-bs-theme', 'light');

    for (let i = 0; i < kulishaBrand.length; i++) {
      kulishaBrand[i].setAttribute('src', currentHost + '/assets/img/brand.png');
    }

    document.cookie = "theme=light; SameSite=None; Secure";
}

/**
 * Set theme to dark
 */
function themeDark() {
    document.documentElement.setAttribute('data-bs-theme', 'dark');

    for (let i = 0; i < kulishaBrand.length; i++) {
      kulishaBrand[i].setAttribute('src', currentHost + '/assets/img/brand-reverse.png');
    }

    document.cookie = "theme=dark; SameSite=None; Secure";
}

/**
 * Set theme to auto
 */
function themeAuto() {
    const darkThemeMq = window.matchMedia("(prefers-color-scheme: dark)");

    if (darkThemeMq.matches) {
        document.documentElement.setAttribute('data-bs-theme', 'dark');

        for (let i = 0; i < kulishaBrand.length; i++) {
            kulishaBrand[i].setAttribute('src', currentHost + '/assets/img/brand-reverse.png');
        }

    } else {
        document.documentElement.setAttribute('data-bs-theme', 'light');

        for (let i = 0; i < kulishaBrand.length; i++) {
            kulishaBrand[i].setAttribute('src', currentHost + '/assets/img/brand.png');
        }
    }

    document.cookie = "theme=auto; SameSite=None; Secure";
}

/**
 * Check string is numeric
 * 
 * @param string str 
 */
function isNumeric(str) {
    if (typeof str != "string") {
        return false
    } // we only process strings!

    return !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
        !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
}

/**
 * Get cookie by name
 * 
 * @param string cname 
 */
 function getCookie(cname) {
    let name = cname + '=';
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');

    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];

        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }

        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }

    return '';
}

/**
 * Switch between two elements visibility
 * 
 * @param string current
 * @param string element1
 * @param string element2
 * @param string message1
 * @param string message2
 */
function switchDisplay(current, form_id, element1, element2, message1, message2) {
    var _form = document.getElementById(form_id);
    var el1 = document.getElementById(element1);
    var el2 = document.getElementById(element2);

    _form.reset();
    el1.classList.toggle('d-none');
    el2.classList.toggle('d-none');

    if (el1.classList.contains('d-none')) {
        current.innerHTML = message1;
    }

    if (el2.classList.contains('d-none')) {
        current.innerHTML = message2;
    }
}

/**
 * Token writter
 * 
 * @param string id
 */
function tokenWritter(id) {
    var _val = document.getElementById(id).value;
    var _splitId = id.split('_');
    var key = event.keyCode || event.charCode;

    if (key === 8 || key === 46 || key === 37) {
        if (_splitId[2] !== '1') {
            var previousElement = document.getElementById('check_digit_' + (parseInt(_splitId[2]) - 1));

            previousElement.focus();
        }

    } else {
        var nextElement = document.getElementById('check_digit_' + (parseInt(_splitId[2]) + 1));

        if (key === 39) {
            nextElement.focus();
        }

        if (_splitId[2] !== '7') {
            if (_val !== undefined && Number.isInteger(parseInt(_val))) {
                nextElement.focus();
            }
        }
    }
}

$(function () {
    $('.navbar, .card, .btn').addClass('shadow-0');
    $('.btn').css({ textTransform: 'inherit', paddingBottom: '0.5rem' });
    $('.back-to-top').click(function (e) {
        $("html, body").animate({ scrollTop: "0" });
    });

    /* Auto-resize textarea */
    autosize($('textarea'));

    /* Perfect scrollbar */
    const ps = new PerfectScrollbar('.perfect-scrollbar', {
        wheelSpeed: 2,
        wheelPropagation: true,
        minScrollbarLength: 20
    });

    /* Bootstrap Tooltip */
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    /* jQuery scroll4ever */
    $('#scope').scroll4ever({
        trigger: '.next-page-link',
        container: '#items',
        selector: '.item',
        distance: 100,
        debug: true,
        start: function () { $('.next-page-link').html('<div class="loader"><div class="loaderBar"></div></div>'); },
        complete: function () { }
    });

    /* On select change, update de country phone code */
    $('#select_country1').on('change', function () {
        var countryData = $(this).val();
        var countryDataArray = countryData.split('-');
        // Get ID and Phone code from splitted data
        var countryId = countryDataArray[1];
        var countryPhoneCode = countryDataArray[0];

        $('#phone_code_text1 .text-value').text(countryPhoneCode);
        $('#country_id1').val(countryId);
        $('#phone_code1').val(countryPhoneCode);
    });
    $('#select_country2').on('change', function () {
        var countryData = $(this).val();
        var countryDataArray = countryData.split('-');
        // Get ID and Phone code from splitted data
        var countryId = countryDataArray[1];
        var countryPhoneCode = countryDataArray[0];

        $('#phone_code_text2 .text-value').text(countryPhoneCode);
        $('#country_id2').val(countryId);
        $('#phone_code2').val(countryPhoneCode);
    });
    $('#select_country3').on('change', function () {
        var countryData = $(this).val();
        var countryDataArray = countryData.split('-');
        // Get ID and Phone code from splitted data
        var countryId = countryDataArray[1];
        var countryPhoneCode = countryDataArray[0];

        $('#phone_code_text3 .text-value').text(countryPhoneCode);
        $('#country_id3').val(countryId);
        $('#phone_code3').val(countryPhoneCode);
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

    /* Theme management */
    // DEFAULT FACTS
    if (getCookie('theme') === 'dark') {
        themeDark();

    } else {
        if (getCookie('theme') === 'light') {
            themeLight();
        } else {
            themeAuto();
        }
    }

    // USER CHOOSES LIGHT
    $('#themeToggler .light').on('click', function (e) {
        e.preventDefault();
        $('#themeToggler .current-theme').html('<i class="bi bi-sun"></i>');
        themeLight();
    });

    // USER CHOOSES DARK
    $('#themeToggler .dark').on('click', function (e) {
        e.preventDefault();
        $('#themeToggler .current-theme').html('<i class="bi bi-moon-fill"></i>');
        themeDark();
    });

    // USER CHOOSES AUTO
    $('#themeToggler .auto').on('click', function (e) {
        e.preventDefault();
        $('#themeToggler .current-theme').html('<i class="bi bi-circle-half"></i>');
        themeAuto();
    });
});

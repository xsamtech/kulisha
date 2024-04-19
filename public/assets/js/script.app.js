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
    if (isNumeric(currentUser)) {
        $.ajax({
            headers: headers,
            type: 'GET',
            contentType: 'application/json',
            url: apiHost + '/user/' + parseInt(currentUser),
            success: function (result) {
                if (result.data.prefered_theme !== null) {
                    if (result.data.prefered_theme === 'dark') {
                        themeDark();

                    } else {
                        if (result.data.prefered_theme === 'light') {
                            themeLight();
                        } else {
                            themeAuto();
                        }
                    }

                } else {
                    themeAuto();
                }
            },
            error: function (xhr, error, status_description) {
                console.log(xhr.responseJSON);
                console.log(xhr.status);
                console.log(error);
                console.log(status_description);
            }
        });

    } else {
        if (getCookie('theme') === 'dark') {
            themeDark();

        } else {
            if (getCookie('theme') === 'light') {
                themeLight();
            } else {
                themeAuto();
            }
        }
    }

    // USER CHOOSES LIGHT
    $('#themeToggler .light').on('click', function (e) {
        e.preventDefault();
        $('#themeToggler .current-theme').html('<i class="bi bi-sun"></i>');
        themeLight();

        // If user is connected, set is theme preference
        if (isNumeric(currentUser)) {
            themeLight();

            $.ajax({
                headers: headers,
                type: 'PUT',
                contentType: 'application/json',
                url: apiHost + '/user/' + currentUser,
                data: JSON.stringify({ 'id': currentUser, 'prefered_theme': 'light' }),
                success: function () {
                    $(this).unbind('click');
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                },
                error: function (xhr, error, status_description) {
                    console.log(xhr.responseJSON);
                    console.log(xhr.status);
                    console.log(error);
                    console.log(status_description);
                }
            });
        }
    });

    // USER CHOOSES DARK
    $('#themeToggler .dark').on('click', function (e) {
        e.preventDefault();
        $('#themeToggler .current-theme').html('<i class="bi bi-moon-fill"></i>');
        themeDark();

        // If user is connected, set is theme preference
        if (isNumeric(currentUser)) {
            $.ajax({
                headers: headers,
                type: 'PUT',
                contentType: 'application/json',
                url: apiHost + '/user/' + currentUser,
                data: JSON.stringify({ 'id': currentUser, 'prefered_theme': 'dark' }),
                success: function () {
                    $(this).unbind('click');
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                },
                error: function (xhr, error, status_description) {
                    console.log(xhr.responseJSON);
                    console.log(xhr.status);
                    console.log(error);
                    console.log(status_description);
                }
            });
        }
    });

    // USER CHOOSES AUTO
    $('#themeToggler .auto').on('click', function (e) {
        e.preventDefault();
        $('#themeToggler .current-theme').html('<i class="bi bi-circle-half"></i>');
        themeAuto();

        // If user is connected, set is theme preference
        if (isNumeric(currentUser)) {
            $.ajax({
                headers: headers,
                type: 'PUT',
                contentType: 'application/json',
                url: apiHost + '/user/' + currentUser,
                data: JSON.stringify({ 'id': currentUser, 'prefered_theme': 'auto' }),
                success: function () {
                    $(this).unbind('click');
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                },
                error: function (xhr, error, status_description) {
                    console.log(xhr.responseJSON);
                    console.log(xhr.status);
                    console.log(error);
                    console.log(status_description);
                }
            });
        }
    });

    /* Crop image and send */
    // AVATAR with ajax
    $('#avatar').on('change', function (e) {
        var files = e.target.files;
        var done = function (url) {
            retrievedAvatar.src = url;
            var modal = new bootstrap.Modal(document.getElementById('cropModalUser'), { keyboard: false });

            modal.show();
        };

        if (files && files.length > 0) {
            var reader = new FileReader();

            reader.onload = function () {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });

    $(modalUser).on('shown.bs.modal', function () {
        cropper = new Cropper(retrievedAvatar, {
            aspectRatio: 1,
            viewMode: 3,
            preview: '#cropModalUser .preview',
            done: function (data) { console.log(data); },
            error: function (data) { console.log(data); }
        });

    }).on('hidden.bs.modal', function () {
        cropper.destroy();

        cropper = null;
    });

    $('#cropModalUser #crop_avatar').click(function () {
        // Ajax loading image to tell user to wait
        $('.user-image').attr('src', currentHost + '/assets/img/ajax-loading.gif');

        var canvas = cropper.getCroppedCanvas({
            width: 700,
            height: 700
        });

        canvas.toBlob(function (blob) {
            URL.createObjectURL(blob);

            var reader = new FileReader();

            reader.readAsDataURL(blob);
            reader.onloadend = function () {
                var base64_data = reader.result;
                var mUrl = apiURL + '/api/user/update_avatar_picture/' + parseInt(currentUser);
                var datas = JSON.stringify({ 'id': parseInt(currentUser), 'user_id': currentUser, 'image_64': base64_data });

                $.ajax({
                    headers: headers,
                    type: 'PUT',
                    contentType: 'application/json',
                    url: mUrl,
                    dataType: 'json',
                    data: datas,
                    success: function (res) {
                        $('.user-image').attr('src', res);
                        location.reload(true);
                    },
                    error: function (xhr, error, status_description) {
                        console.log(xhr.responseJSON);
                        console.log(xhr.status);
                        console.log(error);
                        console.log(status_description);
                    }
                });
            };
        });
    });

    // AVATAR without ajax
    $('#image_profile').on('change', function (e) {
        var files = e.target.files;
        var done = function (url) {
            retrievedImageProfile.src = url;
            var modal = new bootstrap.Modal(document.getElementById('cropModal_profile'), { keyboard: false });

            modal.show();
        };

        if (files && files.length > 0) {
            var reader = new FileReader();

            reader.onload = function () {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });

    $('#cropModal_profile').on('shown.bs.modal', function () {
        cropper = new Cropper(retrievedImageProfile, {
            aspectRatio: 1,
            viewMode: 3,
            preview: '#cropModal_profile .preview'
        });

    }).on('hidden.bs.modal', function () {
        cropper.destroy();

        cropper = null;
    });

    $('#cropModal_profile #crop_profile').on('click', function () {
        var canvas = cropper.getCroppedCanvas({
            width: 700,
            height: 700
        });

        canvas.toBlob(function (blob) {
            URL.createObjectURL(blob);
            var reader = new FileReader();

            reader.readAsDataURL(blob);
            reader.onloadend = function () {
                var base64_data = reader.result;

                $(currentImageProfile).attr('src', base64_data);
                $('#data_profile').attr('value', base64_data);
            };
        });
    });

    // RECTO
    $('#image_recto').on('change', function (e) {
        var files = e.target.files;
        var done = function (url) {
            retrievedImageRecto.src = url;
            var modal = new bootstrap.Modal(document.getElementById('cropModal_recto'), { keyboard: false });

            modal.show();
        };

        if (files && files.length > 0) {
            var reader = new FileReader();

            reader.onload = function () {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });

    $('#cropModal_recto').on('shown.bs.modal', function () {
        cropper = new Cropper(retrievedImageRecto, {
            aspectRatio: 4 / 3,
            viewMode: 3,
            preview: '#cropModal_recto .preview'
        });

    }).on('hidden.bs.modal', function () {
        cropper.destroy();

        cropper = null;
    });

    $('#cropModal_recto #crop_recto').on('click', function () {
        var canvas = cropper.getCroppedCanvas({
            width: 1280,
            height: 960
        });

        canvas.toBlob(function (blob) {
            URL.createObjectURL(blob);
            var reader = new FileReader();

            reader.readAsDataURL(blob);
            reader.onloadend = function () {
                var base64_data = reader.result;

                $(currentImageRecto).attr('src', base64_data);
                $('#data_recto').attr('value', base64_data);
            };
        });
    });

    // VERSO
    $('#image_verso').on('change', function (e) {
        var files = e.target.files;
        var done = function (url) {
            retrievedImageVerso.src = url;
            var modal = new bootstrap.Modal(document.getElementById('cropModal_verso'), { keyboard: false });

            modal.show();
        };

        if (files && files.length > 0) {
            var reader = new FileReader();

            reader.onload = function () {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });

    $('#cropModal_verso').on('shown.bs.modal', function () {
        cropper = new Cropper(retrievedImageVerso, {
            aspectRatio: 4 / 3,
            viewMode: 3,
            preview: '#cropModal_verso .preview'
        });

    }).on('hidden.bs.modal', function () {
        cropper.destroy();

        cropper = null;
    });

    $('#cropModal_verso #crop_verso').on('click', function () {
        var canvas = cropper.getCroppedCanvas({
            width: 1280,
            height: 960
        });

        canvas.toBlob(function (blob) {
            URL.createObjectURL(blob);
            var reader = new FileReader();

            reader.readAsDataURL(blob);
            reader.onloadend = function () {
                var base64_data = reader.result;

                $(currentImageVerso).attr('src', base64_data);
                $('#data_verso').attr('value', base64_data);
            };
        });
    });
});

/**
 * Load "app.blade.php" JS files
 * 
 * Copyright (c) 2024 Xsam Technologies and/or its affiliates. All rights reserved.
 * 
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
// Common variables
const navigator = window.navigator;
const currentLanguage = $('html').attr('lang');
const currentUser = $('[name="kls-visitor"]').attr('content');
const currentHost = $('[name="kls-url"]').attr('content');
const apiHost = $('[name="kls-api-url"]').attr('content');
const appRef = $('[name="kls-ref"]').attr('content');
const headers = { 'Authorization': 'Bearer ' + appRef.split('-')[0], 'Accept': $('.mime-type').val(), 'X-localization': navigator.language };
const kulishaBrand = document.querySelectorAll('.kulisha-brand');
// Modals
const modalUser = $('#cropModalUser');
// Preview images
const retrievedAvatar = document.getElementById('retrieved_image');
const retrievedImageProfile = document.getElementById('retrieved_image_profile');
const currentImageProfile = document.querySelector('#profileImageWrapper img');
const retrievedImageRecto = document.getElementById('retrieved_image_recto');
const currentImageRecto = document.querySelector('#rectoImageWrapper img');
const retrievedImageVerso = document.getElementById('retrieved_image_verso');
const currentImageVerso = document.querySelector('#versoImageWrapper img');
let cropper;
// Mobile user agent
const userAgent = navigator.userAgent;
const normalizedUserAgent = userAgent.toLowerCase();
const standalone = navigator.standalone;

const isIos = /ip(ad|hone|od)/.test(normalizedUserAgent) || navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1;
const isAndroid = /android/.test(normalizedUserAgent);
const isSafari = /safari/.test(normalizedUserAgent);
const isWebview1 = appRef.split('-')[1] != 'nai';
const isWebview2 = (isAndroid && /; wv\)/.test(normalizedUserAgent)) || (isIos && !standalone && !isSafari);

// Dynamically load JS files
function loadJS() {
    $(function () {
        $.getScript(currentHost + '/assets/addons/custom/jquery/js/jquery.min.js');
    });
}

/**
 * Load "guest.blade.php" JS files
 * 
 * Copyright (c) 2024 Xsam Technologies and/or its affiliates. All rights reserved.
 * 
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
// Common variables
const navigator = window.navigator;
const currentLanguage = $('html').attr('lang');
const currentHost = $('[name="kls-url"]').attr('content');
const apiHost = $('[name="kls-api-url"]').attr('content');
const appRef = $('[name="kls-ref"]').attr('content');
const kulishaBrand = document.querySelectorAll('.kulisha-brand');
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
        $.getScript(currentHost + '/assets/addons/social/bootstrap/dist/js/bootstrap.bundle.min.js');
        $.getScript(currentHost + '/assets/addons/social/pswmeter/pswmeter.min.js');
        $.getScript(currentHost + '/assets/js/social/functions.js');
        $.getScript(currentHost + '/assets/addons/custom/autosize/js/autosize.min.js');
        $.getScript(currentHost + '/assets/addons/custom/perfect-scrollbar/dist/perfect-scrollbar.min.js');
        $.getScript(currentHost + '/assets/addons/custom/jquery/scroll4ever/js/jquery.scroll4ever.js');
        $.getScript(currentHost + '/assets/js/load-guest-scripts.js');
        $.getScript(currentHost + '/assets/js/script.guest.js');
    });
}

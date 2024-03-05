/**
 * Load "guest.blade.php" JS files
 * 
 * Copyright (c) 2023 Xsam Technologies and/or its affiliates. All rights reserved.
 * 
 * @author Xanders Samoth
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
// Currrent Host for a well formed URL
var currentHost = location.getAttribute('port') ? location.getAttribute('protocol') + '//' + location.getAttribute('hostname') + ':' + location.getAttribute('port') : location.getAttribute('protocol') + '//' + location.getAttribute('hostname');
// Necessary headers for APIs
var headers = { 'Authorization': 'Bearer ' + document.querySelector('#rsrcToken').getAttribute('content'), 'Accept': 'application/json', 'X-localization': document.querySelector('html').getAttribute('lang') };

// Dynamically load JS files
function loadJS() {
    $(function () {
        $.getScript(currentHost + '/assets/addons/custom/jquery/js/jquery.min.js');
        $.getScript(currentHost + '/assets/addons/custom/jquery/jquery-ui/jquery-ui.min.js');
        $.getScript(currentHost + '/assets/addons/custom/bootstrap/js/bootstrap.bundle.min.js');
        $.getScript(currentHost + '/assets/addons/custom/mdb/js/mdb.min.js');
        $.getScript(currentHost + '/assets/addons/custom/dataTables/datatables.min.js');
        $.getScript(currentHost + '/assets/addons/custom/show-more/dist/js/showMore.min.js');
        $.getScript(currentHost + '/assets/addons/custom/cropper/js/cropper.min.js');
        $.getScript(currentHost + '/assets/addons/custom/croppie/croppie.min.js');
        $.getScript(currentHost + '/assets/addons/custom/autosize/js/autosize.min.js');
        $.getScript(currentHost + '/assets/addons/startup/wow/wow.min.js');
        $.getScript(currentHost + '/assets/addons/startup/easing/easing.min.js');
        $.getScript(currentHost + '/assets/addons/startup/waypoints/waypoints.min.js');
        $.getScript(currentHost + '/assets/addons/startup/counterup/counterup.min.js');
        $.getScript(currentHost + '/assets/addons/startup/owlcarousel/owl.carousel.min.js');
        $.getScript(currentHost + '/assets/js/scripts.startup.js');
        $.getScript(currentHost + '/assets/js/load-guest-scripts.js');
        $.getScript(currentHost + '/assets/addons/custom/biliap/js/biliap.cores.js');
        $.getScript(currentHost + '/assets/js/scripts.custom.js');
    });
}

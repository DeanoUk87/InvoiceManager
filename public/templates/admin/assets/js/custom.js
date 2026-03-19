(function($) {
    "use strict";
    //Left nav scroll
    $(".nano").nanoScroller();
    // Left menu collapse
    $('.left-nav-toggle a').on('click', function (event) {
        event.preventDefault();
        $("body").toggleClass("nav-toggle");
    });

    // Left menu collapse
    $('.left-nav-collapsed a').on('click', function (event) {
        event.preventDefault();
        $("body").toggleClass("nav-collapsed");
    });

    // Left menu collapse
    $('.right-sidebar-toggle').on('click', function (event) {
        event.preventDefault();
        $("#right-sidebar-toggle").toggleClass("right-sidebar-toggle");
    });

    //metis menu
    $('#menu').metisMenu({
        triggerElement: '.nav-link',
        parentTrigger: '.nav-item',
        subMenu: '.nav.flex-column',
        toggle: true
    });
    //slim scroll
    $('.scrollDiv').slimScroll({
        color: '#eee',
        size: '5px',
        height: '300px',
        alwaysVisible: false
    });

    //tooltip popover
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();

    //date and time picker
    $('.datepicker').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD h:mm'
        }
    });

    $('.date1').daterangepicker({
        singleDatePicker: true,
        timePicker: false,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.datepicker2').daterangepicker();

    $('.datepicker3').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'MM/DD/YYYY h:mm A'
        }
    });

    $('.timepicker').clockpicker({
        placement: 'top',
        align: 'left',
        donetext: 'Done',
        autoclose: false
    });

    <!-- Set default-->
    $('.timepicker2').clockpicker({
        placement: 'top',
        align: 'left',
        autoclose: true,
        'default': 'now'
    });

    //trumbowyg
    $('.editor1').trumbowyg();

    //wysihtml5
    $('.editor2').wysihtml5({
        height:'400px'
    });

    //summernote
    $('.editor3').summernote({
        height:400
    });

})(jQuery);
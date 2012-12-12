
$(function() {
    $('#before_text').wysiwyg();
    $('#after_text').wysiwyg();

    $('#date').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
    });
});


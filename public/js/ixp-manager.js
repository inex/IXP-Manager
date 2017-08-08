$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
                            
/**
 * This is default function and it's called than page is loaded.
 */

$( 'document' ).ready( function(){

    // Activate the Bootstrap menubar
    $('.dropdown-toggle').dropdown();

    $.fn.setCursorPosition = function (pos) {
        this.each(function (index, elem) {
            if (elem.setSelectionRange) {
                elem.setSelectionRange(pos, pos);
            } else if (elem.createTextRange) {
                var range = elem.createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        });
        return this;
    };
});

/**
 * Helper function that formats the file sizes
 */
function ixpFormatFileSize( bytes ) {
    if (typeof bytes !== 'number') {
        return '';
    }

    if( bytes >= 1073741824 ) {
        return ( bytes / 1073741824 ).toFixed(2) + ' GB';
    }

    if( bytes >= 1048576 ) {
        return ( bytes / 1048576 ).toFixed(2) + ' MB';
    }

    return ( bytes / 1024 ).toFixed(2) + ' KB';
}


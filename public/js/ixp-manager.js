
                            
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

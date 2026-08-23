/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * This is default function and it's called than page is loaded.
 */
$( document ).ready( function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Activate the Bootstrap menubar
    $('.dropdown-toggle').dropdown();

    // show form errors. FIXME: shouldn;t need this. Interference with help text logic...
    $( "span.help-block" ).show();

    /**
     * display / hide help sections on click on the help button
     */
    $( "#help-btn" ).click( function() {
        $( ".former-help-text" ).toggle();
        $( "#instructions-alert").toggle();
    });

    $( ".help-btn" ).click( function() {
        $( ".former-help-text" ).toggle();
        $( "#instructions-alert").toggle();
    });

    $('.tab-link-body-note').on( 'click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $('.tab-link-preview-note').on( 'click', function(e) {
        const well_div = $(this).closest('div').parent( 'div' ).find( ".well-preview" );
        e.preventDefault();

        $(this).tab('show');

        $.ajax( MARKDOWN_URL, {
            data: {
                text: $(this).closest('div').parent( 'div' ).find( "textarea" ).val()
            },
            type: 'POST'
        })
        .done( function( data ) {
            well_div.html( data.html );
        })
        .fail( function() {
            well_div.html('Error!');
        });
    });

});

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


/***** Tab Panels START *****/
$(document).on('click','.tabButton',function(e) {
    $('.tabButton').removeClass('active');
    $(this).addClass('active');
    $('.tabPanel').removeClass('active');
    const target = $(this).data('target');
    $(target).addClass('active');
});
/***** Tab Panels END *******/

// Expose globals to window


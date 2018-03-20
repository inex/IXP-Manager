/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

$( 'document' ).ready( function(){

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
        $( "p.help-block" ).toggle();
        $( "#instructions-alert").toggle();
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

/**
 * Generate a cryptographically secure random string.
 *
 * If we do not have a cryptographically secure version of a PRNG, just alert and return an empty string.
 *
 * @param length Length of string to return
 * @returns {string}
 */
function ixpRandomString( length = 12 ) {
    let result = '';

    // if we do not have a cryptographically secure version of a PRNG, just alert and return
    if( window.crypto.getRandomValues === undefined ) {
        alert( 'No cryptographically secure PRNG available.' );
    } else {
        let chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let array = new Uint32Array(length);

        window.crypto.getRandomValues(array);
        for( var i = 0; i < length; i++ ) {
            result += chars[array[i] % chars.length];
        }
    }

    return result;
}


/**
 * Replaces an AS  Number with some JS magic to invoke a BootBox.
 *
 * @param string asNumber The AS number
 *
 * @return html
 */
function ixpAsnumber( asNumber ) {
    let url = RIPE_ASN_URL + "/AS" + asNumber;
    let datas = `<table class="asn-table"><tbody>`;
    $.ajax(url)
        .done(function (data) {
            $.each(data, function (i, info) {
                datas += `<tr><td>${info.name}:</td><td>`;

                if (info.link !== undefined) {
                    let link = info.link;
                    if (!link.includes(".json")) {
                        link = link + ".json";
                    }

                    datas += ` <a target="_blank" href="${link}">${info.value}<a/>`;
                } else {
                    datas += `${info.value}`;
                }

                if (info.comment !== undefined) {
                    datas += ` # ${info.comment}`;

                }
                datas += `</td>`;
            });

            datas += `</table>`;
            bootbox.dialog({
                message: datas,
                size: "large",
                title: "AS Number Lookup",
                buttons: {
                    cancel: {
                        label: 'Close',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    }
                }
            });
        })
        .fail(function () {
            alert(`Error running ajax query for ${url}`);
            throw `Error running ajax query for ${url}`;
        })
}




/**
 * Helper function that formats the file sizes
 */
window.ixpFormatFileSize = function( bytes ) {
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
window.ixpRandomString = function( length = 12 ) {
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
 * Equivalent of PHP's htmlentities()
 * @param str
 * @returns {string}
 */
window.htmlEntities = function(str) {
    let entityMap = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '/': '&#x2F;',
        '`': '&#x60;',
        '=': '&#x3D;'
    };

    return String(str).replace(/[&<>"'`=\/]/g, function (s) {
        return entityMap[s];
    });
}


/**
 * Replaces an AS Number with some JS magic to invoke a BootBox.
 *
 * @param asNumber The AS number
 *
 */
window.ixpAsnumber = function( asNumber ) {
    let url = WHOIS_ASN_URL + "/" + asNumber;

    bootbox.dialog({
        message: '<div><p class="text-center"><i class="fa fa-spinner fa-spin text-5xl"></i></p></div>',
        size: "large",
        title: "AS Number Lookup",
        onEscape: true,
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

    $.ajax(url)
        .done(function (data) {
            const content = $('<div class="asn-table"><pre class="font-mono text-xs"></pre></div>');
            content.find('pre').text(data);

            $('.bootbox-body').html(content).scrollTop(0);
        })
        .fail(function (resp) {
            if (resp.status === 404) {
                $('.bootbox-body').text( "No information found for the requested AS" ).scrollTop(0);
            } else {
                $('.bootbox-body').text( `Error running ajax query for the requested AS` ).scrollTop(0);
                throw `Error running ajax query for ${url}`;
            }
        });
}

/**
 * Replaces a prefix with some JS magic to invoke a BootBox.
 *
 * @return html
 */
window.ixpWhoisPrefix = function( prefix, subnet = true ) {
    let parts = prefix.split('/');
    let url = encodeURI(WHOIS_PREFIX_URL + "/" + parts[0] + "/" );

    if( subnet && parts.length !== 2 ) {
        return false;
    }

    if( subnet ) {
        url = encodeURI(WHOIS_PREFIX_URL + "/" + parts[0] + "/" + parts[1] );
    }

    bootbox.dialog({
        message: '<div><p class="text-center"><i class="fa fa-spinner fa-spin text-5xl"></i></p></div>',
        size: "large",
        title: "Prefix Whois Lookup",
        onEscape: true,
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

    $.ajax(url)
        .done(function (data) {
            const content = $('<div class="prefix-table"><pre class="font-mono text-xs"></pre></div>');
            content.find('pre').text(data);

            $('.bootbox-body').html(content).scrollTop(0);
        })
        .fail(function () {
            alert(`Error running ajax query for ${url}`);
            throw `Error running ajax query for ${url}`;
        });
}
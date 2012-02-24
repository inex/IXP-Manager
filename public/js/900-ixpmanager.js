
/**
 * This is default function and it's called than page is loaded.
 */

$( 'document' ).ready( function(){

    // Activate the Bootstrap menubar
	$('.dropdown-toggle').dropdown();

	// A good percentage of pages will have message boxes - this activates them all
	$(".alert").alert();

    // Activate Bootstrap pop ups
	$("[rel=popover]").popover(
		{
			offset: 10,
			html: true
		}
	);
	
	$("[rel=tooltip]").tooltip();

});



/*****************************************************************************/
// Preferences via cookies
/*****************************************************************************/
/*
var tt_cookie_expiry_days = 90;
var tt_prefs = {
	'my_show_notes'       : false,
	'my_show_descs'       : false,
	'my_show_daily_total' : false,
	'my_show_weekly_total': false,
	'chart_settings'      : false
};

cprefs = $.jsonCookie( 'tt_prefs' );
if( cprefs != null )
	tt_prefs = cprefs;

*/

// clear a chosen dropdown
function tt_chosenClear( id ) {
    $( id ).html( "" ).val( "" );
    $( id ).trigger( "liszt:updated" );
}


// set a chosen dropdown
function tt_chosenSet( id, options, value ) {
    $( id ).html( options );

    if( value != undefined )
    	$( id ).val( value );

    $( id ).trigger( "liszt:updated" );
}




/**
 * This function creates throbber with some default parameters and return the throbber object.
 *
 * @param size  This is size of throbber in pixels.
 * @param lines This is lines count, defines how many lines per throbber.
 * @param strokewidth This is the widh of line.
 * @param fallback This is path to alternative throbber image if browser not compatible with this one.
 * @return Throbber The throbber object
 */

function tt_throbber( size, lines, strokewidth, fallback )
{
    if( !fallback )
        fallback = 'images/throbber_32px.gif';

    return new Throbber({
        "color": 'black',
        "size": size,
        "fade": 750,
        "fallback": fallback,
        "rotationspeed": 0,
        "lines": lines,
        "strokewidth": strokewidth,
        "alpha": 1
    });
}

/**
 * This function adding oss messages.
 *
 * Function defines message box. And when check where the message should be shown.
 * First it is looking for modal dialog to display oss message in it.
 * If modal dialog was not found it looks for class breadcrumb, witch is page header,
 * and insert oss message after it. And finaly if no modal dialog or breadcrumb was found
 * it insert oss message at the top of main div.
 *
 * @param msg  This is main text of oss message.
 * @param type This is type of oss message(success, error, info, etc.).
 * @param handled This is means that it came from ossAjaxErrorHandler and message can be dispalyed on modal dialog
 */
function ossAddMessage( msg, type, handled )
{
    rand = Math.floor( Math.random() * 1000000 );

    msgbox = '<div id="oss-message-' + rand + '" class="alert alert-' + type + ' fade in">\
                                <a class="close" href="#" data-dismiss="alert">×</a>\
                                    '+ msg + '</div>';

    if( $('.modal-body:visible').length && handled )
    {
        $('.modal-body').prepend( msgbox );


    }
    else if( $('.page-header').length )
    {
        $('.page-header').after( msgbox );

    }
    else if( $('.page-content').length )
    {
        $('.page-header').after( msgbox );

    }
    else if( $( ".container" ).length )
    {
        $('.container').before( msgbox );
    }
    else if( $('#main').length )
    {
        $('#main').prepend( msgbox );
    }

    $( "#oss-message-" + rand ).alert();
}

/**
 * This function is handling ajax errors.
 *
 * First function is checking if ajax was called on modal window, if so when
 * it checks if buttons are shown that mean that ajax crashed then modal dialog was
 * submitting and enabling modal dialog buttons. If buttons not visible that means
 * that ajax crashed then the content was loading so it close modal dialog.
 * After that it cheks if throbber (canvas) is showing and if so it closes that too.
 * And after that it calls ossAddMessage.
 *
 */
function ossAjaxErrorHandler( XMLHttpRequest, textStatus, errorThrown )
{
    if( $('#modal_dialog:visible').length )
    {
        if( $('#modal_dialog_save').length ){
            $('#modal_dialog_save').removeAttr( 'disabled' ).removeClass( 'disabled' );
            $('#modal_dialog_cancel').removeAttr( 'disabled' ).removeClass( 'disabled' );
        }
        else
        {
            if( dialog )
            {
                dialog.modal('hide');
            }
        }
    }

    if( $('canvas').length ){
        $('canvas').remove();
    }
    ossAddMessage( 'An unexpected error occured.', 'error', true );
}


/**
 * This function is handling toggle elements.
 *
 * First function unbinds toggle element, removes label type and pointer.
 * Then creates throbber and add it to div trobber with id throb-{toggle element id}.
 * div for throbber should be created manualy. Function only assings throbber to it. After
 * that it calls AJAX for passed URL and data. If responce ok flag ok is set to true otherwise
 * error message is show. If we have AJAX error ten ossAjaxErrorHandler calls. After AJAX error
 * or success handlers function sets back label type and pointer by flags On and Ok , kills throbber
 * end bind same function again for toggle element.
 *
 * @param e Element witch will be edited
 * @param Url This is URL for AJAX.
 * @param data Data for AJAX to post.
 * @param delElement Element witch will be removed
 */
function ossToggle( e, Url, data, delElement )
{
    e.unbind();
    if( e.hasClass( 'disabled' ) )
        return;

    var on = true;
    if( e.hasClass( 'btn-danger' ) ) {
        e.removeClass( "btn-danger" ).attr( 'disabled', 'disabled' );
    } else {
        on = false;
        e.removeClass( "btn-success" ).attr( 'disabled', 'disabled' );
    }

    var Throb = tt_throbber( 18, 10, 1, 'images/throbber_16px.gif' ).appendTo( $( '#throb-' + e.attr( 'id' ) ).get(0) ).start();

    var ok = false;

    $.ajax({
        url: Url,
        data: data,
        async: true,
        cache: false,
        type: 'POST',
        timeout: 10000,
        success: function( data ){
            if( data == "ok" ) {
                ok = true;
            } else {
                ossAddMessage( data, 'error' );
            }
        },
        error: ossAjaxErrorHandler,
        complete: function(){

            if( !ok ) on = !on;

            if( on ) {
                e.html( "Yes" ).addClass( "btn-success" ).removeAttr( 'disabled' );
            } else {
                e.html( "No" ).addClass( "btn-danger" ).removeAttr( 'disabled' );
            }

            $( '#throb-' + e.attr( 'id' ) ).html( "" );

            e.click( function( event ){
                ossToggle( e, Url, data );
            });

            if( typeof( delElement ) != undefined ) {
            	$( delElement ).hide( 'slow', function(){ $( delElement ).remove() } );;
            }

        }
    });
}

/**
 * This is simple ajax function where handling made in code.
 *
 * function calls AJAX for passed URL and data. If responce ok it return true
 * else it returns false.
 *
 * NOTE: asyn set to false
 *
 * @param string Url This is URL for AJAX.
 * @param Array data Data for AJAX to post.
 * @return bool
 */
function tt_Ajax( Url, data )
{
    var ok = false;

    $.ajax({
        url: Url,
        data: data,
        async: false,
        cache: false,
        type: 'POST',
        timeout: 10000,
        success: function( data ){
            if( data == "ok" ) {
                ok = true;
            } else {
                ossAddMessage( data, 'error' );
            }
        },
        error: ossAjaxErrorHandler
    });
    return ok;
}

/**
 * This function is opening modal dialog with contact us form.
 *
 * First it creats the throbber witch is shown while form is loading by ajax.
 * When fuction creats and opens modal dialog witch is showing throbber.
 * When form is load the throbber is replaced by it. If ajax gets en error the
 * ossAjaxErrorHandler is called.
 *
 * @param event event Its jQuery event, needed to prevent element from default actions.
 */
function tt_openModalDialog(event) {

    event.preventDefault();
    id = $( event.target ).attr( 'id' ).substr( $( event.target ).attr( 'id' ).lastIndexOf( '-' ) + 1 );

    if( id.substring( 0, 8 ) == "add_todo" )
        $( '#modal_dialog' ).addClass( 'add-todo' );
    else
        $( '#modal_dialog' ).removeClass( 'add-todo' );

    $('#modal_dialog').html( '<div id="throb" style="padding-left:230px; padding-top:175px; height:275px;"></div>' );


    var Throb = tt_throbber( 100, 20, 1.8 ).appendTo( $( '#throb' ).get(0) ).start();

    dialog = $( '#modal_dialog' ).modal( {
                backdrop: true,
                keyboard: true,
                show: true
    });


    $.ajax({
        url: $( event.target ).attr( 'href' ) ,
        async: true,
        cache: false,
        type: 'POST',
        timeout: 10000,
        success:    function(data) {
                        $('#modal_dialog').html( data );
                        $( '.modal-body' ).scrollTop( 0 );
                        $( '#modal_dialog_cancel' ).bind( 'click', function(){
                            dialog.modal('hide');
                        });
                     },

        error:     ossAjaxErrorHandler
    });
};

/**
 * Sort function used in various places
 */
function tt_sortByName(a, b)
{
	var aName = a.name.toLowerCase();
	var bName = b.name.toLowerCase();
	return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
}


//****************************************************************************
// DataTables http://datatables.net/blog/Twitter_Bootstrap_2
//****************************************************************************


/* Default class modification */
$.extend( $.fn.dataTableExt.oStdClasses, {
	"sWrapper": "dataTables_wrapper form-inline"
} );

/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
}

/* Bootstrap style pagination control */
$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
					'<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			var iListLength = 5;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
				// Remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// Add the new list items and their event handlers
				for ( j=iStart ; j<=iEnd ; j++ ) {
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							fnDraw( oSettings );
						} );
				}

				// Add / remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
} );

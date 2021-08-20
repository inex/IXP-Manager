<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    sessions  = <?= json_encode( $t->sessions, JSON_THROW_ON_ERROR ) ?>;
    custs     = <?= json_encode( $t->custs, JSON_THROW_ON_ERROR ) ?>;

    const table       = $( "#table-pm" );

    $( 'document' ).ready( function(){
        table.show();
        let columnClicked, mouseLocked   = false;

        table.delegate( 'td', 'mouseover mouseout click', function( event ) {
            console.log(this.id.indexOf( 'td-asn-' ))
             if( columnClicked ) return;
             if( this.id.indexOf( 'td-asn-' ) === 0   ) return;
             if( this.id.indexOf( 'td-name-' ) === 0  ) return;

             let yasn = this.id.substr( this.id.lastIndexOf( '-' ) + 1 );
             let xasn = this.id.substr( 3, this.id.lastIndexOf( '-' ) - 3 );

             if( event.type === 'click' ) {
                 if( mouseLocked ) {
                     table.find( "colgroup" ).removeClass( "hover" );
                     $( '.col-yasn-' + yasn ).removeClass( 'hover2' );

                     $( '[id|="td-name"]'   ).removeClass( 'highlight'  );
                     $( '[id|="td-asn"]'    ).removeClass( 'highlight'  );
                     $( '[id|="td-name"]'   ).removeClass( 'highlight2' );
                     $( '[id|="td-asn"]'    ).removeClass( 'highlight2' );
                     $( '[id|="th"]'        ).removeClass( 'highlight'  );
                     mouseLocked = false;
                 }
                 else{
                     mouseLocked = true;
                 }
             } else if( event.type === 'mouseover' && !mouseLocked ) {
                 $( "colgroup" ).eq( $(this ).index() ).addClass("hover");
                 $( '.col-yasn-' + yasn ).addClass( 'hover2'        );
                 $( '#td-name-' + xasn  ).addClass( "highlight2"    );
                 $( '#td-asn-' + xasn   ).addClass( "highlight2"    );
                 $( '#td-name-' + yasn  ).addClass( "highlight"     );
                 $( '#td-asn-' + yasn   ).addClass( "highlight"     );
                 $( '#th-as-' + yasn    ).addClass( "highlight"     );
             } else if( event.type === 'mouseout' && !mouseLocked ) {
                $("colgroup").eq( $(this).index() ).removeClass("hover");
                $( '.col-yasn-' + yasn  ).removeClass( 'hover2'     );
                $( '#td-name-' + xasn   ).removeClass( "highlight2" );
                $( '#td-asn-' + xasn    ).removeClass( "highlight2" );
                $( '#td-name-' + yasn   ).removeClass( "highlight"  );
                $( '#td-asn-' + yasn    ).removeClass( "highlight"  );
                $( '#th-as-' + yasn     ).removeClass( "highlight"  );
             }
         });


        /**
         * Highlight the column
         */
        table.delegate( 'th', 'mouseover mouseout', function( event ) {
            if( columnClicked ) return;
            if( this.id === 'th-asn' ) return;
            if( this.id === 'th-name' ) return;

            let yasn = (this.id).substring( 6 );

            if( !mouseLocked ) {
                $( '#td-name-' + yasn ).toggleClass( "highlight" );
                $( '#td-asn-' + yasn ).toggleClass( "highlight" );
                $( '#th-as-' + yasn ).toggleClass( "highlight" );
            }
        });

        /**
         * Allow to display a single column on click
         */
        $( '.th-as' ).click( function( ) {
            let sdisplay = false;
            let yasn = $(this).attr( 'data-id');

            if( columnClicked ) {
                columnClicked = false;
                $( '.th-as' ).show();
                sdisplay = true;
            }
            else {
                columnClicked = true;
            }

            $( '.th-as' ).each( function( index, element ) {
                let asn = $(this).attr( 'data-id');
                if( columnClicked ) {
                    if( asn === yasn ) {
                        return;
                    }
                }
                $( '#th-as-' + asn      ).toggle( sdisplay );
                $( '.col-yasn-' + asn   ).toggle( sdisplay );
            });

            if( yasn ) {
                $( '#td-name-' + yasn ).toggleClass( "highlight" );
                $( '#td-asn-' + yasn  ).toggleClass( "highlight" );
                $( '#th-as-' + yasn   ).toggleClass( "highlight" );
            }
        });


        /**
         * Allow to zoom/dezoom the table of result
         */
        $( '.btn-zoom' ).click( function( e ) {
            let i, zoom = 0;
            for( i = 1; i <= 5; i++ ) {
                if( $( '#tbody-pm' ).hasClass( 'zoom' + i  ) ) {
                    zoom = i;
                    break;
                }
            }

            if( zoom !== 0 ) {
                let nzoom = ( this.id === 'btn-zoom-out' ) ? zoom - 1 : zoom + 1;
                if( nzoom > 5 ) nzoom = 5;
                if( nzoom < 1 ) nzoom = 1;

                $( '.zoom' + zoom ).removeClass( 'zoom' + zoom ).addClass( 'zoom' + nzoom );
            }
        });


        /**
         * Allow to filter result (Bilateral/Route server/All peering)
         */
        $( '[id|="peer-filter"]' ).on( "click", function( e ){
            let filter = this.id.substr( this.id.lastIndexOf( '-' ) + 1 );
            $( "#ul-dd-peer a" ).removeClass( 'active' );
            $( 'td.bilateral-rs'    ).removeClass( 'peered' );
            $( 'td.bilateral-only'  ).removeClass( 'peered' );
            $( 'td.rs-only'         ).removeClass( 'peered' );
            $( 'td.bilateral-only'  ).removeClass( 'not-peered' );
            $( 'td.rs-only'         ).removeClass( 'not-peered' );

            switch( filter ) {
                case 'all':
                    $( 'td.bilateral-rs'    ).addClass( 'peered' );
                    $( 'td.bilateral-only'  ).addClass( 'peered' );
                    $( 'td.rs-only'         ).addClass( 'peered' );
                    $( '#peer-dd-text'      ).html( 'All Peerings' );
                    break;
                case 'bi':
                    $( 'td.bilateral-rs'    ).addClass( 'peered' );
                    $( 'td.bilateral-only'  ).addClass( 'peered' );
                    $( 'td.rs-only'         ).addClass( 'not-peered' );
                    $( '#peer-dd-text'      ).html( 'Bilateral Peerings' );
                    break;

                case 'rs':
                    $( 'td.bilateral-rs'    ).addClass( 'peered' );
                    $( 'td.bilateral-only'  ).addClass( 'not-peered' );
                    $( 'td.rs-only'         ).addClass( 'peered' );
                    $( '#peer-dd-text'      ).html( 'Route Server Peerings' );
                    break;
            }

            $( '#' + this.id ).addClass('active');
            $( '#peer-btn-group' ).removeClass('open');

            return false;
        });
    });
</script>
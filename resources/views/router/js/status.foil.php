<script>
    const lgEnabled = <?= $t->lgEnabled ? 'true' : 'false' ?>;

    let table = $('#router-list').on( 'init.dt', function () {
        let handles   = [ "<?= implode( '", "', $t->routersWithApi ) ?>" ];
        let versions  = { 'bird': {} };

        $('#fetched-total').html( handles.length );

        // get states
        handles.forEach( function( handle, index ) {
            if( !lgEnabled ) {
                return;
            }

            $.ajax({
                "url": "<?= url('api/v4/lg') ?>/" + handle + "/status",
                "type": "GET",
                "timeout": 60000
            })
                .done(function (data) {
                    console.log(data);
                    $('#' + handle + '-version').html( data.status.version );
                    $('#' + handle + '-api-version').html( data.api.version );
                    $('#' + handle + '-last-updated').html( moment( data.status.last_reconfig ).format( "YYYY-MM-DD HH:mm:ss" ) );
                    $('#' + handle + '-last-reboot').html( moment( data.status.last_reboot ).format( "YYYY-MM-DD HH:mm:ss" ) );

                    // reset datatables
                    table.api().rows().invalidate().draw();

                    let numFetched = $('#fetched');
                    numFetched.html( parseInt( numFetched.html() ) + 1) ;

                    // stats
                    // // FIXME - this all assumes Bird only. Hard to fix until we're not bird only.
                     if( data.status.version in versions.bird ) {
                         versions.bird[ data.status.version ]++;
                     } else {
                         versions.bird[ data.status.version ] = 1;
                     }

                    $.ajax({
                        "url": "<?= url('api/v4/lg') ?>/" + handle + "/bgp-summary",
                        "type": "GET",
                        "timeout": 60000
                    })
                    .done(function (data) {
                        let total = 0;
                        let established = 0;

                        for ( let proto in data.protocols ) {
                            if ( data.protocols[proto].state === "up" ) {
                                established++;
                            }
                            total++;
                        }

                        $( '#' + handle + '-bgp-sessions' ).html( total );
                        $( '#' + handle + '-bgp-sessions-up' ).html( established );

                        // reset datatables
                        table.api().rows().invalidate().draw();
                    })
                    .fail(function () {
                        $( '#' + handle + '-bgp-sessions' ).html( '<i class="badge badge-danger">Error</i>' );
                    });
                })
                .fail(function () {
                    let numFetchedErrors = $('#fetched-errors');
                    numFetchedErrors.html( parseInt( numFetchedErrors.html() ) + 1 );
                    $( '#' + handle + '-version' ).html( '<i class="badge badge-danger">Error</i>' );
                })
                .always( function() {
                    let numFetched       = parseInt( $( '#fetched' ).html() );
                    let numFetchedErrors = parseInt( $( '#fetched-errors' ).html() );

                    if( numFetched + numFetchedErrors === handles.length ) {
                        if (numFetchedErrors === 0) {
                            $('#fetched-alert').removeClass('alert-info').addClass('alert-success');
                        } else {
                            $('#fetched-alert').removeClass('alert-info').addClass('alert-danger');
                        }

                        let vdiv = $("#daemon-stats");

                        for( let v in versions.bird ) {
                            vdiv.append( `<span class="badge badge-secondary">${v}:&nbsp;&nbsp;${versions.bird[v]}</span>&nbsp;&nbsp;` );
                        }

                        vdiv.show();
                    }
                })
        }); // handles.forEach( function( handle, index ) {

    }).dataTable({
        stateSave: true,
        stateDuration : DATATABLE_STATE_DURATION,
        responsive: true,
        // paging is disabled as it's complicated to update off screen cells with pagination
        "paging": false
    });

</script>
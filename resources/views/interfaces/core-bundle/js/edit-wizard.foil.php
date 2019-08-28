<script>

    $( document ).ready( function() {
        $('.table-responsive-ixp-no-header').show();

        $('.table-responsive-ixp-no-header').DataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
        } );

    });

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    /**
     * check if the subnet is valid
     */
    $( "input[id|='subnet']" ).blur( function() { checkSubnet(this.id) });

    /**
     * add a new link to the core bundle
     */
    $( "#add-new-core-link" ).click(  () => { addCoreLink( ) } );

    $( "a[id|='delete-cl']" ).on( 'click', function(e){
        e.preventDefault();
        let clid = (this.id).substring(10);
        deleteElement( true , clid );
    });


    $( "a[id|='cb-delete']" ).on( 'click', function(e){
        e.preventDefault();
        deleteElement( false , null );
    });

    /**
     * Check if all the switch ports have been chosen before submit
     */
    $('#core-link-form').submit(function( e ) {
        $( ".subnet-cl" ).each(function() {
            if( $( this ).val() !== '' ){
                if( !validSubnet( $( this ).val() ) ){
                    $("#message-cl").html("<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>");
                    e.preventDefault();
                }
            }

        });
    });

    /**
     * Check if all the switch ports have been chosen before submit
     */
    $('#core-bundle-form').submit(function( e ) {
        if( $( "#subnet" ).val() !== '' ){
            if( !validSubnet( $( "#subnet" ).val() ) ){
                $("#message-cb").html("<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>");
                e.preventDefault();
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            }
        }


    });


    /**
     * Check if all the switch ports have been chosen before submit
     */
    $('#new-core-links-submit-btn').click(function() {
        $("#message-1").html('');

        if( !$( "#sp-a-1" ).val() || !$( "#sp-b-1" ).val() ){
            $( "#message-1" ).append( "<div class='alert alert-danger' role='alert'>You need to select switch ports.</div>" );
            return false;
        }

        // check if the subnet is valid
        if( $( "#subnet-1").val() ) {
            let subnet = $( "#subnet-1").val();
            if( !validSubnet( subnet ) ){
                error = true;
                $( "#message-1" ).append(  "<div class='alert alert-danger' role='alert'>The subnet " + subnet + " is not valid! </div>" );
                return false;
            }
        }
    });


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// Function:
    ///


    /**
     * Function that allow to delete a core link
     */
    function deleteElement( deletecl , clid ){

        let urlAction, elementName;

        if( deletecl ){
             urlAction = "<?= url('interfaces/core-bundle/core-link/delete/') ?>/" + clid;
             elementName = "Core link";
        } else {
             urlAction = "<?= route('core-bundle/delete', [ 'id' => $t->cb->getId() ]) ?>/";
             elementName = "Core Bundle";
        }

        bootbox.confirm({
            title: `Delete ${elementName}`,
            message: `Do you really want to delete this ${elementName}?` ,
            buttons: {
                cancel: {
                    className : "btn-secondary",
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax( urlAction, {
                        type: 'POST'
                    })
                    .done( function( data ) {
                        result = ( data.success ) ? 'success': 'danger';
                        if( result ) {
                            if( deletecl ) {
                                location.reload();
                            }else{
                                window.location.href = "<?= route('core-bundle/list') ?>/";
                            }
                        }
                    })
                    .fail( function(){
                        alert( 'Could not update notes. API / AJAX / network error' );
                        throw new Error("Error running ajax query for " + urlAction);
                    })
                    .always( function() {
                        $('#notes-modal').modal('hide');
                    });
                }
            }
        });
    }


    /**
     * event onchange on the switch port dropdowns
     */
    $(document).on('change', "[id|='sp']" ,function(e){
        e.preventDefault();
        let sid = ( this.id ).substring( 5 );
        let sside = ( this.id ).substring( 3, 4 );

        $( "#hidden-sp-" + sside + '-' + sid ).val( $("#sp-"+ sside + "-" + sid).val() );

    });

    function addCoreLink(){
        let enabled = $( "#enabled").is( ':checked' ) ? 1 : 0 ;

        $.ajax( "<?= action( 'Interfaces\CoreBundleController@addCoreLinkFrag' ) ?>", {
            data: {
                nbCoreLink      : 0,
                enabled         : enabled,
                bundleType      : $( "#type").val(),
                _token          : "<?= csrf_token() ?>"
            },
            type: 'POST'
        })
        .done( function( data ) {
            if( data.success ){
                // add the new core link form
                $('#core-links').append( data.htmlFrag );

                $('#core-links-area').css( 'opacity' , '100' );
                $('#core-links-area').show();
                $('#add-new-core-link').attr( 'disabled', 'disabled' );

                oldNbLink = $("#nb-core-links").val( );
                setSwitchPort( 'a' , 1, null, true );
                setSwitchPort( 'b' , 1, null, true);

                // set the number of core links present for the core bundle
                $("#nb-core-links").val( data.nbCoreLinks );
            }

        })
        .fail( function() {
            alert( "Error running ajax query for core-bundle/add-core-link-frag" );
            throw new Error( "Error running ajax query for core-bundle/add-core-link-frag" );
        })
    }

</script>
<script>

    /**
     * check if the subnet is valid
     */
    $( "input[id|='subnet']" ).blur( function() {
        checkSubnet(this.id);
    });

    /**
     * check if the subnet is valid and display a message
     */
    function checkSubnet( subnet ){
        $( "#"+subnet ).parent().parent().removeClass( 'has-error' );
        if( $( "#"+subnet ).val() != '' ){
            $( "#"+subnet ).parent().find('span').remove();
            if( !validSubnet( $( "#"+subnet ).val() ) ){
                $( "#"+subnet ).parent().parent().addClass( 'has-error' );
                $( "#"+subnet ).parent().append("<span class='help-block'>The subnet is not valid</span> ");
            }
            else{
                $( "#"+subnet ).parent().parent().addClass( 'has-success' );
                $( "#"+subnet ).parent().append("<span class='help-block'>The subnet is valid</span> ");
            }
        }
    }

    /**
     * Check if the subnet provided is valid
     */
    function validSubnet( subnet ){
        var address = new Address4( subnet );
        if( address.isValid() ){
            return true;
        } else {
            return false;
        }
    }

    $( "a[id|='delete-cl']" ).on( 'click', function(e){
        e.preventDefault();
        var clid = (this.id).substring(10);
        deleteCoreLink( clid );
    });

    /**
     * Check if all the switch ports have been chosen before submit
     */
    $('#core-link-form').submit(function( e ) {
        $( ".subnet-cl" ).each(function() {
            if( $( this ).val() != '' ){
                if( !validSubnet( $( this ).val() ) ){
                    $("#message-cl").html("<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>");
                    e.preventDefault();
                }
            }

        });

    });

    /**
     * Delete a core link
     */
    function deleteCoreLink( clid ){
        urlAction = "<?= url('interfaces/core-bundle/core-link/delete/') ?>/" + clid;

        bootbox.confirm({
            title: "Delete Core link",
            message: "Are you sure you want to delete this Core link ?",
            buttons: {
                cancel: {
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
                                location.reload();
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
     * Delete the core bundle and everything associated to it
     */
    function deleteCoreBundle( ){
        urlAction = "<?= route('core-bundle/delete', [ 'id' => $t->cb->getId() ]) ?>/";

        bootbox.confirm({
            title: "Delete Core Bundle",
            message: "Are you sure you want to delete this Core Bundle ?",
            buttons: {
                cancel: {
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
                                window.location.href = "<?= route('core-bundle/list') ?>/";
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
     * allow to refresh the table without reloading the page
     * reloading only a part of the DOM
     */
    function refreshDataTable() {
        $( "#area-cl").load( $(location).attr('pathname')+" #core-link-form" );
    }

    /**
     * add a new link to the core bundle
     */
    $( "#add-new-core-link" ).click( function() {
        addCoreLink( );
    });


    function addCoreLink(){
        if( $( "#enabled").is( ':checked' ) ){
            enabled = 1;
        } else{
            enabled = 0;
        }


        var ajaxCall = $.ajax( "<?= action( 'Interfaces\CoreBundleController@addCoreLinkFrag' ) ?>", {
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
                    $('#add-new-core-link').attr( 'disabled', 'disabled' );

                    oldNbLink = $("#nb-core-links").val( );
                    setSwitchPort( 'a' );
                    setSwitchPort( 'b' );

                    // set the number of core links present for the core bundle
                    $("#nb-core-links").val( data.nbCoreLinks );
                }

            })
            .fail( function() {
                throw new Error( "Error running ajax query for core-bundle/add-core-link-frag" );
                alert( "Error running ajax query for core-bundle/add-core-link-frag" );
            })
    }


    /**
     * set data to the switch port dropdown when we select a switcher
     */
    function setSwitchPort( sside ){
        switchId = $( "#switch-" + sside ).val();

        $( "#sp-" + sside + "-1" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "changed" );
        if( switchId != null && switchId != '' ){

            url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/switch-port";
            $.ajax( url , {
                type: 'POST'
            })

                .done( function( data ) {
                    var options = "<option value=\"\">Choose a switch port</option>\n";
                    $.each( data.listPorts, function( key, value ){
                        options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                    });
                    $( "#sp-" + sside + "-1" ).html( options );
                })
                .fail( function() {
                    throw new Error( "Error running ajax query for api/v4/switcher/$id/switch-port" );
                    alert( "Error running ajax query for api/v4/switcher/$id/switch-port" );


                })
                .always( function() {
                    $( "#sp-" + sside + "-1" ).trigger( "changed" );
                });
        }

    }


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
            subnet = $( "#subnet-1").val();
            if( !validSubnet( subnet ) ){
                error = true;
                $( "#message-1" ).append(  "<div class='alert alert-danger' role='alert'>The subnet " + subnet + " is not valid! </div>" );
                return false;
            }
        }
    });

</script>
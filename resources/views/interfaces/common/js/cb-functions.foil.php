<script>
    /**
     * set data to the switch port dropdown when we select a switcher
     */
    function setSwitchPort( sside, id, action, edit ){
        let datas;
        let switchId = $( "#switch-" + sside ).val();

        if( $("#nb-core-links").val() > 0 || edit ){
            $( "#sp-" + sside + "-"+ id ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "changed" );
            if( !edit ) {
                excludedSwitchPort();
            }

            if( switchId != null && switchId != '' ){
                let url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/switch-port";

                if( !edit ){
                    datas = {
                        spIdsexcluded: exludedSwitchPort
                    };
                } else {
                    datas = {
                        spIdsexcluded: []
                    };
                }

                $.ajax( url , {
                    data: datas,
                    type: 'POST'
                })

                .done( function( data ) {
                    let options = "<option value=\"\">Choose a switch port</option>\n";
                    $.each( data.listPorts, function( key, value ){
                        options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                    });
                    $( "#sp-" + sside + "-"+ id ).html( options );

                    if( action == 'addBtn' ){
                        selectNextSwitchPort( id, sside );
                    }
                })
                .fail( function() {
                    throw new Error( `Error running ajax query for ${url}` );
                    alert( `Error running ajax query for ${url}` );
                })
                .always( function() {
                    $( "#sp-" + sside + "-"+ id ).trigger( "changed" );
                });
            }
        }
    }

    /**
     * Select the switch port depending of the previous core links
     */
    function selectNextSwitchPort(id , side){
        let lastIdSwitchPort = id - 1;
        let nextValue = parseInt($( '#sp-' + side + '-'+ lastIdSwitchPort ).val()) + parseInt(1);
        if( $( "#sp-" + side + "-" + id + " option[value='"+nextValue+"']" ).length ) {
            $( '#sp-' + side + '-'+ id).val( nextValue ).trigger("changed");
        }

        $("#hidden-sp-"+ side + '-' + id).val( $("#sp-"+ side + "-" + id).val() );
    }

    /**
     * check if the subnet is valid and display a message
     */
    function checkSubnet( subnet ){
        $( "#"+subnet ).parent().parent().removeClass( 'has-error' );
        $( "#"+subnet ).parent().find('span').remove();
        if( $( "#"+subnet ).val() != '' ){
            if( !validSubnet( $( "#"+subnet ).val() ) ){
                $( "#"+subnet ).parent().parent().addClass( 'has-error' );
                $( "#"+subnet ).parent().append("<span class='help-block' style='display: block'>The subnet is not valid</span> ");
            }
            else{
                $( "#"+subnet ).parent().parent().addClass( 'has-success' );
                $( "#"+subnet ).parent().append("<span class='help-block' style='display: block' >The subnet is valid</span> ");
            }
        }
    }

    /**
     * Check if the subnet provided is valid
     */
    function validSubnet( subnet ){
        let address = new Address4( subnet );
        if( address.isValid() ){
            return true;
        } else {
            return false;
        }
    }
</script>
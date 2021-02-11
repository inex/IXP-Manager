<script>
    /**
     * set data to the switch port dropdown when we select a switcher
     */
    function setSwitchPort( sside, core_link_form, action, edit )
    {
        let datas;
        let switchId = $( "#switch-" + sside ).val();

        if( $( ".core-link-form" ).length > 0 || edit ) {
            let dd_switch_port = core_link_form.find( `.sp-${ sside }` );
            dd_switch_port.html( `<option value="">Loading please wait</option>\n` ).trigger( 'change.select2' );

            if( !edit ) {
                excludedSwitchPort();
            }

            if( switchId != null && switchId !== '' ) {
                let url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/ports";

                datas = {
                    types : [ <?= \IXP\Models\SwitchPort::TYPE_UNSET ?>, <?= \IXP\Models\SwitchPort::TYPE_CORE ?> ],
                    notAssignToPI: true,
                    piNull: true,
                    spIdsExcluded : !edit ? excludedSwitchPortSideA.concat( excludedSwitchPortSideB ) : []
                };

                $.ajax( url , {
                    data: datas,
                    method: "GET",
                    _token : "<?= csrf_token() ?>"
                })
                .done( function( data ) {
                    let options = `<option value="">Choose a switch port</option>\n`;

                    $.each( data.ports, function( key, value ){
                        options += `<option value="${value.id}">${value.name} (${value.type})</option>\n`;
                    });

                    dd_switch_port.html( options );

                    if( action === 'addBtn' ) {
                        selectNextSwitchPort( dd_switch_port, sside );
                    }
                })
                .fail( function() {
                    alert( `Error running ajax query for ${url}` );
                    throw new Error( `Error running ajax query for ${url}` );
                })
                .always( function() {
                    dd_switch_port.trigger('change.select2');
                });
            }
        }
    }

    /**
     * Insert in array all the switch port selected from the switch ports dropdown for each side (A/B)
     * in order the exclude them from the new switch port dropdown that could be added
     */
    function excludedSwitchPort( sside ) {
        $( "[id|='sp'] :selected" ).each( function() {
            if( this.value !== '' ) {
                if( sside === 'a' ) {
                    excludedSwitchPortSideA.push( this.value );
                } else {
                    excludedSwitchPortSideB.push( this.value );
                }
            }
        });
    }

    /**
     * Select the switch port depending of the previous core links
     */
    function selectNextSwitchPort( dd_switch_port , side ) {
        let previous_dd_sp = dd_switch_port.closest( '.core-link-form' ).prev().find( `.sp-${side}` );
        let sp_val = previous_dd_sp.find( ":selected" ).next().val();

        dd_switch_port.val( sp_val );
        dd_switch_port.closest( '.core-link-form' ).find( `.hidden-sp-${side}` ).val( sp_val );
    }

    /**
     * check if the subnet is valid and display a message
     */
    function checkSubnet( subnet ) {
        $( subnet ).removeClass( 'is-invalid' );
        $( subnet ).parent().find('span').remove();
        if( $( subnet ).val() !== '' ) {
            if( !validSubnet( $( subnet ).val() ) ) {
                $( subnet ).addClass( 'is-invalid' );
                $( subnet ).parent().append( `<span class='help-block invalid-feedback' style='display: block'>The subnet is not valid</span>` );
            } else {
                $( subnet ).addClass( 'is-valid' );
                $( subnet ).parent().append( `<span class='help-block valid-feedback' style='display: block' >The subnet is valid</span>` );
            }
        }
    }

    /**
     * Check if the subnet provided is valid
     */
    function validSubnet( subnet ){
        let address = new Address4( subnet );
        return address.isValid();
    }
</script>
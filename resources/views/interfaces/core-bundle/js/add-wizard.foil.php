<script>

    /**
     * format the inputs to make them well displayed
     */
    function formatInputs(){
        $('#core-links-area .col-sm-3 div.form-group label').removeClass('col-lg-2 col-sm-4').addClass('col-sm-3');
        $('#core-links-area .col-sm-3 div.form-group').children( "div" ).removeClass('col-sm-3').addClass('col-sm-8');
        $('#core-links-area .col-sm-1 div.checkbox').parent('div').css('margin-left' , '50%');
    }

    /**
     * hide the help block at loading
     */
    $('p.help-block').hide();
    $('div.help-block').hide();

    /**
     * display / hide help sections on click on the help button
     */
    $( "#help-btn" ).click( function() {
        $( "p.help-block" ).toggle();
        $( "div.help-block" ).toggle();
    });

    /**
     * display the core link form depending of the type selected
     */
    $( "#type" ).change( function() {
        displayCoreLinks( );
    });

    /**
     * check if the subnet is valid
     */
    $( "#subnet" ).blur( function() {
        $( "#subnet" ).parent().parent().removeClass( 'has-error' );
        if( this.value != '' ){
            if( !validSubnet( this.value) ){
                $( "#subnet" ).parent().parent().addClass( 'has-error' );
                $( "#subnet" ).parent().append(" <span class='help-block'>The subnet is not valid</span> ");
            }
            else{
                $( "#subnet" ).parent().find('span').remove();
            }
        }

    });

    /**
     * Display and set value to input depending on the type selected
     */
    function displayCoreLinks(){
        nbCoreLink = 0 ;
        $("#core-links-area").html( '' );
        $("#nb-core-links").val( 0 );
        $("#div-links").show( );
        actionForL3Lag();
        actionForLxLag();

        if( $( "#type" ).val() == <?= \Entities\CoreBundle::TYPE_L2_LAG ?> ) {
            $("#stp-div").slideDown();
        } else {
            $("#stp-div").slideUp();
        }

            loadBundleLinkSection( 'onChange' );
    }

    function actionForL3Lag(){
        if( $( "#type" ).val() == <?= \Entities\CoreBundle::TYPE_L3_LAG ?>){
            $( '#l3-lag-area' ).slideDown();
            required = true ;
        } else {
            $( '#l3-lag-area' ).slideUp();
            required = false ;
        }
        $( '#subnet' ).prop( 'required', required );
    }

    function actionForLxLag(){
        if( $( "#type" ).val() == <?= \Entities\CoreBundle::TYPE_L3_LAG ?> || $( "#type" ).val() == <?= \Entities\CoreBundle::TYPE_L2_LAG ?> ){
            $( '.lag-area' ).slideDown();
            required = true ;
        } else{
            $( '.lag-area' ).slideUp();
            required = false ;
        }

        $( '#pi-name-a' ).prop( 'required', required );
        $( '#pi-name-b' ).prop( 'required', required );
        $( '#pi-channel-number-a' ).prop( 'required', required );
        $( '#pi-channel-number-b' ).prop( 'required', required );
    }

    /**
     * Check if all the switch ports have been chosen before submit
     */
    $('#core-bundle-submit-btn').click(function() {
        $("[id|='message']").html('');
        for (var i = 1; i <= $("#nb-core-links").val(); i++) {
            if( !$( "#sp-a-" + i ).val() || !$( "#sp-b-" + i ).val() ){
                $( "#message-" + i ).append( "<div class='alert alert-danger' role='alert'>You need to select switch ports.</div>" );
                $('html, body').animate({ scrollTop:$("#core-link-" + i ).offset().top }, 'slow');
                return false;
            }
        }
        if( !$( "#speed" ).val() ){
            $( "#message-1" ).append( "<div class='alert alert-danger' role='alert'>You need to select a speed.</div>" );
            $('html, body').animate({ scrollTop:$("#core-link-1").offset().top }, 'slow');
            return false;
        }
    });

    /**
     * add a new link to the core bundle
     */
    $( "#add-new-core-link" ).click( function() {
        loadBundleLinkSection( 'addBtn' );
    });

    /**
     * Allow to set the value of the core bundle 'enabled' checkbox to the core links
     */
    $( "#enabled" ).click( function() {
        if($('#enabled').is(':checked')){
            $("input[id|='enabled-cl']").prop('checked', true);
        } else {
            $("input[id|='enabled-cl']").prop('checked', false);
        }
    });

    /**
     * set description value in the graph name input if this one is empty
     */
    $( "#description" ).blur( function() {
        if( $( "#graph-title" ).val() == '' ){
            $( "#graph-title" ).val( $("#description" ).val() );
        }
    });


    /**
     * Function adding a new core link form in the core links area
     */
    function loadBundleLinkSection( action ){
        nbCoreLink = $("#nb-core-links").val( );
        if( $( "#subnet-" + nbCoreLink  ).val() ){
            subnet = $( "#subnet-" + nbCoreLink  ).val();
        } else {
            subnet = null;
        }

        bundleType = $( "#type" ).val();
        error = false;

        $("#message-"+nbCoreLink).html('');

        if($('#enabled').is(':checked')){
            enabled = 1;
        } else {
            enabled = 0;
        }

        if( action == 'addBtn' ){
            // check if the switch port for side A and B are set
            if( !$( "#switch-a" ).val() ){
                error = true;
                $( "#message-" + nbCoreLink ).append( "<div class='alert alert-danger' role='alert'>You need to select a switch for side A.</div>" );
                return false;
            } else {
                if( !$( "#sp-a-" + nbCoreLink ).val() ){
                    error = true;
                    $( "#message-" + nbCoreLink ).append( "<div class='alert alert-danger' role='alert'>You need to select a switch port for side A.</div>" );
                    return false;
                }

                if( !$( "#switch-b" ).val() ){
                    error = true;
                    $( "#message-" + nbCoreLink ).append( "<div class='alert alert-danger' role='alert'>You need to select a switch for side B.</div>" );
                    return false;
                } else {
                    if( !$( "#sp-b-" + nbCoreLink ).val() ){
                        error = true;
                        $( "#message-" + nbCoreLink ).append( "<div class='alert alert-danger' role='alert'>You need to select a switch port for side B.</div>" );
                        return false;
                    }

                }
            }

            // check if there is available switch port for the selected switcher for side A and B
            if( $( "#sp-a-" + nbCoreLink + " option" ).length < 3  ||   $( "#sp-b-" + nbCoreLink + " option" ).length < 3 ){
                error = true;
                $( "#message-"  + nbCoreLink ).append(  "<div class='alert alert-danger' role='alert'>Cannot add any more core links as there are no more available ports on side a/b</div>" );
                return false;
            }

            // check if the subnet is valid
            if( subnet ) {
                if( !validSubnet( subnet ) ){
                    error = true;
                    $( "#message-"  + nbCoreLink ).append(  "<div class='alert alert-danger' role='alert'>The subnet " + subnet + " is not valid! </div>" );
                    return false;
                }
            }
        }

        if( !bundleType ){
            error = true;
            $( "#message-"  + nbCoreLink ).append(  "<div class='alert alert-danger' role='alert'>Please select a bundle type.</div>" );
            return false;
        }

        if( !error ){
            // stop the function if there the function is already running
            if( !actionRunnig ){
                actionRunnig = true;
                var ajaxCall = $.ajax( "<?= action( 'Interfaces\CoreBundleController@addCoreLinkFrag' ) ?>", {
                    data: {
                        nbCoreLink      : nbCoreLink,
                        enabled         : enabled,
                        bundleType      : bundleType,
                        _token          : "<?= csrf_token() ?>"
                    },
                    type: 'POST'
                })
                    .done( function( data ) {
                        if( data.success ){

                            // add the new core link form
                            $('#core-links-area').append( data.htmlFrag );

                            oldNbLink = $("#nb-core-links").val( );

                            // set the number of core links present for the core bundle
                            $("#nb-core-links").val( data.nbCoreLinks );

                            if( $("#switch-a").val() ){
                                setSwitchPort( 'a', $("#nb-core-links").val(), action );
                            }

                            if( $("#switch-b").val() ){
                                setSwitchPort( 'b', $("#nb-core-links").val() , action );
                            }

                            // event when the add icon has been clicked
                            if( action == 'addBtn' ){
                                // disable the switch/switchport dropdown (side A/B) of the previous core link
                                disableDropDown( oldNbLink, true);
                                // disable the delete button of the previous core link
                                $( "#remove-core-link-" + oldNbLink ).prop( 'disabled', true );

                                // set the switcher dropdown (A/B) with the value of the first core link
                                $('#switch-a' ).val( $('#switch-a' ).val() ).prop('disabled', true).trigger( "changed" );
                                $('#switch-b' ).val( $('#switch-b' ).val() ).prop('disabled', true).trigger( "changed" );

                                // set the setting from the first core link to the other
                                setSettingsToLinks( data.nbCoreLinks );

                                if( subnet ) {
                                    // set the next valid subnet to the new core link
                                    setNextSubNet( data.nbCoreLinks, subnet );
                                }
                            }


                            actionRunnig = false;
                        }

                    })
                    .fail( function() {
                        throw new Error( "Error running ajax query for core-bundle/add-core-link-frag" );
                        alert( "Error running ajax query for core-bundle/add-core-link-frag" );
                    })
            }
        }
    }




    /**
     * creating a temporary array of all the switch port selected from all the switch port dropdown
     * in order the exclude them from the new switch port dropdown that could be added
     */
    function excludedSwitchPort(){
        exludedSwitchPort = [];
        $("[id|='sp'] :selected").each( function( ) {
            if( this.value != '' ){
                exludedSwitchPort.push( this.value );
            }
        });
    }

    /**
     * set data to the switch port dropdown when we select a switcher
     */
    function setSwitchPort( sside, id, action ){
        switchId = $( "#switch-" + sside ).val();
        if( $("#nb-core-links").val() > 0 ){
            $( "#sp-" + sside + "-"+ id ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "changed" );
            excludedSwitchPort();
            if( switchId != null && switchId != '' ){
                url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/switch-port";
                datas = {
                    spIdsexcluded: exludedSwitchPort
                };

                $.ajax( url , {
                    data: datas,
                    type: 'POST'
                })

                    .done( function( data ) {
                        var options = "<option value=\"\">Choose a switch port</option>\n";
                        $.each( data.listPorts, function( key, value ){
                            options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                        });
                        $( "#sp-" + sside + "-"+ id ).html( options );

                        if( action == 'addBtn' ){
                            selectNextSwitchPort( id, sside );
                        }
                    })
                    .fail( function() {
                        throw new Error( "Error running ajax query for api/v4/switcher/$id/switch-port" );
                        alert( "Error running ajax query for api/v4/switcher/$id/switch-port" );


                    })
                    .always( function() {
                        $( "#sp-" + sside + "-"+ id ).trigger( "changed" );
                    });
            }
        }
    }

    /**
     * Copy the switch dropdown from the side A to B excluding the switch selected in side A
     */
    function setDropDownSwitchSideB( sid ){
        $( "#switch-b" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "changed" );
        var options = "";
        $( "#switch-a option").each( function( ) {
            if( this.value != $( "#switch-a" ).val() ){
                options += "<option value=\"" + this.value + "\">" + this.text + " </option>\n";
            }
        });
        $( "#switch-b" ).html( options ).trigger( "changed" );
    }

    /**
     * Disable the switch/switch port of the both side
     */
    function disableDropDown( id, disable){
        $( "#switch-a" ).prop('disabled', disable).trigger( "changed" );
        $( "#switch-b" ).prop('disabled', disable).trigger( "changed" );
        $( "#sp-a-"+ id ).prop('disabled', disable).trigger( "changed" );
        $( "#sp-b-"+ id ).prop('disabled', disable).trigger( "changed" );
    }

    /**
     * Select the switch port depending of the previous core links
     */
    function selectNextSwitchPort(id , side){
        lastIdSwitchPort = id - 1;
        nextValue = parseInt($( '#sp-' + side + '-'+ lastIdSwitchPort ).val()) + parseInt(1);
        if( $( "#sp-" + side + "-" + id + " option[value='"+nextValue+"']" ).length ) {
            $( '#sp-' + side + '-'+ id).val( nextValue ).trigger("changed");
        }

        $("#hidden-sp-"+ side + '-' + id).val( $("#sp-"+ side + "-" + id).val() );
    }

    /**
     * Set the BFD and ENABLED input with the first core link value
     */
    function setSettingsToLinks( id ){
        if($( '#bfd-1' ).is(':checked')){
            $( '#bfd-'+ id ).prop('checked', true);
        }

        if($( '#enabled-cl-1' ).is(':checked')){
            $( '#enabled-cl-'+ id ).prop('checked', true);
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

    /**
     * set the next valid subnet to the new corelink form
     */
    function setNextSubNet( id , subnet ){
        var address = new Address4( subnet );
        if( !address.isValid() ){
            $( "#message-" + id ).html( "<div class='alert alert-danger' role='alert'>The subnet '" + subnet + "' is not valid!</div>" );
            return false;
        }

        var nextAddressAsInt = parseInt( address.endAddress().bigInteger() ) + 1;
        var nextAddressStart = Address4.fromBigInteger( nextAddressAsInt );
        var nextAddress      = new Address4( nextAddressStart.address + '/' + address.subnetMask );
        $("#subnet-" + id ).val( nextAddress.address );
    }



    /**
     * event onchange on the switch dropdowns
     */
    $(document).on('change', "[id|='switch']" ,function(e){
        e.preventDefault();
        var sside = ( this.id ).substring( 7 );
        setSwitchPort( sside , $("#nb-core-links").val() );
        if( sside == 'a'){
            setDropDownSwitchSideB( );
            $( "#sp-b-1" ).html( "<option value=\"\">Choose a switch port</option>\n" ).trigger( "changed" );
        }
    });


    /**
     * event onchange on the switch port dropdowns
     */
    $(document).on('change', "[id|='sp']" ,function(e){
        e.preventDefault();
        var sid = ( this.id ).substring( 5 );
        var sside = ( this.id ).substring( 3, 4 );

        $( "#hidden-sp-" + sside + '-' + sid ).val( $("#sp-"+ sside + "-" + sid).val() );

        excludedSwitchPort();
    });
</script>
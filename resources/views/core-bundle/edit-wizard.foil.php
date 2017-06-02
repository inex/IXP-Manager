<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'core-bundle/list' )?>">Core Bundles</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Add Core Bundles Wizard</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class=" btn-group btn-group-xs" role="group">

        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>
    <div class="well">
        <?= Former::open()->method( 'POST' )
            ->action( url( 'virtualInterface/storeWizard' ) )
            ->customWidthClass( 'col-sm-3' )
        ?>
        <div>
            <h3>
                General Core Bundle Settings :
            </h3>
            <hr>

            <?= Former::text( 'description' )
                ->label( 'Description' )
                ->placeholder( 'Description' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::text( 'graph-name' )
                ->label( 'Graph Name' )
                ->placeholder( 'Graph Name' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::select( 'type' )
                ->label( 'Type' )
                ->fromQuery( $t->types, 'name' )
                ->placeholder( 'Choose Core Bundle type' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::checkbox( 'enabled' )
                ->id( 'enabled' )
                ->label( 'Enabled' )
                ->unchecked_value( 0 )
                ->blockHelp( "" );
            ?>
        </div>
        <br/>
        <div class="well help-block">
            You have a number of options when assigning a port:

            <ul>
                <li>
                    If you have pre-wired the patch panel to a port, enter the switch and port here. So long as no customer has been
                    assigned to the switch port, the patch panel port will remain available but will be marked as connected to
                    the given switch port in the patch panel port list.
                </li>
            </ul>

            If you need to reset these fields, just click either of the <em>Reset</em> button.
        </div>

        <div>
            <h3>
                Core Links :

                <button style="float: right; margin-right: 20px" id="add-new-core-link" type="button" class=" btn-xs btn btn-default" href="#" title="Add Core link">
                    <span class="glyphicon glyphicon-plus"></span>
                </button>

            </h3>
            <div class="col-sm-12" id="core-links-area">

            </div>

        </div>

        <?= Former::hidden( 'nbCoreLinks' )
            ->id( 'nbCoreLinks')
            ->value( 0 )
        ?>

        <?=Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( url( 'core-bundle/list/' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->id('btn-group');?>

        <?= Former::close() ?>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script type="text/javascript" src="<?= asset( '/bower_components/ip-address/dist/ip-address-globals.js' ) ?>"></script>
    <script>

        $(document).ready( function() {
            actionRunnig = false;
            exludedSwitchPort = [];
            loadBundleLinkSection( 'onLoad' );
            

        });

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
            if( $( "#graph-name" ).val() == '' ){
                $( "#graph-name" ).val( $("#description" ).val() );
            }
        });


        /**
         * Function adding a new core link form in the core links area
         */
        function loadBundleLinkSection( action ){
            nbCoreLink = $( "#nbCoreLinks" ).val();
            error = false;
            $("#message-"+nbCoreLink).html('');

            if($('#enabled').is(':checked')){
                enabled = 1;
            } else{
                enabled = 0;
            }

            if( action == 'addBtn' ){
                // check if the switch port for side A and B are set
                if( !$( "#sp-a-" + nbCoreLink ).val()  ||  !$( "#sp-b-" + nbCoreLink ).val()){
                    error = true;
                    $( "#message-" + nbCoreLink ).html( "<div class='alert alert-danger' role='alert'>You need to select a switch port for both side (A and B)</div>" );
                }

                // check if there is available switch port for the selected swticher for side A and B
                if( $( "#sp-a-" + nbCoreLink + " option" ).length < 3  ||   $( "#sp-b-" + nbCoreLink + " option" ).length < 3 ){
                    error = true;
                    $( "#message-"  + nbCoreLink ).html(  "<div class='alert alert-danger' role='alert'>Cannot add any more core links as there are no more available ports on side a/b</div>" );
                }


            }

            if( !error ){
                // stop the function if there the function is already running
                if( !actionRunnig ){
                    actionRunnig = true;
                    var ajaxCall = $.ajax( "<?= url( 'core-bundle/add-core-link-frag' ) ?>", {
                        data: {
                            nbCoreLink  : nbCoreLink,
                            enabled     : enabled,
                            _token : "<?= csrf_token() ?>"
                        },
                        type: 'POST'
                    })
                    .done( function( data ) {
                        if( data.success ){
                            // disable the switch/switchport dropdown (side A/B) of the previous core link
                            disableDropDown(nbCoreLink, true);
                            // add the new core link form
                            $('#core-links-area').append( data.htmlFrag );
                            // set the number of core links present for the core bundle
                            $('#nbCoreLinks').val( data.nbCoreLinks );

                            // event when the add icon has been clicked
                            if( action == 'addBtn' ){
                                // disable the delete button of the previous core link
                                $( "#remove-core-link-" + nbCoreLink ).prop( 'disabled', true );
                                // set the dropdown of side B
                                setDropDownSwitchSideB( data.nbCoreLinks );
                                // set the switcher dropdown (A/B) with the value of the first core link
                                $('#s-a-' + data.nbCoreLinks).val( $('#s-a-1' ).val() ).prop('disabled', true).trigger( "chosen:updated" );
                                $('#s-b-' + data.nbCoreLinks).val( $('#s-b-1' ).val() ).prop('disabled', true).trigger( "chosen:updated" );

                                // set the switch port dropdown value
                                setSwitchPort( data.nbCoreLinks, 'a', action );
                                setSwitchPort( data.nbCoreLinks, 'b',  action );
                                // set the setting from the first core link to the other
                                setSettingsToLinks( data.nbCoreLinks );
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
         * event onchange on the switch dropdowns
         */
        $(document).on('change', "[id|='s']" ,function(e){
            e.preventDefault();
            var sid = ( this.id ).substring( 4 );
            var sside = ( this.id ).substring( 2, 3 );
            setSwitchPort( sid, sside );
        });


        /**
         * event onchange on the switch port dropdowns
         */
        $(document).on('change', "[id|='sp']" ,function(e){
            e.preventDefault();
            var sid = ( this.id ).substring( 5 );
            var sside = ( this.id ).substring( 3, 4 );

            if( sside == 'a' && sid == 1 ) {
                $( "#sp-b-" + sid ).html( "<option value=\"\">Choose a switch port</option>\n" ).trigger( "chosen:updated" );
                setDropDownSwitchSideB( sid );
            }

            excludedSwitchPort();
        });


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
        function setSwitchPort( sid, sside, action ){
            $( "#sp-" + sside + "-"+ sid ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
            switchId = $( "#s-" + sside + "-"+ sid ).val();
            excludedSwitchPort();
            if( switchId != null && switchId != '' ){
                url = "<?= url( '/api/v4/switcher' )?>/" + switchId + "/switch-port";
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
                        $( "#sp-" + sside + "-"+ sid ).html( options );

                        if( action == 'addBtn' ){
                            selectNextSwitchPort( sid, sside );
                        }
                    })
                    .fail( function() {
                        throw new Error( "Error running ajax query for api/v4/switcher/$id/switch-port" );
                        alert( "Error running ajax query for api/v4/switcher/$id/switch-port" );

                    })
                    .always( function() {
                        $( "#sp-" + sside + "-"+ sid ).trigger( "chosen:updated" );
                    });
            }


        }

        /**
         * Copy the switch dropdown from the side A to B excluding the switch selected in side A
         */
        function setDropDownSwitchSideB( sid ){
            $( "#s-b-"+ sid ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
            var options = "";
            $( "#s-a-"+ sid + ' option').each( function( ) {
                if( this.value != $( "#s-a-"+ sid ).val() ){
                    options += "<option value=\"" + this.value + "\">" + this.text + " </option>\n";
                }
            });
            $( "#s-b-"+ sid ).html( options ).trigger( "chosen:updated" );
        }

        /**
         * Disable the switch/switch port of the both side
         */
        function disableDropDown( id, disable){
            $( "#s-a-"+ id  ).prop('disabled', disable).trigger( "chosen:updated" );
            $( "#s-b-"+ id  ).prop('disabled', disable).trigger( "chosen:updated" );
            $( "#sp-a-"+ id ).prop('disabled', disable).trigger( "chosen:updated" );
            $( "#sp-b-"+ id ).prop('disabled', disable).trigger( "chosen:updated" );
        }

        /**
         * Select the switch port depending of the previous core links
         */
        function selectNextSwitchPort(id , side){
            lastIdSwitchPort = id - 1;
            nextValue = parseInt($( '#sp-' + side + '-'+ lastIdSwitchPort ).val()) + parseInt(1);
            if( $( "#sp-" + side + "-" + id + " option[value='"+nextValue+"']" ).length ) {
                $( '#sp-' + side + '-'+ id).val( nextValue ).trigger("chosen:updated");
            }
        }

        /**
         * Set the RFD and ENABLED input with the first core link value
         */
        function setSettingsToLinks( id ){
            if($( '#rfd-1' ).is(':checked')){
                $( '#rfd-'+ id ).prop('checked', true);
            }

            if($( '#enabled-cl-1' ).is(':checked')){
                $( '#enabled-cl-'+ id ).prop('checked', true);
            }
        }

    </script>
<?php $this->append() ?>
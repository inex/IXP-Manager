<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action( 'Interfaces\VirtualInterfaceController@list' )?>">(Virtual) Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Edit Physical Interface</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route( 'interfaces/physical/list' )?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

    <div class="well col-sm-12">
        <?= Former::open()->method( 'POST' )
            ->action( action( 'Interfaces\PhysicalInterfaceController@store' ) )
            ->customWidthClass( $t->otherPICoreLink || $t->enableFanout ? 'col-sm-6' : 'col-sm-3' )
        ?>
        <div class="<?= $t->otherPICoreLink || $t->enableFanout ? 'col-sm-6': 'col-sm-12'  ?>">
            <h3>
                Physical Interface Settings
            </h3>
            <hr>
            <?= Former::select( 'switch' )
                ->label( 'Switch' )
                ->fromQuery( $t->switches, 'name' )
                ->placeholder( 'Choose a Switch' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'switch-port' )
                ->label( 'Switch Port' )
                ->fromQuery( $t->switchports, 'name' )
                ->placeholder( 'Choose a switch port' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'status' )
                ->label( 'Status' )
                ->fromQuery( \Entities\PhysicalInterface::$STATES, 'name' )
                ->placeholder( 'Choose a status' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'speed' )
                ->label( 'Speed' )
                ->fromQuery( \Entities\PhysicalInterface::$SPEED, 'name' )
                ->placeholder( 'Choose a speed' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'duplex' )
                ->label( 'Duplex' )
                ->fromQuery( \Entities\PhysicalInterface::$DUPLEX, 'name' )
                ->placeholder( 'Choose Duplex' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::checkbox( 'autoneg-label' )
                ->label( 'Auto-Negotiation Enabled' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( "" ); ?>

            <?= Former::number( 'monitorindex' )
                ->label( 'Monitor Index' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::textarea( 'notes' )
                ->label( 'Notes' )
                ->rows( 10 )
                ->style( 'width:500px' )
                ->blockHelp( ' ' );
            ?>
        </div>

        <?php if( $t->otherPICoreLink ): ?>
            <div class="col-sm-6">
                <h3>
                    Other Side of the Core Link
                </h3>
                <hr>

                <?= Former::select( 'switch-b' )
                    ->label( 'Switch' )
                    ->fromQuery( $t->switches, 'name' )
                    ->placeholder( 'Choose a Switch' )
                    ->addClass( 'chzn-select' )
                    ->disabled( true)
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'switch-port-b' )
                    ->label( 'Switch Port' )
                    ->fromQuery( $t->ee( $t->otherPICoreLink->getSwitchPort()->getName() ) , 'name' )
                    ->placeholder( 'Choose a switch port' )
                    ->addClass( 'chzn-select' )
                    ->disabled( true)
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'status-b' )
                    ->label( 'Status' )
                    ->fromQuery( \Entities\PhysicalInterface::$STATES, 'name' )
                    ->placeholder( 'Choose a status' )
                    ->addClass( 'chzn-select' )
                    ->disabled( true)
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'speed-b' )
                    ->label( 'Speed' )
                    ->fromQuery( \Entities\PhysicalInterface::$SPEED, 'name' )
                    ->placeholder( 'Choose a speed' )
                    ->addClass( 'chzn-select' )
                    ->disabled( true)
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'duplex-b' )
                    ->label( 'Duplex' )
                    ->fromQuery( \Entities\PhysicalInterface::$DUPLEX, 'name' )
                    ->placeholder( 'Choose Duplex' )
                    ->addClass( 'chzn-select' )
                    ->disabled( true)
                    ->blockHelp( '' );
                ?>

                <?= Former::checkbox( 'autoneg-label-b' )
                    ->label( 'Auto-Negotiation Enabled' )
                    ->unchecked_value( 0 )
                    ->value( 1 )
                    ->disabled( true)
                    ->blockHelp( "" ); ?>

                <?= Former::number( 'monitorindex-b' )
                    ->label( 'Monitor Index' )
                    ->disabled( true)
                    ->blockHelp( 'help text' );
                ?>

                <?= Former::textarea( 'notes-b' )
                    ->label( 'Notes' )
                    ->rows( 10 )
                    ->style( 'width:500px' )
                    ->disabled( true)
                    ->blockHelp( ' ' );
                ?>

            </div>
        <?php endif; ?>

        <?php if( $t->enableFanout ): ?>
        <div class="col-sm-6">
            <h3>
                Fanout Port
            </h3>
            <hr>
            <?= Former::checkbox( 'fanout' )
                ->label( 'Associate a fanout port' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( "" ); ?>

            <div id="fanout-area" style="display: none">
                <?= Former::select( 'switch-fanout' )
                    ->label( 'Switch Fanout' )
                    ->fromQuery( $t->switches, 'name' )
                    ->placeholder( 'Choose a Switch' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'switch-port-fanout' )
                    ->label( 'Switch Port Fanout' )
                    ->placeholder( 'Choose a switch port' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::number( 'monitorindex-fanout' )
                    ->label( 'Monitor Index fanout' )
                    ->blockHelp( 'help text' );
                ?>

                <?= Former::hidden( 'sp-fanout' )
                    ->value( $t->spFanout )
                ?>
            </div>


        </div>
        <?php endif; ?>

        <?= Former::hidden( 'id' )
            ->value( $t->pi ? $t->pi->getId() : false )
        ?>

        <?= Former::hidden( 'viid' )
            ->value( $t->pi ? $t->pi->getVirtualInterface()->getId() : $t->vi->getId() )
        ?>

        <?= Former::hidden( 'cb' )
            ->value( $t->cb  )
        ?>

        <?= Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( route ( 'interfaces/physical/list' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->id('btn-group');?>

        <?= Former::close() ?>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready( function() {
            <?php if( $t->enableFanout ): ?>
                checkFanout();
                $( '#fanout' ).on( 'click', checkFanout );
                $( '#switch-fanout' ).change();
            <?php endif; ?>
        });

        <?php if( $t->enableFanout ): ?>
            function checkFanout(){
                if( $( '#fanout' ).prop( 'checked' ) )
                    $( '#fanout-area' ).slideDown();
                else
                    $( '#fanout-area' ).slideUp();

            }

        <?php endif; ?>

        $( "#switch" ).on( 'change', updateSwitchPort );
        $( "#switch-fanout" ).on( 'change', updateSwitchPort );

        function updateSwitchPort( ){
            var type = "";
            var arrayType = [ <?= \Entities\SwitchPort::TYPE_UNSET ?>,  <?= \Entities\SwitchPort::TYPE_PEERING ?>];
            if( $( this ).attr( "id" ).substr( -6 ) == "fanout" )
            {
                type = "-fanout";
                arrayType = [ <?= \Entities\SwitchPort::TYPE_UNSET ?>, <?= \Entities\SwitchPort::TYPE_FANOUT ?> ];
            }

            $( "#switch-port" + type ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

            switchId = $( "#switch" + type ).val();

            url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/ports";

            $.ajax( url )
                .done( function( data ) {
                    options = "<option value=\"\">Choose a switch port</option>\n";
                    $.each( data.switchports, function( key, port ){
                        if( port.pi_id == null &&  arrayType.indexOf( port.sp_type ) != -1 ) {
                            options += "<option value=\"" + port.sp_id + "\">" + port.sp_name + " (" + port.sp_type_name + ")</option>\n";
                        }
                    });


                    ///////// select the good sp ID

                    $( "#switch-port" + type ).html( options );
                })
                .fail( function() {
                    options = "<option value=\"\">ERROR</option>\n";
                    $( "#switch-port" + type ).html( options );
                    alert( "Error running ajax query for " + url );
                    throw new Error( "Error running ajax query for " + url );
                })
                .always( function() {
                    $( "#switch-port" + type ).trigger( "chosen:updated" );
                });
        }



        /**
         * hide the help block at loading
         */
        $('p.help-block').hide();

        /**
         * display / hide help sections on click on the help button
         */
        $( "#help-btn" ).click( function() {
            $( "p.help-block" ).toggle();
        });


    </script>
<?php $this->append() ?>
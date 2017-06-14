<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'virtualInterface/list' )?>">(Virtual) Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Edit Physical Interface</li>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

    <div class="well">
        <?= Former::open()->method( 'POST' )
            ->action( url( 'physicalInterface/store' ) )
            ->customWidthClass( 'col-sm-3' )
        ?>
        <div>
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
                ->fromQuery( $t->sp, 'name' )
                ->placeholder( 'Choose a switch port' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'status' )
                ->label( 'Status' )
                ->fromQuery( $t->status, 'name' )
                ->placeholder( 'Choose a status' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'speed' )
                ->label( 'Speed' )
                ->fromQuery( $t->speed, 'name' )
                ->placeholder( 'Choose a speed' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'duplex' )
                ->label( 'Duplex' )
                ->fromQuery( $t->duplex, 'name' )
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


        <?= Former::hidden( 'id' )
            ->value( $t->pi ? $t->pi->getId() : false )
        ?>

        <?= Former::hidden( 'viid' )
            ->value( $t->pi ? $t->pi->getVirtualInterface()->getId() : $t->vi->getId() )
        ?>

        <?= Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( url( 'physicalInterface/list/' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->id('btn-group');?>

        <?= Former::close() ?>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready( function() {

        });

        $( "#switch" ).change(function(){
            $( "#switch-port" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

            switchId = $( "#switch" ).val();

            // ask what is that ?
            var type = "peering";
            url = "<?= url( '/api/v4/switcher' )?>/" + switchId + "/switch-port-not-assign-to-pi";

            $.ajax( url , {
                data: {type : type },
                type: 'POST'
            })
                .done( function( data ) {
                    var options = "<option value=\"\">Choose a switch port</option>\n";
                    $.each( data.listPorts, function( key, value ){
                        options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                    });
                    $( "#switch-port" ).html( options );
                })
                .fail( function() {
                    throw new Error( "Error running ajax query for api/v4/switch/$id/switch-port-not-assign-to-pi" );
                    alert( "Error running ajax query for api/v4/switch/$id/switch-port-not-assign-to-pi" );
                })
                .always( function() {
                    $( "#switch-port" ).trigger( "chosen:updated" );
                });
        });


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
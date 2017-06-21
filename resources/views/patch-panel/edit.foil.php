<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action ( 'PatchPanel\PatchPanelController@index' )?>">Patch Panels</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        <?= $t->pp ? 'Editing Patch Panel: ' . $t->ee( $t->pp->getName() ) : 'Add New Patch Panel' ?>
    </li>
<?php $this->append() ?>


<?php $this->section('content') ?>

<?php if( $t->pp ): ?>
    <div class="well">
        To add additional ports to an existing patch panel, just add the number of ports <b>you want to add</b> in <em>Add Number of Ports</em> below.
    </div>
<?php endif; ?>


    <?= Former::open()
        ->method( 'post' )
        ->action( action ( 'PatchPanel\PatchPanelController@store' ) )
        ->customWidthClass( 'col-sm-3' );
    ?>

        <?= Former::text( 'name' )
            ->label( 'Patch Panel Name' )
            ->blockHelp( "The name / reference for the patch panel, displayed throughout IXP Manager. "
                . "Using the co-location provider's reference is probably the sanest / least confusing option." );
        ?>

        <?= Former::text( 'colo_reference' )
            ->label( 'Colocation reference' )
            ->blockHelp( 'The reference the co-location provider has assigned to this patch panel.' );
        ?>

        <?= Former::select( 'cabinet' )
            ->label( 'Cabinet' )
            ->fromQuery( $t->cabinets, 'name' )
            ->placeholder( 'Choose a Cabinet' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::select( 'mounted_at' )
            ->label( 'Mounted At' )
            ->options(   Entities\PatchPanel::$MOUNTED_AT )
            ->placeholder( '---' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Is this patch panel mounted at the front or rear of the cabinet?' );
        ?>

        <?= Former::number( 'u_position' )
            ->label( 'U Position' )
            ->blockHelp( "Rack 'U' position of patch panel" );
        ?>

        <?= Former::select( 'cable_type' )
            ->label( 'Cable Type' )
            ->options(   Entities\PatchPanel::$CABLE_TYPES )
            ->placeholder( 'Choose a Cable Type' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::select( 'connector_type' )
            ->label( 'Connector Type' )
            ->options( Entities\PatchPanel::$CONNECTOR_TYPES )
            ->placeholder( 'Choose a Connector Type')
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::number( 'numberOfPorts' )
            ->label( ( $t->pp ? 'Add ' : '' ) . 'Number of Ports' )
            ->appendIcon( 'nb-port glyphicon glyphicon-info-sign' )
            ->blockHelp(
                $t->pp ? 'There are ' . $t->pp->getPortCount() . " ports in this panel already. Enter the number of ports <b> you want to add</b> above."
                        . "<b>Note that duplex ports should be entered as two ports.</b>"
                    : 'Please set the number of ports that you want to create for this patch panel. <b>Note that duplex ports should be entered as two ports.</b>'
            );
        ?>

        <?= Former::text( 'port_prefix' )
            ->label( 'Port Name Prefix' )
            ->placeholder( 'Optional port prefix' )
            ->blockHelp( "This is optional. As an example, you may was to prefix individual fibre strands in a duplex port with "
                . "<code>F</code> which would mean the name of a duplex port would be displayed as <code>F1/F2</code>." );
        ?>

        <?= Former::select( 'chargeable' )
            ->label( 'Chargeable' )
            ->options( Entities\PatchPanelPort::$CHARGEABLES )
            ->select(  $t->pp ? $t->pp->getChargeable() : Entities\PatchPanelPort::CHARGEABLE_NO )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Usually IXPs request their members to <em>come to them</em> and bear the costs of that. '
                . 'However, sometimes a co-location facility may charge the IXP for a half circuit or the IXP may need '
                . 'order and pay for the connection. Setting this only sets the default option when allocating ports to '
                . 'members later.' );
        ?>

        <?= Former::date( 'installation_date' )
            ->label( 'Installation Date' )
            ->append( '<button class="btn-default btn" id="date-today" type="button">Today</button>' )
            ->value( date( 'Y-m-d' ) );
        ?>

        <?= Former::textarea( 'location_notes' )
            ->label( 'Location Notes' )
            ->rows( 5 )
//            ->style( 'width:500px' )
            ->blockHelp( 'These notes are included on connection and other emails to help co-location providers correctly '
                . 'identify their own co-location references. Unfortunately, it has been the experience of the authors '
                . 'that co-location providers change identifiers (and ownership) like the wind changes direction. These '
                . 'notes will be parsed as Markdown.'
            );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->pp ? $t->pp->getId() : '' )
        ?>

        <?= Former::actions(
                Former::primary_submit( 'Save Changes' ),
                Former::default_link( 'Cancel' )->href(  action ( 'PatchPanel\PatchPanelController@index' ) ),
                Former::success_button( 'Help' )->id( 'help-btn' )
            );
        ?>

    <?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

    <script>

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
        $( document ).ready( function() {
            /**
             * set the today date on click on the today button
             */
            $( "#date-today" ).click( function() {
                $( "#installation_date" ).val( '<?= date( "Y-m-d" ) ?>' );
            });

            /**
             * set the colo_reference in empty input by the name input value
             */
            $( "#name" ).blur( function() {
                if( $( "#colo_reference" ).val() == '' ){
                    $( "#colo_reference" ).val( $("#name" ).val() );
                }
            });

            /**
            * set data to the tooltip
            */
            $( ".glyphicon-nb-port" ).parent().attr( 'data-toggle','popover' ).attr( 'title' , 'Help - Number of Ports' ).attr( 'data-content' ,
                '<b>Note that duplex ports should be entered as two ports.</b>' );

            /**
             * configuration of the tooltip
             */
            $( "[data-toggle=popover]" ).popover( { placement: 'left',container: 'body', html: true, trigger: "hover" } );
        });
    </script>

<?php $this->append() ?>
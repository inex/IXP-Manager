<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'patch-panel' )?>">Patch Panel</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        <?= $t->pp ? 'Editing Patch Panel: ' . $t->pp->getName() : 'Add New Patch Panel' ?>
    </li>
<?php $this->append() ?>


<?php $this->section('content') ?>

    <?= Former::open()
        ->method( 'post' )
        ->action( url( 'patch-panel/store' ) )
        ->customWidthClass( 'col-sm-3' );
    ?>

        <?= Former::text( 'name' )
            ->label( 'Patch Panel Name' )
            ->help( "The name / reference for the patch panel. Using the co-location provider's reference is probably the sanest / least confusing option." );
        ?>

        <?= Former::text( 'colo_reference' )
            ->label( 'Colocation reference' )
            ->help( 'The reference the co-location provider has assigned to this patch panel.' );
        ?>

        <?= Former::select( 'cabinet' )
            ->label( 'Cabinet' )
            ->fromQuery( $t->cabinets, 'name' )
            ->placeholder( 'Choose a Cabinet' )
            ->addClass( 'chzn-select' );
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
            ->label( 'Number of Ports' )
            ->appendIcon( 'nb-port glyphicon glyphicon-info-sign' )
            ->help(
                $t->pp ? 'There are ' . $t->pp->getPortCount() . " ports in this panel already. Enter the number of ports <b> you want to add</b> above."
                        . "<b>Note that duplex ports should be entered as two ports.</b>"
                    : 'Please set the number of ports that you want to create for this patch panel.'
            );
        ?>

        <?= Former::text( 'port_prefix' )
            ->label( 'Port Name Prefix' )
            ->placeholder( 'Optional port prefix' )
            ->readonly( $t->pp && $t->pp->getPortPrefix() )
            ->help( "This is optional. As an example, you may was to prefix individual fibre strands in a duplex port with <code>F</code> which would mean the name of a duplex port would be displayed as <code>F1/F2</code>." );
        ?>

        <?= Former::date( 'installation_date' )
            ->label( 'Installation Date' )
            ->append( '<button class="btn-default btn" id="date-today" type="button">Today</button>' )
            ->value( date( 'Y-m-d' ) );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->pp ? $t->pp->getId() : '' )
        ?>

        <?= Former::actions(
                Former::primary_submit( 'Save Changes' ),
                Former::default_link( 'Cancel' )->href( url( 'patch-panel/list' ) ),
                Former::success_button( 'Help' )->id( 'help-btn' )
            );
        ?>

    <?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( document ).ready( function() {
            /**
             * hide the help sections at loading
             */
            $( '.help-block' ).hide();

            /**
             * display / hide help sections on click on the help button
             */
            $( "#help-btn" ).click( function() {
                $( ".help-block" ).toggle();
            });

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
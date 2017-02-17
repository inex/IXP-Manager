<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel')?>">Patch Panel</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li> <?= $t->patchPanel ? 'Editing Patch Panel: ' . $t->patchPanel->getName() : 'Add New Patch Panel' ?> </li>
<?php $this->append() ?>


<?php $this->section('content') ?>



<?= Former::open()
        ->method('post')
        ->action( url( 'patch-panel/store' ) )
        ->customWidthClass('col-sm-3');
?>

    <?= Former::text( 'name' )
            ->label( 'Patch Panel Name' )
            ->help('help text');
    ?>

    <?= Former::text( 'colo_reference' )
            ->label( 'Colocation reference' )
            ->help('help text');
    ?>

    <?= Former::select( 'cabinet' )
            ->label( 'Cabinet' )
            ->fromQuery( $t->cabinets, 'name' )
            ->placeholder( 'Choose a Cabinet' )
            ->addClass( 'chzn-select' )
            ->help('help text');
    ?>

    <?= Former::select( 'cable_type' )
            ->label( 'Cable Type' )
            ->options(   Entities\PatchPanel::$CABLE_TYPES )
            ->placeholder( 'Choose a Cable Type' )
            ->addClass( 'chzn-select' )
            ->help('help text');
    ?>

    <?= Former::select( 'connector_type' )
            ->label( 'Connector Type' )
            ->options( Entities\PatchPanel::$CONNECTOR_TYPES )
            ->placeholder( 'Choose a Connector Type')
            ->addClass( 'chzn-select' )
            ->help('help text');
    ?>

    <?= Former::number( 'numberOfPorts' )
        ->label( 'Number of Ports' )
        ->appendIcon( 'nb-port glyphicon glyphicon-info-sign' )
        ->help( $t->patchPanel ? 'Existing: ' . $t->patchPanel->getPortCount() : '' )
    ?>

    <?= Former::text( 'port_prefix' )
        ->label( 'Port Name Prefix' )
        ->placeholder( 'Optional port prefix' )
        ->readonly( $t->patchPanel && $t->patchPanel->getPortPrefix() )
        ->appendIcon( 'prefix glyphicon glyphicon-info-sign' )
        ->help('help text');
    ?>

    <?= Former::date( 'installation_date' )
        ->label( 'Installation Date' )
        ->append( '<button class="btn-default btn" id="date-today" type="button">Today</button>' )
        ->value(date('Y-m-d'))
        ->help('help text');
    ?>

    <?= Former::hidden( 'id' )
            ->value( $t->patchPanel ? $t->patchPanel->getId() : '' )
    ?>

    <?= Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link('Cancel')->href(url('patch-panel/list')),
            Former::success_button('Help')->id('help-btn')
        );
    ?>

<?= Former::close() ?>

<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script>
$(document).ready( function() {
    $('.help-block').hide();

    $( "#help-btn" ).click( function() {
        if($( ".help-block" ).css('display') == 'none'){
            $( ".help-block" ).show();
        }
        else{
            $( ".help-block" ).hide();
        }

    });

    $( ".glyphicon-nb-port" ).parent().attr( 'data-toggle','popover' ).attr( 'title' , 'Help' ).attr( 'data-content' ,
        'Please set the number of ports that you want to create for this patch panel. Note that duplex ports should be entered as two ports.' );

    $( ".glyphicon-prefix" ).parent().attr( 'data-toggle', 'popover' ).attr( 'title' , 'Help' ).attr( 'data-content' ,
        'need text'
    );

    $( "#date-today" ).click( function() {
        $( "#installation_date" ).val( '<?= date("Y-m-d" ) ?>' );
    });

    $("[data-toggle=popover] ").popover({ placement: 'left',container: 'body', trigger: "hover"});

    $( "#name" ).blur( function() {
        if( $("#colo_reference").val() == '' ){
            $("#colo_reference").val( $("#name" ).val());
        }
    });

});
</script>
<?php $this->append() ?>

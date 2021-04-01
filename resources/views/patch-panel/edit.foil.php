<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'patch-panel@list' )?>">Patch Panels</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panels
    /
    <?= $t->pp ? 'Edit: ' . $t->ee( $t->pp->name ) : 'Create' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route('patch-panel@list' ) ?>" title="Patch panel list">
            <i class="fa fa-th-list"></i>
        </a>
        <?php if( $t->pp ): ?>
            <a class="btn btn-white" href="<?= route('patch-panel@view', [ "pp" => $t->pp->id ] ) ?>" title="Patch panel list">
                <i class="fa fa-eye"></i>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?php if( $t->pp ): ?>
                <div class="alert alert-info" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-question-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            To add additional ports to an existing patch panel, add the number of ports <b>you want to add</b> in <em>Add Number of Ports</em> below.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">

                    <?= Former::open()
                        ->method(  $t->pp ? 'PUT' : 'POST' )
                        ->action( $t->pp ? route( 'patch-panel@update', [ 'pp' => $t->pp->id  ] ) : route( 'patch-panel@store' ) )
                        ->customInputWidthClass( 'col-lg-4 col-md-6 col-sm-6' )
                        ->customLabelWidthClass( 'col-sm-4 col-md-4 col-lg-3' )
                        ->actionButtonsCustomClass( "grey-box");
                    ?>

                    <?= Former::text( 'name' )
                        ->label( 'Name' )
                        ->blockHelp( "The name / reference for the patch panel, displayed throughout IXP Manager. "
                            . "Using the co-location provider's reference is probably the sanest / least confusing option." );
                    ?>

                    <?= Former::text( 'colo_reference' )
                        ->label( 'Colocation reference' )
                        ->blockHelp( 'The reference the facility/co-location provider has assigned to this patch panel.' );
                    ?>

                    <?= Former::select( 'cabinet_id' )
                        ->label( 'Rack' )
                        ->fromQuery( $t->cabinets, 'name' )
                        ->placeholder( 'Choose a rack' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::select( 'mounted_at' )
                        ->label( 'Mounted At' )
                        ->options(   \IXP\Models\PatchPanel::$MOUNTED_AT )
                        ->placeholder( '---' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Is this patch panel mounted at the front or rear of the rack?' );
                    ?>

                    <?= Former::number( 'u_position' )
                        ->label( 'U Position' )
                        ->blockHelp( "Rack 'U' position of patch panel" );
                    ?>

                    <?= Former::select( 'cable_type' )
                        ->label( 'Cable Type' )
                        ->options(   \IXP\Models\PatchPanel::$CABLE_TYPES )
                        ->placeholder( 'Choose a Cable Type' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::select( 'connector_type' )
                        ->label( 'Connector Type' )
                        ->options( \IXP\Models\PatchPanel::$CONNECTOR_TYPES )
                        ->placeholder( 'Choose a Connector Type')
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::number( 'numberOfPorts' )
                        ->label( ( $t->pp ? 'Add ' : '' ) . 'Number of Ports' )
                        ->append( '<button class="btn-white btn rounded-right" id="icon-nb-port" type="button"><i class="nb-port fa fa-info-circle"></i></button>' )
                        ->min( 0 )
                        ->blockHelp(
                            $t->pp ? 'There are ' . $t->pp->patchPanelPorts()->count() . " ports in this panel already. Enter the number of ports <b> you want to add</b> above."
                                . " <b>Note that duplex ports should be entered as two ports.</b>"
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
                        ->options( \IXP\Models\PatchPanelPort::$CHARGEABLES )
                        ->select( $t->pp->chargeable ?? \IXP\Models\PatchPanelPort::CHARGEABLE_NO )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Usually IXPs request their members to <em>come to them</em> and bear the costs of that. '
                            . 'However, sometimes a co-location facility may charge the IXP for a half circuit or the IXP may need '
                            . 'order and pay for the connection. Setting this only sets the default option when allocating ports to '
                            . 'members later.' );
                    ?>

                    <?= Former::date( 'installation_date' )
                        ->label( 'Installation Date' )
                        ->append( '<button class="btn-white btn rounded-right" id="date-today" type="button">Today</button>' )
                        ->value( date( 'Y-m-d' ) );
                    ?>

                    <div class="form-group row">
                        <div class="col-sm-8">
                            <div class="card mt-4">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs">
                                        <li role="presentation" class="nav-item">
                                            <a class="tab-link-body-note nav-link active" href="#body">Facility Notes</a>
                                        </li>
                                        <li role="presentation" class="nav-item">
                                            <a class="tab-link-preview-note nav-link" href="#preview">Preview</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content card-body">
                                    <div role="tabpanel" class="tab-pane show active" id="body">
                                        <?= Former::textarea( 'location_notes' )
                                            ->id( 'location_notes' )
                                            ->label( '' )
                                            ->rows( 10 )
                                            ->blockHelp( "These notes are included on connection and other emails to help co-location providers correctly identify their 
                                            own co-location references. These notes will be parsed as Markdown." )
                                        ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="preview">
                                        <div class="bg-light p-4 well-preview">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?= Former::actions(
                        Former::primary_submit( $t->pp ? 'Save Changes' : 'Create' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href(  route( 'patch-panel@list' ) )->class( "mb-2 mb-sm-0" ),
                        Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                    );
                    ?>

                    <?= Former::close() ?>
                </div>
            </div>
        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'patch-panel/js/edit' ); ?>
<?php $this->append() ?>
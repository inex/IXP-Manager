<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Physical Interfaces / <?= $t->pi ? 'Edit' : 'Create' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'physical-interface@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <?php if( $t->pi ): ?>
            <a class="btn btn-white" href="<?= route( 'physical-interface@view' , [ "pi" => $t->pi->id ])?>" title="list">
                <span class="fa fa-eye"></span>
            </a>
        <?php endif;?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <?= $t->alerts() ?>
        <div class="card col-lg-12">
            <div class="card-body row">
                <div class="<?= $t->otherPICoreLink || $t->enableFanout ? 'col-lg-6 col-sm-12': 'col-lg-12'  ?>">
                    <?php if( $t->otherPICoreLink || $t->enableFanout ): ?>
                        <h3>
                            Main Physical Interface
                        </h3>
                        <hr>
                    <?php endif; ?>

                    <?= Former::open()->method( $t->pi ? 'PUT' : 'POST' )
                        ->action( $t->pi ? route( 'physical-interface@update', [ 'pi' => $t->pi->id  ] ) : route( 'physical-interface@store' ) )
                        ->customInputWidthClass( $t->otherPICoreLink || $t->enableFanout ? 'col-sm-6' : 'col-lg-4 col-md-6 col-sm-6' )
                        ->customLabelWidthClass( $t->otherPICoreLink || $t->enableFanout ? 'col-lg-6 col-sm-4' : 'col-lg-2 col-md-3 col-sm-3' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                    <?= Former::select( 'switch' )
                        ->dataValue( 'switch' )
                        ->label( 'Switch' )
                        ->fromQuery( $t->switches, 'name' )
                        ->placeholder( 'Choose a switch' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The switch where the port will be located. Selected / changing this updates the port list below.' );
                    ?>

                    <?= Former::select( 'switchportid' )
                        ->label( 'Switch Port' )
                        ->fromQuery( $t->switchports, 'name' )
                        ->placeholder( 'Choose a switch port' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Suitable available ports. For example, when adding a peering interface, only ports of type <em>Peering</em> or <em>Unset / Unknown</em> are shown. '
                            . 'Port types can be set by editing the appropriate switch.');
                    ?>

                    <?=
                    Former::hidden( 'original-switch-port')
                        ->id('original-switch-port')
                        ->forceValue( old('switch-port') ?: ($t->pi && $t->pi->switchPort ? $t->pi->switchPort->id : ''))
                    ?>

                    <?= Former::select( 'status' )
                        ->label( 'Status' )
                        ->fromQuery( \IXP\Models\PhysicalInterface::$STATES, 'name' )
                        ->placeholder( 'Choose a status' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'This is an important setting. Only ports (or LAGs) with at least one physical interface set to <em>Connected</em> which have elements such as '
                            . 'route server configurations built, monitoring configuration generated, etc. Similarly, for a quarantine route collector session to be generated, the '
                            . 'port must have the <em>Quarantine</em> state. Currently all other states are just informational.' );
                    ?>

                    <?= Former::select( 'speed' )
                        ->label( 'Speed' )
                        ->fromQuery( \IXP\Models\PhysicalInterface::$SPEED, 'name' )
                        ->placeholder( 'Choose a speed' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The port speed configured on the physical interface. This information is used for provisioning and '
                            . 'presented publicly to other members in a number of places. For statistics / graphing, it dictates the maximum data rate accepted. ' );
                    ?>

                    <?= Former::select( 'duplex' )
                        ->label( 'Duplex' )
                        ->fromQuery( \IXP\Models\PhysicalInterface::$DUPLEX, 'name' )
                        ->placeholder( 'Choose Duplex' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Configure the port to use half-duplex ethernet.' );
                    ?>

                    <?= Former::number( 'rate_limit' )
                        ->label( 'Rate Limit <u>(Mbps)</u>' )
                        ->blockHelp( 'Enter the provisioned speed if the port has been rate limited below its line rate. <strong>Enter in Mbps!</strong> Leave blank if port is not rate limited. Zero will be converted to null (blank - not rate limited).');
                    ?>


                    <?= Former::checkbox( 'autoneg' )
                        ->label( '&nbsp;' )
                        ->text( 'Auto-Negotiation Enabled' )
                        ->value( 1 )
                        ->inline()
                        ->blockHelp( "Unless you are provisioning switches from IXP Manager, this is informational." );
                    ?>

                    <div class="form-group row">
                        <div class="<?= $t->otherPICoreLink || $t->enableFanout ? 'col-sm-10': 'col-sm-8'  ?>">
                            <div class="card mt-4">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs">
                                        <li role="presentation" class="nav-item">
                                            <a class="tab-link-body-note nav-link active" href="#body">Notes</a>
                                        </li>
                                        <li role="presentation" class="nav-item">
                                            <a class="tab-link-preview-note nav-link" href="#preview">Preview</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content card-body">
                                    <div role="tabpanel" class="tab-pane show active" id="body">
                                        <?= Former::textarea( 'notes' )
                                            ->id( 'notes' )
                                            ->label( '' )
                                            ->rows( 10 )
                                        ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="preview">
                                        <div class="bg-light p-4 well-preview" style="background: rgb(255,255,255);">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-12 d-block mt-4 mt-md-4 mt-lg-0 ">
                    <?php if( $t->otherPICoreLink ): ?>
                        <div class="col-sm-12 mb-4">
                            <h3>
                                Other Side of the Core Link
                            </h3>
                            <hr>

                            <?= Former::select( 'switch-b' )
                                ->label( 'Switch' )
                                ->fromQuery( $t->switches, 'name' )
                                ->placeholder( 'Choose a fanout switch' )
                                ->addClass( 'chzn-select' )
                                ->disabled( true)
                                ->blockHelp( '' );
                            ?>

                            <?= Former::select( 'switch-port-b' )
                                ->label( 'Switch Port' )
                                ->fromQuery( $t->ee( $t->otherPICoreLink->switchPort->name ) , 'name' )
                                ->placeholder( 'Choose a switch port' )
                                ->addClass( 'chzn-select' )
                                ->disabled( true)
                                ->blockHelp( '' );
                            ?>

                            <?= Former::select( 'status-b' )
                                ->label( 'Status' )
                                ->fromQuery( \IXP\Models\PhysicalInterface::$STATES, 'name' )
                                ->placeholder( 'Choose a status' )
                                ->addClass( 'chzn-select' )
                                ->disabled( true)
                                ->blockHelp( '' );
                            ?>

                            <?= Former::select( 'speed-b' )
                                ->label( 'Speed' )
                                ->fromQuery( \IXP\Models\PhysicalInterface::$SPEED, 'name' )
                                ->placeholder( 'Choose a speed' )
                                ->addClass( 'chzn-select' )
                                ->disabled( true)
                                ->blockHelp( '' );
                            ?>

                            <?= Former::select( 'duplex-b' )
                                ->label( 'Duplex' )
                                ->fromQuery( \IXP\Models\PhysicalInterface::$DUPLEX, 'name' )
                                ->placeholder( 'Choose Duplex' )
                                ->addClass( 'chzn-select' )
                                ->disabled( true)
                                ->blockHelp( '' );
                            ?>

                            <?= Former::checkbox( 'autoneg-label-b' )
                                ->label('&nbsp;')
                                ->text( 'Auto-Negotiation Enabled' )
                                ->value( 1 )
                                ->disabled( true )
                                ->inline()
                                ->blockHelp( "" ); ?>

                            <div class="form-group">
                                <label for="notes" class="control-label col-lg-6 col-sm-6">Notes</label>
                                <div class="col-sm-8">
                                    <?= @parsedown( $t->notesb )?>
                                </div>
                            </div>

                        </div>

                    <?php endif; ?>

                    <?php if( $t->enableFanout ): ?>
                        <div class="col-sm-12">
                            <h3>
                                Fanout Port
                            </h3>
                            <hr>
                            <?= Former::checkbox( 'fanout' )
                                ->label( '&nbsp;' )
                                ->text('Associate a fanout port?')
                                ->value( 1 )
                                ->inline()
                                ->blockHelp( "" );
                            ?>

                            <div id="fanout-area" class="collapse">
                                <?= Former::select( 'switch-fanout' )
                                    ->dataValue( 'fanout' )
                                    ->label( 'Switch' )
                                    ->fromQuery( $t->switches, 'name' )
                                    ->placeholder( 'Choose a Switch' )
                                    ->addClass( 'chzn-select' )
                                    ->blockHelp( '' );
                                ?>

                                <?= Former::select( 'switch-port-fanout' )
                                    ->label( 'Switch Port' )
                                    ->placeholder( 'Choose a switch port' )
                                    ->addClass( 'chzn-select' )
                                    ->blockHelp( '' );
                                ?>

                                <?= Former::hidden( 'original-switch-port-fanout')
                                    ->id('original-switch-port-fanout')
                                    ->forceValue( old('switch-port-fanout') ?: ($t->spFanout ? $t->spFanout : ''))
                                ?>

                                <?= Former::hidden( 'sp-fanout' )
                                    ->id( 'sp-fanout' )
                                    ->value( $t->spFanout )
                                ?>

                                <?= Former::hidden( 'fanout-checked' )
                                    ->id( 'fanout-checked' )
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?= Former::hidden( 'virtualinterfaceid' )
                        ->value( $t->pi ? $t->pi->virtualInterface->id : $t->vi->id )
                    ?>

                    <?= Former::hidden( 'cb' )
                        ->value( $t->cb  )
                    ?>

                </div>

                <div class="flow-root"></div>

                <?= Former::actions(
                    Former::primary_submit( $t->pi ? 'Save Changes' : 'Create' )->class( "mb-2 mb-sm-0" ),
                    Former::secondary_link( 'Cancel' )->id( 'cancel-btn' )->href( $t->vi ? route( 'virtual-interface@edit' , [ 'vi' => $t->vi->id ] ) : route( 'physical-interface@list' ) )->class( "mb-2 mb-sm-0" ),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                )->id('btn-group')?>
                <?= Former::close() ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ); ?>
    <?= $t->insert( 'interfaces/common/js/interface-functions' ); ?>
    <?= $t->insert( 'interfaces/common/js/pi-form-logic' ); ?>
    <?= $this->insert( 'interfaces/physical/js/edit' ) ?>
<?php $this->append(); ?>
<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
Physical Interfaces / Edit
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'interfaces/physical/list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>

        <?php if( $t->pi ): ?>
            <a class="btn btn-white" href="<?= route( 'interfaces/physical/view' , [ "id" => $t->pi->getId() ])?>" title="list">
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

                    <?= Former::open()->method( 'POST' )
                        ->action( route( 'interfaces/physical/store' ) )
                        ->customInputWidthClass( $t->otherPICoreLink || $t->enableFanout ? 'col-sm-6' : 'col-lg-4 col-md-6 col-sm-6' )
                        ->customLabelWidthClass( $t->otherPICoreLink || $t->enableFanout ? 'col-lg-6 col-sm-4' : 'col-lg-2 col-md-3 col-sm-3' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                    <?= Former::select( 'switch' )
                        ->label( 'Switch' )
                        ->fromQuery( $t->switches, 'name' )
                        ->placeholder( 'Choose a switch' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The switch where the port will be located. Selected / changing this updates the port list below.' );
                    ?>

                    <?= Former::select( 'switch-port' )
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
                        ->forceValue( old('switch-port') ? old('switch-port')  : ( $t->pi && $t->pi->getSwitchPort() ? $t->pi->getSwitchPort()->getId() : '' ) )
                    ?>

                    <?= Former::select( 'status' )
                        ->label( 'Status' )
                        ->fromQuery( \Entities\PhysicalInterface::$STATES, 'name' )
                        ->placeholder( 'Choose a status' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'This is an important setting. Only ports (or LAGs) with at least one physical interface set to <em>Connected</em> which have elements such as '
                            . 'route server configurations built, monitoring configuration generated, etc. Similarly, for a quarantine route collector session to be generated, the '
                            . 'port must have the <em>Quarantine</em> state. Currently all other states are just informational.' );
                    ?>

                    <?= Former::select( 'speed' )
                        ->label( 'Speed' )
                        ->fromQuery( \Entities\PhysicalInterface::$SPEED, 'name' )
                        ->placeholder( 'Choose a speed' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The port speed to be configured. Unless you are provisioning switches from IXP Manager, this is informational / useful for billing. It is also '
                            . 'presented publically to other members in a number of places. For statistics / graphing, it dictates the maximum data rate accepted also. ' );
                    ?>

                    <?= Former::select( 'duplex' )
                        ->label( 'Duplex' )
                        ->fromQuery( \Entities\PhysicalInterface::$DUPLEX, 'name' )
                        ->placeholder( 'Choose Duplex' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Is half duplex even a thing anymore?' );
                    ?>

                    <?= Former::checkbox( 'autoneg-label' )
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
                                        <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?=  $t->notes ?></textarea>
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

                            <div id="fanout-area" style="display: none">

                                <?= Former::select( 'switch-fanout' )
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
                                    ->forceValue( old('switch-port-fanout') ? old('switch-port-fanout')  : ( $t->spFanout ? $t->spFanout : '' ) )
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

                    <?= Former::hidden( 'id' )
                        ->value( $t->pi ? $t->pi->getId() : false )
                    ?>

                    <?= Former::hidden( 'viid' )
                        ->value( $t->pi ? $t->pi->getVirtualInterface()->getId() : $t->vi->getId() )
                    ?>

                    <?= Former::hidden( 'cb' )
                        ->value( $t->cb  )
                    ?>

                </div>

                <div class="clearfix"></div>

                <?= Former::actions(
                    Former::primary_submit( $t->pi ? 'Save Changes' : 'Add' )->class( "mb-2 mb-sm-0" ),
                    Former::secondary_link( 'Cancel' )->id( 'cancel-btn' )->href( $t->vi ? route( 'interfaces/virtual/edit' , [ 'id' => $t->vi->getId() ] ) : route( 'interfaces/physical/list' ) )->class( "mb-2 mb-sm-0" ),
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

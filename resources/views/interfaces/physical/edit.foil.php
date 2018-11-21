<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/physical/list' ) ?>">Physical Interfaces</a>
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

            <?php if( $t->pi ): ?>
                <a type="button" class="btn btn-default" href="<?= route( 'interfaces/physical/view' , [ "id" => $t->pi->getId() ])?>" title="list">
                    <span class="glyphicon glyphicon-eye-open"></span>
                </a>
            <?php endif;?>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <div class="well">

                <?= Former::open()->method( 'POST' )
                    ->action( route( 'interfaces/physical/store' ) )
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
                        ->blockHelp( "Unless you are provisioning switches from IXP Manager, this is informational." );
                    ?>


                    <div class="form-group">

                        <label for="notes" class="control-label col-lg-2 col-sm-4">Notes</label>
                        <div class="col-sm-8">

                            <ul class="nav nav-tabs">
                                <li role="presentation" class="active">
                                    <a class="tab-link-body-note" href="#body">Notes</a>
                                </li>
                                <li role="presentation">
                                    <a class="tab-link-preview-note" href="#preview">Preview</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="body">

                                    <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?=  $t->notes ?></textarea>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="preview">
                                    <div class="well well-preview" style="background: rgb(255,255,255);">
                                        Loading...
                                    </div>
                                </div>
                            </div>

                            <br><br>
                        </div>

                    </div>

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
                            ->blockHelp( "" ); ?>

                        <div class="form-group">

                            <label for="notes" class="control-label col-lg-2 col-sm-4">Notes</label>
                            <div class="col-sm-8">
                                <?= @parsedown( $t->notesb )?>
                            </div>

                        </div>

                    </div>

                <?php endif; ?>

                <?php if( $t->enableFanout ): ?>

                    <div class="col-sm-6">
                        <h3>
                            Fanout Port
                        </h3>
                        <hr>

                        <?= Former::checkbox( 'fanout' )
                            ->label( '&nbsp;' )
                            ->text('Associate a fanout port?')
                            ->value( 1 )
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

                <?= Former::actions(
                    Former::primary_submit( 'Save Changes' ),
                    Former::default_link( 'Cancel' )->id( 'cancel-btn' )->href( $t->vi ? route( 'interfaces/virtual/edit' , [ 'id' => $t->vi->getId() ] ) : route( 'interfaces/physical/list' ) ),
                    Former::success_button( 'Help' )->id( 'help-btn' )
                )->id('btn-group');?>

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

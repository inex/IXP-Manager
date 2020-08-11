<?php if( $t->cb ): ?>
    <div class="alert alert-warning mt-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center">
                <i class="fa fa-exclamation-circle fa-2x"></i>
            </div>
            <div class="col-sm-12">
                The Virtual Interface belongs to a Core Bundle: ensure you match any changes to MTU, 802.1q framing, etc. to the other half which can be
                <a href="<?= route('core-bundle@edit' , [ 'id' => $t->cb->getId() ]) ?>"> accessed by clicking here </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?= Former::open()->method( 'POST' )
            ->action( route( 'interfaces/virtual/store' ) )
            ->customInputWidthClass( 'col-lg-6 col-md-7' )
            ->customLabelWidthClass( 'col-lg-4 col-md-5' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <div class="col-lg-12">
            <div class="row">
                <div class="col-sm-6">
                    <?= Former::select( 'cust' )
                        ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
                        ->fromQuery( $t->cust, 'name' )
                        ->placeholder( 'Choose a ' . config( 'ixp_fe.lang.customer.one' ) )
                        ->addClass( 'chzn-select' )
                        ->disabled( $t->selectedCust ? true : false )
                        ->blockHelp( 'The ' . config( 'ixp_fe.lang.customer.one' ) . ' who owns this virtual interface.' );
                    ?>

                    <?php if( $t->vi && count( $t->vi->getVlanInterfaces() ) > 1 && !$t->vi->getTrunk() ): ?>
                      <div class="alert alert-warning mt-4" role="alert">
                        <div class="d-flex align-items-center">
                          <div class="text-center">
                            <i class="fa fa-exclamation-circle fa-2x"></i>
                          </div>
                          <div class="col-sm-12">
                            <b class="label label-warning">WARNING</b>
                            802.1q framing is not set but there are >1 VLAN interfaces:
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>

                  <?= Former::checkbox( 'trunk' )
                        ->label( '&nbsp;' )
                        ->text( 'Use 802.1q framing' )
                        ->inline()
                        ->blockHelp( 'Indicates if operators / provisioning systems should configure this port with 802.1q framing / tagged packets.' )
                        ->value( 1 );
                    ?>

                    <?php if( $t->vi && count( $t->vi->getPhysicalInterfaces() ) > 1 && !$t->vi->getLagFraming() ): ?>
                        <div class="alert alert-warning mt-4" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="text-center">
                                    <i class="fa fa-exclamation-circle fa-2x"></i>
                                </div>
                                <div class="col-sm-12">
                                    <b class="label label-warning">WARNING</b>
                                    LAG framing is not set and there is >1 physical interfaces. This may be intended but should be verified:
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?= Former::checkbox( 'lag_framing' )
                        ->label( '&nbsp;' )
                        ->text( 'Link aggregation / LAG framing' )
                        ->inline()
                        ->blockHelp( 'Indicates if operators / provisioning systems should enable LAG framing such as LACP. <br/><br/>Mandatory where there is more than one phsyical interface.<br/><br/> Otherwise optional where a member requests a single member LAG for ease of upgrades.' )
                        ->value( 1 );
                    ?>

                    <div id='fastlacp-area' style="<?= old( 'fastlacp' ) || $t->vi && $t->vi->getLagFraming() ? "" : "display: none" ?>">
                        <?= Former::checkbox( 'fastlacp' )
                            ->label( '&nbsp;' )
                            ->text( 'Use Fast LACP' )
                            ->inline()
                            ->blockHelp( 'When LACP is used for LAG framing, indicates if operators / provisioning systems should enable fast LACP.' )
                            ->value( 1 )
                        ?>
                    </div>

                    <?php if ($t->vi && $t->vi->getBundleName() ): ?>
                        <div class="form-group row">
                            <label for="custid" class="control-label col-sm-4">Bundle Name</label>
                            <div class="col-sm-6">
                                <label class="">
                                    <b>
                                        <code>
                                            <?= $t->ee( $t->vi->getBundleName() ) ?>
                                        </code>
                                    </b>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($t->vi && $t->vi->getType() ): ?>

                        <div class="form-group row">
                            <label class="control-label col-sm-4">Type</label>

                            <div class="col-sm-6">

                                <span class="badge <?php if( $t->vi->isTypePeering() ): ?> badge-success <?php elseif( $t->vi->isTypeFanout() ): ?>badge-secondary <?php elseif( $t->vi->isTypeReseller() ): ?>badge-dark <?php endif; ?>">
                                    <?= $t->vi->resolveType() ?>
                                </span>

                                <?php if( count( $t->vi->getPhysicalInterfaces() ) == 1 ):
                                    $pi = $t->vi->getPhysicalInterfaces()[0]; /** @var Entities\PhysicalInterface $pi */
                                    ?>
                                    <?php if( $t->vi->isTypePeering() && $pi->getFanoutPhysicalInterface() ): ?>
                                        <span style="margin-left: 15px;">
                                            <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi->getFanoutPhysicalInterface()->getVirtualInterface()->getId() ]) ?>" >
                                                See fanout port
                                            </a>
                                        </span>
                                    <?php elseif( $t->vi->isTypeFanout() && $pi->getPeeringPhysicalInterface() ): ?>
                                        <span style="margin-left: 15px;">
                                            <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi->getPeeringPhysicalInterface()->getVirtualInterface()->getId() ]) ?>" >
                                                See peering port
                                            </a>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

                <div id='advanced-area' class="col-sm-6 mt-4 mt-sm-0" style="display: none">
                    <?= Former::text( 'name' )
                        ->label( 'Virtual Interface Name' )
                        ->blockHelp( 'Ordinarily this is left blank. In the case of LAGs with provisioning systems, this is used to indicate the '
                            . 'interface base name for LAGs. E.g. on a Cisco, this would be <code>Port-Channel</code>.<br><br>'
                            . 'Some systems require trailing white space after the name. For this, use double-quotes which will '
                            . 'be removed automatically. E.g. for a Force10 device, enter: <code>"Port-channel "</code>.' );
                    ?>

                    <?= Former::text( 'description' )
                        ->label( 'Description' )
                        ->blockHelp( 'Free text, currently unusued.' );
                    ?>

                    <?= Former::number( 'channel-group' )
                        ->label( 'Channel Group Number' )
                        ->blockHelp( 'Ordinarily this is left blank. In the case of LAGs with provisioning systems, this is used to indicate the '
                            . 'unique LAG number where required.' );
                    ?>

                    <?= Former::number( 'mtu' )
                        ->label( 'MTU' )
                        ->blockHelp( 'The IP (layer 3) MTU to configure on an interface. Typically on an IXP, jumbo frames at enabled at layer 2 '
                            . ' while IXP participants are advised to use an IP MTU of 1500. Higher IP MTUs are configured here for core ports '
                            . ' for underlays such as VxLAN.');
                    ?>
                </div>

                <?= Former::hidden( 'id' )
                    ->value( $t->vi ? $t->vi->getId() : null )
                ?>

                <?php if( $t->vi ): ?>
                    <?= Former::hidden( 'custid' )
                        ->id( "custid" )
                        ->value( $t->vi->getCustomer()->getId() )
                    ?>
                <?php endif; ?>

                <?php if( $t->selectedCust ): ?>
                    <?= Former::hidden( 'selectedCust' )
                        ->value( $t->selectedCust->getId() )
                    ?>
                <?php endif; ?>


                <?php
                if( $t->cb ) {
                    $bbtn = '<a href="' . route( 'core-bundle@edit', [ 'id' => $t->cb->getId() ] ) . '" class="btn btn-secondary mb-2 mb-md-2 mb-lg-0">Return to Core Bundle</a>';
                } elseif( $t->vi ) {
                    $bbtn  = '<a href="' . route( "customer@overview" , [ "id" => $t->vi->getCustomer()->getId(), "tab" => "ports" ] ) . '" class="btn btn-secondary mb-2 mb-md-2 mb-lg-0">Return to ' . ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' Overview</a>';
                    $bbtn .= '<a class="collapse btn btn-danger mb-2 mb-md-2 mt-lg-2 ml-1" id="delete-vi-' . $t->vi->getId() . '" href="">Delete Interface</a>';
                } else {
                    $bbtn = '<a href="' . action( 'Interfaces\VirtualInterfaceController@list' ) . '" class="btn btn-secondary mmb-2 mb-md-2 mb-lg-0" >Cancel</a>';
                }
                ?>

                <?=
                Former::actions(
                    Former::primary_submit( $t->vi ? 'Save Changes' : 'Add' )->class( "mb-2 mb-md-2 mb-lg-0" )->id( "submit-form" ),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-md-2 mb-lg-0" ),
                    '<a class="btn btn-secondary mb-2 mb-md-2 mb-lg-0" href="#" id="advanced-options">Advanced Options</a>',
                    $bbtn
                )->id('btn-group')?>

                <?= Former::close() ?>
            </div>
        </div>
    </div>
</div>

<?php if( $t->cb ): ?>
    <div class="alert alert-warning mt-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center">
                <i class="fa fa-exclamation-circle fa-2x"></i>
            </div>
            <div class="col-sm-12">
                The Virtual Interface belongs to a Core Bundle: ensure you match any changes to MTU, 802.1q framing, etc. to the other half which can be
                <a href="<?= route('core-bundle@edit' , [ 'cb' => $t->cb->id ]) ?>"> accessed by clicking here </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?= Former::open()->method( $t->vi ? 'PUT' :'POST' )
            ->action( $t->vi ? route( 'virtual-interface@update', [ 'vi' => $t->vi->id ] ) : route( 'virtual-interface@store' ) )
            ->customInputWidthClass( 'col-lg-6 col-md-7' )
            ->customLabelWidthClass( 'col-lg-4 col-md-5' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <div class="col-lg-12">
            <div class="row">
                <div class="col-sm-6">
                    <?= Former::select( 'custid' )
                        ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
                        ->fromQuery( $t->custs, 'name' )
                        ->placeholder( 'Choose a ' . config( 'ixp_fe.lang.customer.one' ) )
                        ->addClass( 'chzn-select' )
                        ->disabled( $t->selectedCust ? true : false )
                        ->blockHelp( 'The ' . config( 'ixp_fe.lang.customer.one' ) . ' who owns this virtual interface.' );
                    ?>

                    <?php if( $t->vi && $t->vi->vlanInterfaces()->count() > 1 && !$t->vi->trunk ): ?>
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

                    <?php if( $t->vi && $t->vi->physicalInterfaces()->count() > 1 && !$t->vi->lag_framing ): ?>
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

                    <div id='fastlacp-area' style="<?= old( 'fastlacp' ) || ( $t->vi && $t->vi->lag_framing ) ? "" : "display: none" ?>">
                        <?= Former::checkbox( 'fastlacp' )
                            ->label( '&nbsp;' )
                            ->text( 'Use Fast LACP' )
                            ->inline()
                            ->blockHelp( 'When LACP is used for LAG framing, indicates if operators / provisioning systems should enable fast LACP.' )
                            ->value( 1 )
                        ?>
                    </div>

                    <?php if ($t->vi && $t->vi->bundleName() ): ?>
                        <div class="form-group row">
                            <label for="custid" class="control-label col-sm-4">Bundle Name</label>
                            <div class="col-sm-6">
                                <label class="">
                                    <b>
                                        <code>
                                            <?= $t->ee( $t->vi->bundleName() ) ?>
                                        </code>
                                    </b>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($t->vi && $t->vi->type() ): ?>
                        <div class="form-group row">
                            <label class="control-label col-sm-4">Type</label>

                            <div class="col-sm-6">

                                <span class="badge <?php if( $t->vi->typePeering() ): ?> badge-success <?php elseif( $t->vi->typeFanout() ): ?>badge-secondary <?php elseif( $t->vi->typeReseller() ): ?>badge-dark <?php else: ?>badge-info <?php endif; ?>">
                                    <?= $t->vi->resolveType() ?>
                                </span>

                                <?php if( $t->vi->physicalInterfaces()->count() === 1 ):
                                    $pi = $t->vi->physicalInterfaces()->first(); /** @var \IXP\Models\PhysicalInterface $pi */
                                    ?>
                                    <?php if( $t->vi->typePeering() && $pi->fanoutPhysicalInterface ): ?>
                                        <span class="ml-2">
                                            <a href="<?= route( 'virtual-interface@edit' , [ 'vi' => $pi->fanoutPhysicalInterface->virtualInterface->id ]) ?>" >
                                                See fanout port
                                            </a>
                                        </span>
                                    <?php elseif( $t->vi->typeFanout() && $pi->peeringPhysicalInterface ): ?>
                                        <span class="ml-2">
                                            <a href="<?= route( 'virtual-interface@edit' , [ 'vi' => $pi->peeringPhysicalInterface->virtualInterface->id ]) ?>" >
                                                See peering port
                                            </a>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div id='advanced-area' class="col-sm-6 mt-4 mt-sm-0 collapse">
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

                    <?= Former::number( 'channelgroup' )
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
                    ->value( $t->vi->id ?? null )
                ?>

                <?php if( $t->vi ): ?>
                    <?= Former::hidden( 'custid' )
                        ->id( "custid" )
                        ->forceValue( $t->vi->customer->id )
                    ?>
                <?php endif; ?>

                <?php if( $t->selectedCust ): ?>
                    <?= Former::hidden( 'selectedCust' )
                        ->forceValue( $t->selectedCust->id )
                    ?>
                <?php endif; ?>

                <?php
                if( $t->cb ) {
                    $bbtn = '<a href="' . route( 'core-bundle@edit', [ 'cb' => $t->cb->id ] ) . '" class="btn btn-secondary mb-2 mb-md-2 mb-lg-0">Return to Core Bundle</a>';
                } elseif( $t->vi ) {
                    $bbtn  = '<a href="' . route( "customer@overview" , [ 'cust' => $t->vi->customer->id, "tab" => "ports" ] ) . '" class="btn btn-secondary mb-2 mb-md-2 mb-lg-0">Return to ' . ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' Overview</a>';
                    $related = $t->resellerMode() && ( count( $t->vi->peeringPhysicalInterface() ) || count( $t->vi->fanoutPhysicalInterface() ) ) ? "data-related='1'" : '';
                    $bbtn .= '<a class="collapse btn btn-danger mb-2 mb-md-2 mt-lg-2 ml-1 btn-delete-vi"  id="delete-vi-' . $t->vi->id . '" href="' . route( 'virtual-interface@delete', [ 'vi' => $t->vi->id ]  ) . '" ' . $related . ' >Delete Interface</a>';
                } else {
                    $bbtn = '<a href="' . route( 'virtual-interface@list' ) . '" class="btn btn-secondary mmb-2 mb-md-2 mb-lg-0" >Cancel</a>';
                }
                ?>

                <?=
                Former::actions(
                    Former::primary_submit( $t->vi ? 'Save Changes' : 'Create' )->class( "mb-2 mb-md-2 mb-lg-0" )->id( "submit-form" ),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-md-2 mb-lg-0" ),
                    '<a class="btn btn-secondary mb-2 mb-md-2 mb-lg-0" href="#" id="advanced-options">Advanced Options</a>',
                    $bbtn
                )->id('btn-group')?>

                <?= Former::close() ?>
            </div>
        </div>
    </div>
</div>
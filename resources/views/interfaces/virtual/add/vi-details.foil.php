<div class="well">
    <?php if( $t->cb ): ?>
        <div class="alert alert-warning" role="alert">
            The Virtual Interface belongs to a Core Bundle: ensure you match any changes to MTU, 802.1q framing, etc. to the other half which can be
            <a href="<?= route('core-bundle/edit' , [ 'id' => $t->cb->getId() ]) ?>"> accessed by clicking here </a>
        </div>
    <?php endif; ?>
    <?= Former::open()->method( 'POST' )
        ->action( route( 'interfaces/virtual/store' ) )
        ->customWidthClass( 'col-sm-6' )
    ?>
    <div class="col-sm-6">
        <?= Former::select( 'cust' )
            ->label( 'Customer' )
            ->fromQuery( $t->cust, 'name' )
            ->placeholder( 'Choose a Customer' )
            ->addClass( 'chzn-select' )
            ->disabled( $t->selectedCust ? true : false )
            ->blockHelp( 'The customer who owns this virtual interface.' );
        ?>

        <?= Former::checkbox( 'trunk' )
            ->label( '&nbsp;' )
            ->text( 'Use 802.1q framing' )
            ->blockHelp( 'Indicates if operators / provisioning systems should configure this port with 802.1q framing / tagged packets.' )
            ->value( 1 );
        ?>

        <?php if( $t->vi && count( $t->vi->getPhysicalInterfaces() ) > 1 && !$t->vi->getLagFraming() ): ?>
            <div class="alert alert-warning" role="alert">
                <span class="label label-warning">WARNING</span>
                LAG framing is not set and there is >1 physical interfaces. This may be intended but should be verified:
            </div>
        <?php endif; ?>

        <?= Former::checkbox( 'lag_framing' )
            ->label( '&nbsp;' )
            ->text( 'Link aggregation / LAG framing' )
            ->blockHelp( 'Indicates if operators / provisioning systems should enable LAG framing such as LACP. <br/><br/>Mandatory where there is more than one phsyical interface.<br/><br/> Otherwise optional where a member requests a single member LAG for ease of upgrades.' )
            ->value( 1 );
        ?>

        <div id='fastlacp-area' style="<?= old( 'fastlacp' ) || $t->vi && $t->vi->getLagFraming() ? "" : "display: none" ?>">
            <?= Former::checkbox( 'fastlacp' )
                ->label( '&nbsp;' )
                ->text( 'Use Fast LACP' )
                ->blockHelp( 'When LACP is used for LAG framing, indicates if operators / provisioning systems should enable fast LACP.' )
                ->value( 1 )
            ?>
        </div>

        <?php if ($t->vi && $t->vi->getBundleName() ): ?>
            <div class="form-group">
                <label for="custid" class="control-label col-sm-4">Bundle Name</label>
                <div class="col-sm-6">
                    <label class="control-label">
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

            <div class="form-group">
                <label class="control-label col-sm-4">Type</label>

                <div class="col-sm-6">

                    <label class="control-label">

                        <span class="label <?php if( $t->vi->isTypePeering() ): ?> label-success <?php elseif( $t->vi->isTypeFanout() ): ?>label-default <?php endif; ?>">
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

                    </label>

                </div>

            </div>

        <?php endif; ?>

        <hr>
    </div>

    <div id='advanced-area' class="col-sm-6" style="display: none">
        <?= Former::text( 'name' )
            ->label( 'Virtual Interface Name' )
            ->blockHelp( 'Ordinarily this is left blank. In the case of LAGs with provisioning systems, this is used to indicate the '
                . 'interface base name for LAGs. E.g. on a Cisco, this would be <em>Port-Channel</em>.' );
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
            ->value( $t->vi->getcustomer()->getId() )
        ?>
    <?php endif; ?>

    <?php if( $t->selectedCust ): ?>
        <?= Former::hidden( 'selectedCust' )
            ->value( $t->selectedCust->getId() )
        ?>
    <?php endif; ?>


    <?php
        if( $t->cb ) {
            $bbtn = '<a style="margin-left: 5px;" href="' . route( 'core-bundle/edit', [ 'id' => $t->cb->getId() ] ) . '" class="btn btn-default">Return to Core Bundle</a>';
        } elseif( $t->vi ) {
            $bbtn  = '<a style="margin-left: 5px;" href="' . route( "customer@overview" , [ "id" => $t->vi->getCustomer()->getId(), "tab" => "ports" ] ) . '" class="btn btn-default">Return to Customer Overview</a>';
            $bbtn .= '<a style="margin-left: 5px; display: none;" class="btn btn btn-danger pull-right" id="delete-vi-' . $t->vi->getId() . '" href="">Delete Interface</a>';
        } else {
            $bbtn = '<a style="margin-left: 5px;" href="' . action( 'Interfaces\VirtualInterfaceController@list' ) . '" class="btn btn-default">Cancel</a>';
        }
    ?>

    <?=
        Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::success_button( 'Help' )->id( 'help-btn' ),
            '<a class="btn btn-default" id="advanced-options">Advanced Options</a>',
            $bbtn
        )->id('btn-group');?>

    <?= Former::close() ?>
</div>

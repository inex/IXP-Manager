<div class="well">
    <?php if( $t->cb ): ?>
        <div class="alert alert-warning" role="alert">
            The Virtual Interface belongs to a Core Bundle: ensure you match any changes to MTU, 802.1q framing, etc. to the other half which can be
            <a href="<?= route('core-bundle/edit' , [ 'id' => $t->cb->getId() ]) ?>"> accessed by clicking here </a>
        </div>
    <?php endif; ?>
    <?= Former::open()->method( 'POST' )
        ->action( action( 'Interfaces\VirtualInterfaceController@store' ) )
        ->customWidthClass( 'col-sm-6' )
    ?>
    <div class="col-sm-6">
        <?= Former::select( 'cust' )
            ->label( 'Customer' )
            ->fromQuery( $t->cust, 'name' )
            ->placeholder( 'Choose a Customer' )
            ->addClass( 'chzn-select' )
            ->disabled( $t->selectedCust ? true : false )
            ->blockHelp( '' );
        ?>

        <?= Former::checkbox( 'trunk' )
            ->label( '&nbsp;' )
            ->text( 'Use 802.1q framing' )
            ->blockHelp( 'Indicates if operators / provisioning systems should configure this port with 802.1q framing / tagged packets.' )
            ->check( $t->vi ? $t->vi->getTrunk() : false )
            ->addClass( 'col-sm-6' );

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
            ->check( $t->vi ? $t->vi->getLagFraming() : false );
        ?>

        <div id='fastlacp-area' style="<?= $t->vi && $t->vi->getLagFraming() ? "" : "display: none" ?>">
            <?= Former::checkbox( 'fastlacp' )
                ->label( '&nbsp;' )
                ->text( 'Use Fast LACP' )
                ->blockHelp( '' )
                ->check( $t->vi ? $t->vi->getFastLACP() : false );
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
                        <?php if( count( $t->vi->getPhysicalInterfaces()  ) ) : ?>
                            <?php foreach( $t->vi->getPhysicalInterfaces() as $pi ):
                                /** @var Entities\PhysicalInterface $pi */ ?>
                                <?php if( $t->vi->isTypePeering() && $pi->getFanoutPhysicalInterface() ) : ?>
                                    <span style="margin-left: 15px;">
                                                <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi->getFanoutPhysicalInterface()->getVirtualInterface()->getId() ]) ?>" >
                                                    See <?= $t->vi->resolveType() ?> port
                                                </a>
                                            </span>
                                <?php endif; ?>
                                <?php if( $t->vi->isTypeFanout() && $pi->getPeeringPhysicalInterface() ) : ?>
                                    <span style="margin-left: 15px;">
                                                <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi->getPeeringPhysicalInterface()->getVirtualInterface()->getId() ]) ?>" >
                                                    See <?= $t->vi->resolveType() ?> port
                                                </a>
                                            </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
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
            ->blockHelp( 'help text' );
        ?>

        <?= Former::text( 'description' )
            ->label( 'Description' )
            ->blockHelp( 'help text' );
        ?>

        <?= Former::number( 'channel-group' )
            ->label( 'Channel Group Number' )
            ->blockHelp( 'help text' );
        ?>

        <?= Former::number( 'mtu' )
            ->label( 'MTU' )
            ->blockHelp( 'help text' );
        ?>
    </div>

    <?= Former::hidden( 'id' )
        ->value( $t->vi ? $t->vi->getId() : null )
    ?>

    <?php if( $t->selectedCust ): ?>
        <?= Former::hidden( 'selectedCust' )
            ->value( $t->selectedCust->getId() )
        ?>
    <?php endif; ?>


    <?=Former::actions(
        Former::primary_submit( 'Save Changes' ),
        Former::success_button( 'Help' )->id( 'help-btn' ),
        '<a class="btn btn-default" id="advanced-options">Advanced Options</a>'
    )->id('btn-group');?>

    <?= Former::close() ?>
</div>

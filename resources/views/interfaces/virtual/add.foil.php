<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action( 'Interfaces\VirtualInterfaceController@list' )?>">(Virtual) Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Add New Virtual Interface</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class=" btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= action( 'Interfaces\VirtualInterfaceController@list' )?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-plus"></i> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a id="" href="<?= action( 'Interfaces\VirtualInterfaceController@wizard' )?>" >
                        Add Interface Wizard...
                    </a>
                </li>
                <li>
                    <a id="" href="<?= action( 'Interfaces\VirtualInterfaceController@add' )?>" >
                        Virtual Interface Only...
                    </a>
                </li>
            </ul>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>
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
                ->blockHelp( '' );
            ?>

            <?= Former::checkbox( 'trunk' )
                ->label( 'Use 802.1q framing' )
                ->blockHelp( 'Indicates if operators / provisioning systems should configure this port with 802.1q framing / tagged packets.' )
                ->check( $t->vi ? $t->vi->getTrunk() : false )
                ->addClass( 'col-sm-6' );

            ?>

            <?= Former::checkbox( 'lag_framing' )
                ->label( 'Link aggregation / LAG framing' )
                ->blockHelp( 'Indicates if operators / provisioning systems should enable LAG framing such as LACP. <br/><br/>Mandatory where there is more than one phsyical interface.<br/><br/> Otherwise optional where a member requests a single member LAG for ease of upgrades.' )
                ->check( $t->vi ? $t->vi->getLagFraming() : false );
            ?>

            <div id='fastlacp-area' style="display: none">
                <?= Former::checkbox( 'fastlacp' )
                    ->label( 'Use Fast LACP' )
                    ->blockHelp( '' )
                    ->check( $t->vi ? $t->vi->getFastLACP() : false );
                ?>
            </div>

            <?= Former::checkbox( 'advanced-options' )
                ->label( 'Advanced Options' )
                ->blockHelp( '' );

            ?>

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
                                <span class="label <?php if( $t->vi->isTypePeering() ): ?> label-success <?php elseif( $t->vi->isTypeFanout() ): ?>label-inverse <?php endif; ?>">
                                    <?= $t->vi->resolveType() ?>
                                </span>
                            <?php if( count( $t->vi->getPhysicalInterfaces()  ) ) : ?>
                                <?php foreach( $t->vi->getPhysicalInterfaces() as $pi ):
                                    /** @var Entities\PhysicalInterface $pi */ ?>
                                    <?php if( $t->vi->isTypePeering() && $pi->getFanoutPhysicalInterface() ) : ?>
                                        <span style="margin-left: 15px;">
                                                <a href="">
                                                    See <?= $t->vi->resolveType() ?> port
                                                </a>
                                            </span>
                                    <?php endif; ?>
                                    <?php if( $t->vi->isTypeFanout() && $pi->getPeeringPhysicalInterface() ) : ?>
                                        <span style="margin-left: 15px;">
                                                <a href="">
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

        <?=Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( action( 'Interfaces\VirtualInterfaceController@list' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->id('btn-group');?>

        <?= Former::close() ?>
    </div>

<?php if( $t->vi ): ?>
    <div class="row-fluid">
        <h3>
            Physical Interfaces
            <a class="btn btn-default btn-xs" href="<?= route('interfaces/physical/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                <i class="glyphicon glyphicon-plus"></i>
            </a>
        </h3>
        <div id="message-pi"></div>
        <div class="" id="area-pi">
            <?php if( count( $t->vi->getPhysicalInterfaces()  ) ) : ?>
                <?php if( !$t->vi->sameSwitchForEachPI() ): ?>
                    <div class="alert alert-warning" role="alert">
                        The physical interfaces don't have the same switches !
                    </div>
                <?php endif; ?>

                <table id="table-pi" class="table table-bordered">
                    <tr style="font-weight: bold">
                        <td>
                            Location
                        </td>
                        <td>
                            Peering Port
                        </td>
                        <?php if( !$t->cb ): ?>
                            <td>
                                Fanout Port
                            </td>
                        <?php endif; ?>
                        <td>
                            Speed/Duplex
                        </td>
                        <?php if( $t->cb ): ?>
                            <td>
                                Peering Port other side ( Core Bundle )
                            </td>
                        <?php endif; ?>
                        <td>
                            Action
                        </td>
                    </tr>
                    <?php foreach( $t->vi->getPhysicalInterfaces() as $pi ):
                        /** @var Entities\PhysicalInterface $pi */ ?>
                        <tr>
                            <td>
                                <?php if( $pi->getSwitchPort()->getSwitcher()->getCabinet() ): ?>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ( $pi->getSwitchPort()->getType() != \Entities\SwitchPort::TYPE_FANOUT ): ?>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $pi->getSwitchPort()->getIfName() ?>
                                <?php elseif( $pi->getPeeringPhysicalInterface() ): ?>
                                    <a href="#">
                                        <?= $t->ee( $pi->getPeeringPhysicalInterface()->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $pi->getPeeringPhysicalInterface()->getSwitchPort()->getIfName() ?>
                                    </a>
                                <?php endif; ?>

                                <?php if( $t->cb ): ?>

                                    <?php if( $pi->getOtherPICoreLink()->getSwitchPort()->getSwitcher()->getId() == $pi->getSwitchPort()->getSwitcher()->getId( ) ): ?>
                                        <span class="label label-danger"> Same switch for other side !</span>
                                    <?php endif; ?>

                                <?php endif; ?>

                            </td>
                            <?php if( !$t->cb ): ?>
                                <td>
                                    <?php if ( $pi->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_FANOUT ): ?>
                                        <?= $pi->getSwitchPort()->getSwitcher()->getName() ?> :: <?= $int->getSwitchPort()->getIfName() ?>
                                    <?php elseif( $pi->getFanoutPhysicalInterface() ): ?>
                                        <a href="">
                                            <?= $t->ee( $pi->getFanoutPhysicalInterface()->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $pi->getFanoutPhysicalInterface()->getSwitchPort()->getIfName() ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?= $pi->getSpeed() ?> / <?= $pi->getDuplex() ?>
                                <?php if ( $pi->getAutoneg() ): ?>
                                    <span class="badge phys-int-autoneg-state" data-toggle="tooltip" title="Auto-Negotiation Enabled">AN</span>
                                <?php else: ?>
                                    <span class="badge phys-int-autoneg-state" data-toggle="tooltip" title="Hard-Coded - Auto-Negotiation DISABLED">HC</span>
                                <?php endif; ?>
                            </td>
                            <?php if( $t->cb ): ?>
                                <td>
                                    <?= $pi->getOtherPICoreLink()->getSwitchPort()->getSwitcher()->getName() ?> :: <?= $pi->getOtherPICoreLink()->getSwitchPort()->getIfName() ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn btn-default" href="<?= route( 'interfaces/physical/edit' , [ 'id' => $pi->getId() ] )?>" title="Edit">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </a>

                                    <a class="btn btn btn-default" id="delete-pi-<?= $pi->getId()?>" href="" title="Delete Physical Interface">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </table>
            <?php else: ?>
                <div id="table-pi" class="alert alert-warning" role="alert">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    <span class="sr-only">Information :</span>
                    There are no physical interfaces defined for this virtual interface.
                    <a href="<?= route('interfaces/physical/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                        Add one now...
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <?php if( !$t->cb ): ?>
        <div class="row-fluid">
            <h3>
                VLAN Interfaces
                <a class="btn btn-default btn-xs" href="<?= route('interfaces/vlan/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </h3>
            <div id="message-vli"></div>
            <div class="" id="area-vli">
                <?php if( count( $t->vi->getVlanInterfaces()  ) ) : ?>
                    <table id="table-vli" class="table table-bordered">
                        <tr style="font-weight: bold">
                            <td>
                                VLAN Name
                            </td>
                            <td>
                                VLAN Tag
                            </td>
                            <td>
                                Layer2 Address
                            </td>
                            <td>
                                IPv4 Address
                            </td>
                            <td>
                                IPv6 Address
                            </td>
                            <td>
                                Action
                            </td>
                        </tr>
                        <?php foreach( $t->vi->getVlanInterfaces() as $vli ):
                            /** @var Entities\VlanInterface $vli */ ?>
                            <tr>
                                <td>
                                    <?= $t->ee( $vli->getVlan()->getName() ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $vli->getVlan()->getNumber() )?>
                                </td>
                                <td>
                                    <a href="<?= action ( 'Layer2AddressController@index' , [ 'id' => $vli->getId() ] )?> " >
                                        <?php if ( !count( $vli->getLayer2Addresses() ) ) : ?>
                                            <span class="label btn-warning">(none)</span>
                                        <?php elseif ( count( $vli->getLayer2Addresses() ) > 1 ) : ?>
                                            <span class="label btn-warning">(multiple)</span>
                                        <?php else: ?>
                                            <?php $l2a = $vli->getLayer2Addresses() ?>
                                            <?= $l2a[0]->getMacFormattedWithColons() ?>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if( $vli->getIPv4Enabled() and $vli->getIPv4Address() ) : ?>
                                        <?=  $t->ee( $vli->getIPv4Address()->getAddress() ) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if( $vli->getIPv6Enabled() and $vli->getIPv6Address() ) : ?>
                                        <?=  $t->ee( $vli->getIPv6Address()->getAddress() ) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn btn-default" href="<?= route ( 'interfaces/vlan/edit', [ 'id' => $vli->getId() ] ) ?>" title="Edit">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>

                                        <a class="btn btn btn-default" id="delete-vli-<?= $vli->getId()?>" href="" title="Delete Vlan Interface">
                                            <i class="glyphicon glyphicon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <div id="table-vli" class="alert alert-warning" role="alert">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        <span class="sr-only">Information :</span>
                        There are no VLAN interfaces defined for this virtual interface.
                        <a href="<?= route('interfaces/vlan/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                            Add one now...
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if( !$t->cb ): ?>
        <div class="row-fluid">
            <h3>
                Sflow Receivers
                <a class="btn btn-default btn-xs" href="<?= route('interfaces/sflow-receiver/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>"><i class="glyphicon glyphicon-plus"></i></a>
            </h3>
            <div id="message-sflr"></div>
            <div id="area-sflr">
                <?php if( count( $t->vi->getSflowReceivers() ) ) : ?>
                    <table id="table-sflr" class="table table-bordered">
                        <tr>
                            <th>
                                Target IP
                            </th>
                            <th>
                                Target Port
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                        <?php foreach( $t->vi->getSflowReceivers() as $sflr ):
                            /** @var Entities\SflowReceiver $sflr */ ?>
                            <tr>
                                <td>
                                    <?= $t->ee( $sflr->getDstIp() ) ?>
                                </td>
                                <td>
                                    <?= $sflr->getDstPort() ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn btn-default" href="<?= route('interfaces/sflow-receiver/edit' , [ 'id' => $sflr->getId() ] ) ?>">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                        <a class="btn btn btn-default" id="delete-sflr-<?= $sflr->getId()?>">
                                            <i class="glyphicon glyphicon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <div id="table-sflr" class="alert alert-warning" role="alert">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        <span class="sr-only">Information :</span>
                        There are no Sflow receivers defined for this virtual interface.
                        <a href="<?= route('interfaces/sflow-receiver/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                            Add one now...
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/virtual/js/interface' ); ?>

    <script>
        $(document).ready( function() {

            <?php if( $t->cb ): ?>
                $( "#btn-group div" ).append('<a style="margin-left: 5px;" href="<?= route( 'core-bundle/edit' , [ 'id' => $t->cb->getId() ] ) ?>" class="btn btn-default">Return to Core Bundle</a>');
            <?php elseif( $t->vi ): ?>
                $( "#btn-group div" ).append('<a style="margin-left: 5px;" href="<?= url( 'customer/overview/tab/ports/id').'/'.$t->vi->getCustomer()->getId() ?>" class="btn btn-default">Return to Customer Overview</a>');
            <?php endif;?>

            $( 'label.col-lg-2' ).removeClass('col-lg-2');

            if ($( '#lag_framing' ).is(":checked") ) {
                $( "#fastlacp-area" ).slideDown();
            }

            if ( $( '#name' ).val() != '' || $( '#description' ).val() != '' || $( '#channel-group' ).val() != '' || $( '#mtu' ).val() != '' ) {
                $( "#advanced-options" ).prop('checked', true);
                $( "#advanced-area" ).slideDown();
            }

        });

        /**
         * hide the help block at loading
         */
        $('p.help-block').hide();

        /**
         * display / hide help sections on click on the help button
         */
        $( "#help-btn" ).click( function() {
            $( "p.help-block" ).toggle();
        });

        /**
         * display or hide the fastlapc area
         */
        $( '#lag_framing' ).change( function(){
            if( this.checked ){
                $( "#fastlacp-area" ).slideDown();
            } else {
                $( "#fastlacp-area" ).slideUp();
            }
        });

        /**
         * display or hide the advanced area
         */
        $( '#advanced-options' ).change( function(){
            if( this.checked ){
                $( "#advanced-area" ).slideDown();
            } else {
                $( "#advanced-area" ).slideUp();
            }
        });

        <?php if( $t->vi ): ?>

            /**
             * on click even allow to delete a Sflow receiver
             */
            $(document).on('click', "a[id|='delete-pi']" ,function(e){
                e.preventDefault();
                var piid = (this.id).substring(10);
                deletePopup( piid, <?= $t->vi->getId() ?> , 'pi' );
            });

            /**
             * on click even allow to delete a Sflow receiver
             */
            $(document).on('click', "a[id|='delete-vli']" ,function(e){
                e.preventDefault();
                var vliid = (this.id).substring(11);
                deletePopup( vliid, <?= $t->vi->getId() ?>, 'vli' );
            });

            /**
             * on click even allow to delete a Sflow receiver
             */
            $(document).on('click', "a[id|='delete-sflr']" ,function(e){
                e.preventDefault();
                var sflrid = (this.id).substring(12);
                deletePopup( sflrid, <?= $t->vi->getId() ?>, 'sflr' );
            });


        <?php endif;?>
    </script>
<?php $this->append() ?>
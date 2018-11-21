<div class="col-sm-12">

    <div class="col-sm-6">

        <h3>
            Connection <?= $t->nbVi ?>

            <?php $vlis = $t->vi->getVlanInterfaces() ?>

            <?php if( count( $vlis ) ): ?>
                <?php $vli = $vlis[ 0 ] ?>
            <?php else: ?>
                <?php $vli = 0 ?>
            <?php endif; ?>

            <small>
                <?php $pis = $t->vi->getPhysicalInterfaces() ?>

                <?php if( count( $pis ) ): ?>
                    <?php $firstPi = $pis[ 0 ] ?>
                <?php else: ?>
                    <?php $firstPi = 0 ?>
                <?php endif; ?>

                <?php if( $t->vi->getType() == \Entities\SwitchPort::TYPE_PEERING && count( $pis ) ): ?>
                &nbsp;&nbsp;&nbsp;&nbsp;<?= $t->ee( $firstPi->getSwitchPort()->getSwitcher()->getInfrastructure()->getName() ) ?>
                <?php elseif( $t->vi->getType() == \Entities\SwitchPort::TYPE_FANOUT ): ?>
                &nbsp;&nbsp;&nbsp;&nbsp;Reseller Fanout

                    <?php if( count( $pis ) && $firstPi->getRelatedInterface() ): ?>
                    for <a

                        <?php if( Auth::user()->getPrivs() == \Entities\User::AUTH_SUPERUSER ): ?>
                            href="<?= route( "customer@overview" , [ 'id' => $firstPi->getRelatedInterface()->getVirtualInterface()->getCustomer()->getId() ] ) ?>"
                        <?php else: ?>
                            href="<?= route( "customer@detail" , [ "id" => $firstPi->getRelatedInterface()->getVirtualInterface()->getCustomer()->getId() ] ) ?>"
                        <?php endif; ?>

                        ><?= $t->ee( $firstPi->getRelatedInterface()->getVirtualInterface()->getCustomer()->getAbbreviatedName() ) ?></a>
                    <?php else: ?>
                        <em>(unassigned)</em>
                    <?php endif; ?>

                <?php elseif( $t->vi->getType() == \Entities\SwitchPort::TYPE_RESELLER ): ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Resller Uplink
                <?php endif; ?>

                <?php if( count( $t->vi->getPhysicalInterfaces() ) > 1 ): ?>
                    <?php $isLAG = 1 ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LAG Port
                <?php else: ?>
                    <?= $t->insert( 'customer/overview-tabs/ports/pi-status', [ 'pi' => $firstPi, 'vi' => $t->vi ] ); ?>
                    <?php $isLAG = 0 ?>
                <?php endif; ?>
            </small>

            <?php if( Auth::getUser()->isSuperUser() ): ?>

                <div class="btn-group" style="padding-left: 20px;">
                    <a class="btn btn-xs btn-default" href="<?= route( "interfaces/virtual/edit", [ "id" => $t->vi->getId() ] ) ?>" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
                </div>

            <?php endif; ?>

        </h3>



        <?php if( count( $t->vi->getPhysicalInterfaces() ) > 0 ): ?>
            <?php $countPi = 1 ?>
            <?php foreach( $t->vi->getPhysicalInterfaces() as $pi ): ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?php if( $isLAG ): ?>
                            <h5>
                                Port <?= $countPi ?> of <?= count( $t->vi->getPhysicalInterfaces() ) ?> in LAG
                                <?= $t->insert( 'customer/overview-tabs/ports/pi-status', [ 'pi' => $pi ] ); ?>
                            </h5>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-borderless">
                            <tr>
                                <td>
                                    <b>Switch:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getName() ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Speed:</b>
                                </td>
                                <td>
                                    <?= $pi->resolveSpeed() ?>
                                    <?php if( $pi->getDuplex() != 'full' ): ?>
                                        (HD)
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <?php if( $pi->getSwitchPort()->getSwitcher()->getCabinet() ): ?>
                                <tr>
                                    <td>
                                        <b>Location:</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php if( $pi->getSwitchPort()->getPatchPanelPort() ): ?>
                                <tr>
                                    <td>
                                        <b>XConnect Port:</b>
                                    </td>
                                    <td class="wrap">
                                        <?= $t->ee( $pi->getSwitchPort()->getPatchPanelPort()->getPatchPanel()->getColoReference() ) ?> -

                                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                                            <a href="<?= route( "patch-panel-port/list/patch-panel" , [ "id" => $pi->getSwitchPort()->getPatchPanelPort()->getPatchPanel()->getId() ] ) ?>">
                                                <?= $t->ee( $pi->getSwitchPort()->getPatchPanelPort()->getName() ) ?>
                                            </a>
                                        <?php else: ?>
                                            <?= $t->ee( $pi->getSwitchPort()->getPatchPanelPort()->getName() ) ?>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-borderless">
                            <tr>
                                <td>
                                    <b>Switch Port:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getName() ) ?>
                                </td>
                            </tr>
                            <tr>
                                <?php if( $pi->getSwitchPort()->getSwitcher()->getMauSupported() ): ?>
                                    <td>
                                        <b>Media:</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $pi->getSwitchPort()->getMauType() ) ?>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <b>Duplex:</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $pi->getDuplex() ) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php if( $pi->getSwitchPort()->getSwitcher()->getCabinet() ): ?>
                                <tr>
                                    <td>
                                        <b>
                                            Colo Cabinet ID:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getName() ) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if( $pi->getSwitchPort()->getPatchPanelPort() ): ?>
                                <tr>
                                    <td>
                                        <b>XConnect Status:</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $pi->getSwitchPort()->getPatchPanelPort()->resolveStates() ) ?>
                                        <?php if( $pi->getSwitchPort()->getPatchPanelPort()->getState() == \Entities\PatchPanelPort::STATE_CONNECTED ): ?>
                                            <?= $pi->getSwitchPort()->getPatchPanelPort()->getConnectedAtFormated() ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                <?php $countPi++ ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="row">
                <p>
                    No physical interfaces defined.
                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <a href="<?= route( "interfaces/physical/add", [ "id" =>  0 , "viid" => $t->vi->getId() ] ) ?>">Add one...</a>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if( count( $t->vi->getVlanInterfaces() ) > 0 ): ?>
            <?php foreach( $t->vi->getVlanInterfaces() as $vli ): ?>
                <?php $vlanid =$vli->getVlan()->getId() ?>
                <?php if( $vli->getVlan()->getPrivate() ): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php if( !isset( $pvlans ) ): ?>
                                <?php $pvlans = $t->c->getPrivateVlanDetails() ?>
                            <?php endif; ?>
                            <h4>
                                &nbsp;&nbsp;&nbsp;Private VLAN Service
                                <small><?= config( "identity.orgname" ) ?> Reference: #<?= $vli->getVlan()->getId() ?></small>
                            </h4>

                            <table class="table table-borderless">
                                <tr>
                                    <td>
                                        <b>Name</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $vli->getVlan()->getName() ) ?>
                                    </td>


                                    <td>
                                        <b>Tag</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $vli->getVlan()->getNumber() ) ?>
                                    </td>

                                    <td>
                                        <b>Other Members:</b>
                                    </td>
                                    <td>

                                        <?php if( count( $pvlans[ $vli->getVlan()->getId() ][ 'members'] ) == 1 ): ?>
                                            <em>None - single member</em>
                                        <?php else: ?>
                                            <?php foreach( $pvlans[ $vli->getVlan()->getId() ][ 'members'] as $m ): ?>
                                                <?= $t->ee( $m->getAbbreviatedName() )?> <br />
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br />
                <?php else: ?>
                    <div class="row">
                        <h4><?= $t->ee( $vli->getVlan()->getName() ) ?>:</h4>
                        <div class="col-sm-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td>
                                        <b>
                                            IPv6 Address:
                                        </b>
                                    </td>
                                    <td>
                                        <?php if( $vli->getIpv6enabled() and $vli->getIpv6address() ): ?>
                                            <?= $t->ee( $vli->getIPv6Address()->getAddress() ) ?> <?php if( isset( $netinfo[ $vlanid ][ 6 ][ 'masklen' ] ) ) : ?> /<?= $netinfo[ $vlanid ][ 6 ][ "masklen" ] ?> <?php endif;?>
                                        <?php else: ?>
                                            IPv6 not enabled.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            Multicast Enabled:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $vli->getMcastenabled() ? "Yes" : "No" ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            Route Server Client:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $vli->getRsclient() ? "Yes" : "No" ?>
                                    </td>
                                </tr>
                                <?php if( $t->as112UiActive() ): ?>
                                    <tr>
                                        <td>
                                            <b>
                                                AS112 Client:
                                            </b>
                                        </td>
                                        <td>
                                            <?= $vli->getAs112client() ? "Yes" : "No" ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td>
                                        <b>IPv4 Address:</b>
                                    </td>
                                    <td>
                                        <?php if( $vli->getIpv4enabled() and $vli->getIpv4address() ): ?>
                                            <?= $t->ee( $vli->getIPv4Address()->getAddress() ) ?> <?php if( isset( $netinfo[ $vlanid ][ 4 ][ 'masklen' ] ) ) : ?> /<?= $netinfo[ $vlanid ][ 4 ][ "masklen" ] ?> <?php endif;?>
                                        <?php else: ?>
                                            IPv4 not enabled.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            Mac Address:
                                        </b>
                                    </td>
                                    <td>
                                        <?php foreach( $vli->getLayer2AddressesAsArray() as $l2a ): ?>
                                            <?= $l2a ?><br />
                                        <?php endforeach; ?>
                                        <?php if( count( $vli->getLayer2AddressesAsArray() ) > 0 && config( 'ixp_fe.layer2-addresses.customer_can_edit' ) ): ?>
                                            <a href="<?= route( "layer2-address@forVlanInterface", [ "id" => $vli->getId() ] ) ?>">Edit</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <?php if( $t->vi->isTypePeering() ): ?>
                <div class="row">
                    <p>
                        No VLAN interfaces defined.
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>

    <div class="col-sm-6">
        <?php if( $isLAG ): ?>

            <?php
                if( $t->vi->isGraphable() ): ?>

                    <div class="well">
                        <h4>
                            Aggregate Day Graph for LAG
                            <a class="btn btn-default btn-xs pull-right" href="<?= route( "statistics@member-drilldown", [ 'type' => 'vi', 'typeid' => $t->vi->getId() ] ) ?>">
                                <i class="glyphicon glyphicon-zoom-in"></i>
                            </a>
                        </h4>
                        <br />
                        <?= $t->grapher->virtint( $t->vi )->renderer()->boxLegacy() ?>
                    </div>

                <?php endif; ?>

        <?php endif; ?>


        <?php foreach( $t->vi->getPhysicalInterfaces() as $pi ): ?>
            <?php if( !$pi->isGraphable() ) { continue; } ?>

            <div class="well">
                <h4>
                    Day Graph for <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getName() ) ?> / <?= $t->ee( $pi->getSwitchPort()->getName() ) ?>
                    <a class="btn btn-default btn-xs pull-right" href="<?= route( "statistics@member-drilldown", [ 'type' => 'pi', 'typeid' => $pi->getId() ] ) ?>">
                        <i class="glyphicon glyphicon-zoom-in"></i>
                    </a>
                </h4>
                <br />
                <?= $t->grapher->physint( $pi )->renderer()->boxLegacy() ?>
            </div>
        <?php endforeach; ?>

    </div>
</div>
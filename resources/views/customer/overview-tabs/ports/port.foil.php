<div class="row col-sm-12">
    <div class="col-lg-6 col-md-12">
        <div class="d-flex">
            <h3 class="mr-auto">
                Connection <?= $t->nbVi ?>

                <small>
                    <?php
                        $vlis       = $t->vi->vlanInterfaces;
                        $vli        = $vlis[ 0 ] ?? 0 /** @var $vli \IXP\Models\VlanInterface */;

                        $pis        = $t->vi->physicalInterfaces;
                        $countPis   = $pis->count();
                        $firstPi    = $pis[ 0 ] ?? 0 /** @var $firstPi \IXP\Models\PhysicalInterface */
                    ?>

                    <?php if( $t->vi->typePeering() && $countPis ): ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;<?= $t->ee( $firstPi->switchPort->switcher->infrastructureModel->name ) ?>
                    <?php elseif( $t->vi->typeFanout() ): ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;Reseller Fanout
                        <?php if( $countPis && $related = $firstPi->relatedInterface() ): ?>
                            for <a href="<?= route( $t->isSuperUser ? "customer@overview" : "customer@detail" , [ 'cust' => $related->virtualInterface->custid ] ) ?>">
                                <?= $t->ee( $related->virtualInterface->customer->abbreviatedName ) ?>
                        </a>
                        <?php else: ?>
                            <em>(unassigned)</em>
                        <?php endif; ?>
                    <?php elseif( $t->vi->typeReseller() ): ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reseller Uplink
                    <?php endif; ?>

                    <?php if( $countPis > 1 ): ?>
                        <?php $isLAG = 1 ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LAG Port
                    <?php else: ?>
                        <?= $t->insert( 'customer/overview-tabs/ports/pi-status', [ 'pi' => $firstPi, 'vi' => $t->vi, 'isSuperUser' => $t->isSuperUser ] ); ?>
                        <?php $isLAG = 0 ?>
                    <?php endif; ?>
                </small>
            </h3>

            <?php if( $t->isSuperUser ): ?>
                <div class="btn-group my-auto">
                    <a class="btn btn-sm btn-white" href="<?= route( 'virtual-interface@edit', [ 'vi' => $t->vi->id ] ) ?>" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if( $countPis > 0 ): ?>
            <?php $countPi = 1 ?>

            <?php foreach( $pis as $pi ): ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?php if( $isLAG ): ?>
                            <h5>
                                Port <?= $countPi ?> of <?= $countPis ?> in LAG
                                <?= $t->insert( 'customer/overview-tabs/ports/pi-status', [ 'pi' => $pi, 'isSuperUser' => $t->isSuperUser ] ); ?>
                            </h5>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-12">
                        <table class="table table-sm table-borderless table-striped table-connection">
                            <tr>
                                <td>
                                    <b>Switch:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->switchPort->switcher->name ) ?>
                                </td>
                                <td>
                                    <b>Switch Port:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->switchPort->name ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Speed:</b>
                                </td>
                                <td>
                                    <?= $t->scaleSpeed( $pi->configuredSpeed() ) ?>
                                    <?php if( $pi->isRateLimited() ): ?>
                                        <span class="badge badge-info" data-toggle="tooltip" title="Rate Limited">RL</span>
                                    <?php endif; ?>
                                    <?php if( $pi->duplex !== 'full' ): ?>
                                        (HD)
                                    <?php endif; ?>
                                </td>
                                <?php if( $pi->switchPort->switcher->mauSupported ): ?>
                                    <td>
                                        <b>Media:</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $pi->switchPort->mauType ) ?>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <b>Duplex:</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $pi->duplex ) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>

                            <?php if( $cabinet = $pi->switchPort->switcher->cabinet ): ?>
                                <tr>
                                    <td>
                                        <b>Location:</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $cabinet->location->name ) ?>
                                    </td>
                                    <td>
                                        <b>
                                            Colo Cabinet ID:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $cabinet->name ) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php if( $ppp = $pi->switchPort->patchPanelPort ): ?>
                                <tr>
                                    <td>
                                        <b>XConnect Port:</b>
                                    </td>
                                    <td class="wrap">
                                        <?= $t->ee( $ppp->patchPanel->colo_reference ) ?> -

                                        <?php if( $t->isSuperUser ): ?>
                                            <a href="<?= route( 'patch-panel-port@list-for-patch-panel' , [ "pp" => $ppp->patch_panel_id ] ) ?>">
                                                <?= $t->ee( $ppp->name() ) ?>
                                            </a>
                                        <?php else: ?>
                                            <?= $t->ee( $ppp->name() ) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <b>XConnect Status:</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $ppp->states() ) ?>
                                        <?php if( $ppp->stateConnected() ): ?>
                                            <?= $ppp->connected_at ?>
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
            <div class="col-lg-12 mb-4">
                <p>
                    No physical interfaces defined.
                    <?php if( $t->isSuperUser ): ?>
                        <a href="<?= route( "physical-interface@create", [ "vi" => $t->vi->id ] ) ?>">
                          Create one...
                        </a>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if( $vlis->isNotEmpty() ): ?>
            <?php foreach( $vlis as $vli ): ?>
                <?php $vlanid = $vli->vlanid ?>
                <?php if( $vli->vlan->private ): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php if( !isset( $pvlans ) ): ?>
                                <?php $pvlans = $t->c->privateVlanDetails() ?>
                            <?php endif; ?>
                            <h4>
                                &nbsp;&nbsp;&nbsp;Private VLAN Service
                                <small>
                                    <?= config( "identity.orgname" ) ?> Reference: #<?= $vli->vlanid ?>
                                </small>
                            </h4>

                            <table class="table table-borderless">
                                <tr>
                                    <td>
                                        <b>Name</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $vli->vlan->name ) ?>
                                    </td>
                                    <td>
                                        <b>Tag</b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $vli->vlan->number ) ?>
                                    </td>
                                    <td>
                                        <b>Other Members:</b>
                                    </td>
                                    <td>
                                        <?php if( count( $pvlans[ $vli->vlanid ][ 'members'] ) === 1 ): ?>
                                            <em>None - single member</em>
                                        <?php else: ?>
                                            <?php foreach( $pvlans[ $vli->vlanid ][ 'members'] as $m ): ?>
                                                <?= $t->ee( $m->abbreviatedName )?> <br />
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
                        <div class="col-sm-12">
                            <h4>
                                <?= $t->ee( $vli->vlan->name ) ?>:
                            </h4>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <table class="table table-sm table-borderless table-striped">
                                <?php if( $vli->ipv6enabled && $v6 = $vli->ipv6address ): ?>
                                    <tr>
                                        <td>
                                            <b>
                                                IPv6 Address:
                                            </b>
                                        </td>
                                        <td>
                                            <span class="tw-font-mono">
                                                <?= $t->ee( $v6->address ) ?><?php if( isset( $t->netInfo[ $vlanid ][ 6 ][ 'masklen' ] ) ) : ?>/<?= $t->netInfo[ $vlanid ][ 6 ][ "masklen" ] ?><?php endif;?>
                                            </span>
                                        </td>
                                        <td>
                                            <b>IPv6 RS/RC MD5:</b>
                                        </td>
                                        <td>
                                            <?php if( $vli->ipv6bgpmd5secret ): ?>
                                                <span class="tw-font-mono">
                                                    <?= $t->ee( $vli->ipv6bgpmd5secret ) ?>
                                                </span>
                                            <?php else: ?>
                                                <em>(not configured)</em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php if( $vli->ipv4enabled && $v4 = $vli->ipv4address ): ?>
                                    <tr>
                                        <td>
                                            <b>
                                                IPv4 Address:
                                            </b>
                                        </td>
                                        <td>
                                            <span class="tw-font-mono">
                                                <?= $t->ee( $v4->address ) ?><?php if( isset( $t->netInfo[ $vlanid ][ 4 ][ 'masklen' ] ) ) : ?>/<?= $t->netInfo[ $vlanid ][ 4 ][ "masklen" ] ?><?php endif;?>
                                            </span>
                                        </td>
                                        <td>
                                            <b>IPv4 RS/RC MD5:</b>
                                        </td>
                                        <td>
                                            <?php if( $vli->ipv4bgpmd5secret ): ?>
                                                <span class="tw-font-mono">
                                                    <?= $t->ee( $vli->ipv4bgpmd5secret ) ?>
                                                </span>
                                            <?php else: ?>
                                                <em>(not configured)</em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td>
                                        <b>
                                            Route Server Client:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $vli->rsclient ? "Yes" : "No" ?>
                                    </td>
                                    <td>
                                        <b>
                                            MAC Address:
                                        </b>
                                    </td>
                                    <td>
                                        <?php foreach( $vli->layer2Addresses as $l2a ): ?>
                                            <span class="tw-font-mono">
                                                <?= $l2a->macFormatted( ':' ) ?>
                                            </span><br />
                                        <?php endforeach; ?>
                                        <?php if( config( 'ixp_fe.layer2-addresses.customer_can_edit' ) ): ?>
                                            <a href="<?= route( "layer2-address@forVlanInterface", [ "vli" => $vli->id ] ) ?>">
                                              Edit
                                            </a>
                                        <?php endif; ?>
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
                                            <?= $vli->as112client ? "Yes" : "No" ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <?php if( $t->vi->typePeering() ): ?>
                <div class="row">
                    <p>
                        No VLAN interfaces defined.
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="col-lg-6 col-md-12">
        <?php if( $isLAG ): ?>
            <?php if( $t->vi->isGraphable() ): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex">
                        <div class="mr-auto">
                            <h5>
                                Aggregate Day Graph for LAG
                            </h5>
                        </div>
                        <div clas="my-auto">
                            <a class="btn btn-white btn-sm " href="<?= route( "statistics@member-drilldown", [ 'type' => 'vi', 'typeid' => $t->vi->id ] ) ?>">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?= $t->grapher->virtint( $t->vi )->renderer()->boxLegacy() ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php foreach( $pis as $pi ): ?>
            <?php if( !$pi->isGraphable() ) { continue; } ?>

            <div class="card mb-4">
                <div class="card-header d-flex">
                    <div class="mr-auto">
                        <h5>
                            Day Graph for <?= $t->ee( $pi->switchPort->switcher->name ) ?> / <?= $t->ee( $pi->switchPort->name ) ?>
                        </h5>
                    </div>

                    <div class="my-auto">
                        <a class="btn btn-white btn-sm" href="<?= route( "statistics@member-drilldown", [ 'type' => 'pi', 'typeid' => $pi->id ] ) ?>">
                            <i class="fa fa-search"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?= $t->grapher->physint( $pi )->renderer()->boxLegacy() ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
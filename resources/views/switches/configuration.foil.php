<?php
    $this->layout( 'layouts/ixpv4' );
    $isSuperUser = Auth::getUser()->isSuperUser();
    $switch     = $t->s; /** @var \IXP\Models\Switcher $switch */
    $infra      = $t->infra; /** @var \IXP\Models\Infrastructure $infra */
    $vlan       = $t->vlan; /** @var \IXP\Models\Vlan $vlan */
    $location   = $t->location; /** @var \IXP\Models\Location $location */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Switch Configuration / Configuration
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $switch ? $switch->infrastructureModel->name : ($infra->name ?? "All Infrastructures") ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= $switch ? "" : ( !$infra ? "active" : "" ) ?>" href="<?= route( "switch@configuration", [ "infra" => 0 ] ) ?>">
                    All Infrastructures
                </a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->infras as $i ): ?>
                    <a class="dropdown-item <?= $switch ? "active" : ( $infra && $infra->id === $i->id ? "active" : "" )?>" href="<?= route( "switch@configuration", [ "infra" => $i->id ] ) ?>">
                        <?= $i->name ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $vlan->name ?? "All VLANs" ?>
            </button>

            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= !$vlan ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "vlan" => 0 ] ) ?>">
                    All VLANs
                </a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->vlans as $vl ): ?>
                    <a class="dropdown-item <?= $vlan && $vlan->id === $vl->id ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "vlan" => $vl->id ] ) ?>">
                        <?= $vl->name ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $switch ? $switch->cabinet->location->name : ($location->name ?? "All Facilities") ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= $switch ? "" : ( $location ?: "active" ) ?>" href="<?= route( "switch@configuration", [ "location" => 0 ] ) ?>">
                    All Facilities
                </a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->locations as $l ): ?>
                    <a class="dropdown-item <?= $switch ? "active" : ( !($location && $location->id === $l->id) ?: "active" ) ?>" href="<?= route( "switch@configuration", [ "location" => $l->id ] ) ?>">
                        <?= $l->name ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $switch->name ?? "All switches" ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= !$switch ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "switch" => 0 ] ) ?>">All Switch</a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->switches as $s ): ?>
                    <a class="dropdown-item <?= $switch && $switch->id === $s->id ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "switch" => $s->id ] ) ?>">
                        <?= $s->name ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->speed ? $t->scaleBits( $t->speed * 1000000, 0 ) : "All speeds" ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= !$t->speed ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "speed" => 0 ] ) ?>">
                  All Speed
                </a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->speeds as $speed ): ?>
                    <a class="dropdown-item <?= $t->speed === $speed ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "speed" => $speed ] ) ?>">
                        <?= $t->scaleBits( $speed * 1000000, 0 ) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <a class="btn btn-white" href="<?= route( "switch@configuration", [ "switch" => 0, "infra" => 0, "location" => 0, "speed" => 0, 'vlan' => 0 ] ) ?>">
            Clear
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?php if( $t->summary ): ?>
                <p>
                    <?= $t->summary ?>
                </p>
            <?php endif; ?>

            <table id="list-configuration" class="table table-striped table-bordered w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                        </th>
                        <th>
                            Switch
                        </th>
                        <th>
                            Port
                        </th>
                        <th>
                            Speed
                        </th>
                        <th>
                            Raw Speed
                        </th>
                        <th>
                            Peering LAN
                        </th>
                        <th>
                            ASN
                        </th>
                        <th>
                            Route Server
                        </th>
                        <th>
                            IPv4</th>
                        <th>
                            IPv6
                        </th>
                        <th>
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $t->config as $conf ): ?>
                        <tr>
                            <td>
                                <?= $conf[ "switchid" ] ?>
                            </td>
                            <td>
                                <?php if( $conf[ 'custid' ] ): ?>
                                    <a href="<?= route( $isSuperUser ? 'customer@overview' : 'customer@detail' , [ 'cust' => $conf[ "custid" ] ] ) ?>">
                                        <?= $conf[ 'customer' ] ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if( $conf[ 'switchid' ] ): ?>
                                    <a href="<?= route( $isSuperUser ? 'switch@port-report' : 'switch@configuration' , [ "switch" => $conf[ "switchid" ] ] ) ?>">
                                        <?= $conf[ 'switchname' ] ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= str_replace( ",", "<br>", $conf[ "ifName" ] ) ?>
                            </td>
                            <td>
                                <?= $t->scaleBits( array_sum( explode( "," , $conf[ "rate_limit" ] ?: $conf[ "speed" ]) )*1000*1000, 0 ) ?>
                            </td>
                            <td>
                                <?= array_sum( explode( ',' , $conf[ "rate_limit" ] ?: $conf[ 'speed' ] ) ) ?>
                            </td>
                            <td>
                                <?= $conf[ "vlan" ] ?>
                            </td>
                            <td>
                                <?=  $t->asNumber( $conf[ "asn" ] ) ?>
                            </td>
                            <td>
                                 <?= $conf[ "rsclient" ] ? 'Yes' : 'No' ?>
                            </td>
                            <td>
                                <?php if( $conf['ipv4enabled'] ): ?>
                                    <?= str_replace( ",", "<br>" , $conf[ "ipv4address" ] ) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if( $conf['ipv6enabled'] ): ?>
                                    <?= str_replace( ",", "<br>" , $conf[ "ipv6address" ] ) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php foreach( explode( "," , $conf[ "portstatus" ] ) as $portstatus ): ?>
                                    <?= \IXP\Models\PhysicalInterface::$STATES[ $portstatus ] ?? '' ?>
                                    <?php if( $conf[ "rate_limit" ] ): ?>
                                        <span class="badge badge-info" data-toggle="tooltip" title="Rate Limited">RL</span>
                                    <?php endif; ?>
                                    <br>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( document ).ready( function() {
            $('#list-configuration').dataTable({
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive : true,
                "columnDefs": [
                    { "targets": [ 0 ], "visible": false, "searchable": false },
                    { "targets": [ 4 ], "orderData": 5 },
                    { "targets": [ 5 ], "visible": false, "searchable": false }
                ],
                "order": [[ 1, "asc" ]],
                "iDisplayLength": 100,
                "aoColumns": [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ],
            }).show();
        });
    </script>
<?php $this->append() ?>
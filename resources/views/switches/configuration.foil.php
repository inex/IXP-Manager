<?= config( 'ixp_fe.customer.one' ) ?>
<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Switch Configuration
    /
    Configuration
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->s ? $t->s->infrastructure->name : ( $t->infra ? $t->infra->name : "All Infrastructures" ) ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= $t->s ? "" : ( !$t->infra ? "active" : "" ) ?>" href="<?= route( "switch@configuration", [ "infra" => 0 ] ) ?>">
                    All Infrastructures
                </a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->infras as $infra ): ?>
                    <a class="dropdown-item <?= $t->s ? "active" : ( $t->infra && $t->infra->id === $id ? "active" : "" )?>" href="<?= route( "switch@configuration", [ "infra" => $infra[ 'id' ] ] ) ?>">
                        <?= $infra[ 'name' ] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>


        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->vlan ? $t->vlan->getName() : "All VLANs" ?>
            </button>


            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= !$t->vlan ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "vlan" => 0 ] ) ?>">
                    All VLANs
                </a>


                <div class="dropdown-divider"></div>

                <?php foreach( $t->vlans as $id => $name ): ?>
                    <a class="dropdown-item <?= $t->vlan && $t->vlan->getId() == $id ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "vlan" => $id ] ) ?>">
                        <?= $name ?>
                    </a>

                <?php endforeach; ?>

            </div>
        </div>


        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->s ? $t->s->cabinet->location->name : ( $t->location ? $t->location->name : "All Facilities" ) ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= $t->s ? "" : ( !$t->location ? "active" : "" ) ?>" href="<?= route( "switch@configuration", [ "location" => 0 ] ) ?>">
                    All Facilities
                </a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->locations as $location ): ?>
                    <a class="dropdown-item <?= $t->s ? "active" : ( $t->location && $t->location->id === $id ? "active" : "" ) ?>" href="<?= route( "switch@configuration", [ "location" => $location[ 'id' ] ] ) ?>">
                        <?= $location[ 'name' ] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->s ? $t->s->name : "All switches" ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= !$t->s ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "switch" => 0 ] ) ?>">All Switch</a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->switches as $s ): ?>
                    <a class="dropdown-item <?= $t->s && $t->s->id === $s->id ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "switch" => $s->id ] ) ?>"><?= $s->name ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->speed ? $t->scaleBits( $t->speed * 1000000, 0 ) : "All speeds" ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= !$t->speed ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "speed" => 0 ] ) ?>">All Speed</a>
                <div class="dropdown-divider"></div>
                <?php foreach( $t->speeds as $speed ): ?>
                    <a class="dropdown-item <?= $t->speed === $speed[ 'speed' ] ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "speed" => $speed[ 'speed' ] ] ) ?>">
                        <?= $t->scaleBits( $speed[ 'speed' ] * 1000000, 0 ) ?>
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


        <table id="list-configuration" class="table table-striped table-bordered" width="100%">

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
                        <?php if( $conf[ "custid" ] ): ?>
                            <?php if( Auth::getUser()->isSuperUser() ): ?>
                                <a href="<?= route( "customer@overview" , [ "id" => $conf[ "custid" ] ] ) ?>"><?= $conf[ "customer" ] ?></a>
                            <?php else: ?>
                                <a href="<?= route( "customer@detail"   , [ "id" => $conf[ "custid" ] ] ) ?>"><?= $conf[ "customer" ] ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( $conf[ "switchid" ] ): ?>
                            <?php if( Auth::getUser()->isSuperUser() ): ?>
                                <a href="<?= route( "switch@port-report"    , [ "switch" => $conf[ "switchid" ] ] ) ?>"><?= $conf[ "switchname" ] ?></a>
                            <?php else: ?>
                                <a href="<?= route( "switch@configuration"  , [ "switch" => $conf[ "switchid" ] ] ) ?>"><?= $conf[ "switchname" ] ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= str_replace( ",", "<br>",$conf[ "ifName" ] ) ?>
                    </td>
                    <td>
                        <?php $totalSpeed = explode( "," , $conf[ "speed" ]) ?>

                        <?= $t->scaleBits( array_sum( $totalSpeed )*1000*1000, 0 ) ?>
                    </td>
                    <td>
                        <?php $totalSpeed = explode( "," , $conf[ "speed" ]) ?>

                        <?= array_sum( $totalSpeed ) ?>
                    </td>
                    <td>
                        <?= $conf[ "vlan" ] ?>
                    </td>
                    <td>
                        <?=  $t->asNumber( $conf[ "asn" ] ) ?>
                    </td>
                    <td>
                         <?php if( $conf[ "rsclient" ] ): ?>Yes<?php else: ?>No<?php endif; ?>
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
                        <?php foreach( explode( "," , $conf[ "portstatus" ] ) as $portstatus ) {
                                if( isset( Entities\PhysicalInterface::$STATES[ $portstatus ] ) ) {
                                    echo Entities\PhysicalInterface::$STATES[ $portstatus ] . '<br>';
                                }
                            } ?>
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

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

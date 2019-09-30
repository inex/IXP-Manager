<?= config( 'ixp_fe.customer.one' ) ?><?php $this->layout( 'layouts/ixpv4' ) ?>


<?php $this->section( 'page-header-preamble' ) ?>
    Switch Configuration
    /
    Configuration
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>


    <div class="btn-group btn-group-sm" role="group">

        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->s ? $t->s->getInfrastructure()->getName() : ( $t->infra ? $t->infra->getName() : "All Infrastructures" ) ?>
            </button>


            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= $t->s ? "" : ( !$t->infra ? "active" : "" ) ?>" href="<?= route( "switch@configuration", [ "infra" => 0 ] ) ?>">
                    All Infrastructures
                </a>


                <div class="dropdown-divider"></div>

                <?php foreach( $t->infras as $id => $name ): ?>
                    <a class="dropdown-item <?= $t->s ? "active" : ( $t->infra && $t->infra->getId() == $id ? "active" : "" )?>" href="<?= route( "switch@configuration", [ "infra" => $id ] ) ?>">
                        <?= $name ?>
                    </a>

                <?php endforeach; ?>

            </div>
        </div>


        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->s ? $t->s->getCabinet()->getLocation()->getName() : ( $t->location ? $t->location->getName() : "All Facilities" ) ?>
            </button>

            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                <a class="dropdown-item <?= $t->s ? "" : ( !$t->location ? "active" : "" ) ?>" href="<?= route( "switch@configuration", [ "location" => 0 ] ) ?>">
                    All Facilities
                </a>


                <div class="dropdown-divider"></div>

                <?php foreach( $t->locations as $id => $name ): ?>

                    <a class="dropdown-item <?= $t->s ? "active" : ( $t->location && $t->location->getId() == $id ? "active" : "" ) ?>" href="<?= route( "switch@configuration", [ "location" => $id ] ) ?>">
                        <?= $name ?>
                    </a>

                <?php endforeach; ?>

            </div>
        </div>


        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->s ? $t->s->getName() : "All switches" ?>
            </button>


            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                <a class="dropdown-item <?= !$t->s ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "switch" => 0 ] ) ?>">All Switch</a>


                <div class="dropdown-divider"></div>

                <?php foreach( $t->switches as $s ): ?>

                    <a class="dropdown-item <?= $t->s && $t->s->getId() == $s->getId() ? "active" : "" ?>" href="<?= route( "switch@configuration", [ "switch" => $s->getId() ] ) ?>"><?= $s->getName() ?></a>

                <?php endforeach; ?>

            </div>
        </div>


        <a class="btn btn-white" href="<?= route( "switch@configuration", [ "switch" => 0, "infra" => 0, "location" => 0 ] ) ?>">
            Clear
        </a>

    </div>

<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

<div class="row">

    <div class="col-sm-12">

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
                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                            <a href="<?= route( "customer@overview" , [ "id" => $conf[ "custid" ] ] ) ?>"><?= $conf[ "customer" ] ?></a>
                        <?php else: ?>
                            <a href="<?= route( "customer@detail"   , [ "id" => $conf[ "custid" ] ] ) ?>"><?= $conf[ "customer" ] ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                            <a href="<?= route( "switch@port-report"    , [ "id" => $conf[ "switchid" ] ] ) ?>"><?= $conf[ "switchname" ] ?></a>
                        <?php else: ?>
                            <a href="<?= route( "switch@configuration"  , [ "id" => $conf[ "switchid" ] ] ) ?>"><?= $conf[ "switchname" ] ?></a>
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
                        <?php if( isset( Entities\PhysicalInterface::$STATES[ $conf[ "portstatus" ] ] ) ): ?>
                            <?= Entities\PhysicalInterface::$STATES[ $conf[ "portstatus" ] ] ?>
                        <?php endif; ?>
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

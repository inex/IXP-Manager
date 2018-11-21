<?php $this->layout( 'layouts/ixpv4' ) ?>


    <?php $this->section( 'title' ) ?>

        <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>

            <a href="<?= route( 'switch@list' )?>">Switches</a>

        <?php else: ?>

            Switch Configuration

        <?php endif; ?>
    <?php $this->append() ?>

<?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <li class="active">Configuration <?= $t->summary ?></li>
    <?php $this->append() ?>
<?php endif; ?>

    <?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">

        <div class="btn-group btn-group-xs" role="group">

            <!-- Single button -->
            <div class="btn-group">

                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->s ? $t->s->getInfrastructure()->getName() : ( $t->infra ? $t->infra->getName() : "All Infrastructures" ) ?> <span class="caret"></span>
                </button>


                <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                    <li class="<?= $t->s ? "" : ( !$t->infra ? "active" : "" ) ?>">
                        <a href="<?= route( "switch@configuration", [ "infra" => 0 ] ) ?>">All Infrastructures</a>
                    </li>

                    <li role="separator" class="divider"></li>
                    
                    <?php foreach( $t->infras as $id => $name ): ?>

                        <li class="<?= $t->s ? "active" : ( $t->infra && $t->infra->getId() == $id ? "active" : "" )?>">
                            <a href="<?= route( "switch@configuration", [ "infra" => $id ] ) ?>"><?= $name ?></a>
                        </li>


                    <?php endforeach; ?>

                </ul>
            </div>


            <!-- Single button -->
            <div class="btn-group">

                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->s ? $t->s->getCabinet()->getLocation()->getName() : ( $t->location ? $t->location->getName() : "All Facilities" ) ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                    <li class="<?= $t->s ? "" : ( !$t->location ? "active" : "" ) ?>">
                        <a href="<?= route( "switch@configuration", [ "location" => 0 ] ) ?>">All Facilities</a>
                    </li>

                    <li role="separator" class="divider"></li>

                    <?php foreach( $t->locations as $id => $name ): ?>

                        <li class="<?= $t->s ? "active" : ( $t->location && $t->location->getId() == $id ? "active" : "" ) ?>">
                            <a href="<?= route( "switch@configuration", [ "location" => $id ] ) ?>"><?= $name ?></a>
                        </li>


                    <?php endforeach; ?>

                </ul>
            </div>

            <!-- Single button -->
            <div class="btn-group">

                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->s ? $t->s->getName() : "All switches" ?> <span class="caret"></span>
                </button>


                <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                    <li class="<?= !$t->s ? "active" : "" ?>">
                        <a href="<?= route( "switch@configuration", [ "switch" => 0 ] ) ?>">All Switch</a>
                    </li>

                    <li role="separator" class="divider"></li>


                    <?php foreach( $t->switches as $s ): ?>

                        <li class="<?= $t->s && $t->s->getId() == $s->getId() ? "active" : "" ?>">
                            <a href="<?= route( "switch@configuration", [ "switch" => $s->getId() ] ) ?>"><?= $s->getName() ?></a>
                        </li>


                    <?php endforeach; ?>

                </ul>
            </div>

            <a class="btn btn-default btn-xs" href="<?= route( "switch@configuration", [ "switch" => 0, "infra" => 0, "location" => 0 ] ) ?>">Clear</a>

        </div>

    </li>
    <?php $this->append() ?>

<?php $this->section( 'content' ) ?>

<div class="row">

    <div class="col-sm-12">

        <table id="list-configuration" class="table table-striped table-bordered">

            <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Switch</th>
                <th>Port</th>
                <th>Speed</th>
                <th>Raw Speed</th>
                <th>Peering LAN</th>
                <th>ASN</th>
                <th>Route Server</th>
                <th>IPv4</th>
                <th>IPv6</th>
                <th>Status</th>
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
                        <?= str_replace( ",", "<br>" , $conf[ "ipv4address" ] ) ?>
                    </td>
                    <td>
                        <?= str_replace( ",", "<br>" , $conf[ "ipv6address" ] ) ?>
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

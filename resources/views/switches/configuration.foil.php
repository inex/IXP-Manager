<?php $this->layout( 'layouts/ixpv4' ) ?>


    <?php $this->section( 'title' ) ?>

        <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>

            <a href="<?= route( 'switchs@list' )?>">Switches</a>

        <?php else: ?>

            Switch Configuration

        <?php endif; ?>
    <?php $this->append() ?>

<?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <li>Configuration</li>
    <?php $this->append() ?>
<?php endif; ?>

    <?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">

        <div class="btn-group btn-group-xs" role="group">

            <!-- Single button -->
            <div class="btn-group">

                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->s ? $t->s->getName() : "All switches" ?> <span class="caret"></span>
                </button>


                <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                    <li class="<?= !$t->s ? "active" : "" ?>">
                        <a href="<?= route( "switchs@configuration", [ "switch" => 0 ] ) ?>">All Switch</a>
                    </li>

                    <li role="separator" class="divider"></li>


                    <?php foreach( $t->switches as $id => $name ): ?>

                        <li class="<?= $t->s && $t->s->getId() == $id ? "active" : "" ?>">
                            <a href="<?= route( "switchs@configuration", [ "switch" => $id ] ) ?>"><?= $name ?></a>
                        </li>


                    <?php endforeach; ?>

                </ul>
            </div>

            <!-- Single button -->
            <div class="btn-group">

                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->vl ? $t->vl->getName() : "All Peering Vlan" ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                    <li class="<?= !$t->vl ? "active" : "" ?>">
                        <a href="<?= route( "switchs@configuration", [ "vlan" => 0 ] ) ?>">All Peering Vlan</a>
                    </li>

                    <li role="separator" class="divider"></li>

                    <?php foreach( $t->vlans as $id => $name ): ?>

                        <li class="<?= $t->vl && $t->vl->getId() == $id ? "active" : "" ?>">
                            <a href="<?= route( "switchs@configuration", [ "vlan" => $id ] ) ?>"><?= $name ?></a>
                        </li>


                    <?php endforeach; ?>

                </ul>
            </div>

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
                            <a href="<?= route( "customer@detail" , [ "id" => $conf[ "custid" ] ] ) ?>"><?= $conf[ "customer" ] ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                            <a href="<?= route( "switchs@port-report" , [ "id" => $conf[ "switchid" ] ] ) ?>"><?= $conf[ "switchname" ] ?></a>
                        <?php else: ?>
                            <a href="<?= route( "switchs@configuration" , [ "id" => $conf[ "switchid" ] ] ) ?>"><?= $conf[ "switchname" ] ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $conf[ "ifName" ] ?>
                    </td>
                    <td>
                        <?= $conf[ "speed" ] ?>
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
                        <?= $conf[ "ipv4address" ] ?>
                    </td>
                    <td>
                        <?= $conf[ "ipv6address" ] ?>
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
                { "targets": [ 0 ], "visible": false }
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
                null
            ],
        }).show();

    });
</script>

<?php $this->append() ?>

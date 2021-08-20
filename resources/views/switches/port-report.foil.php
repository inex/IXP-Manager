<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Switches
    /
    Port Report for <?= $t->s->name ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <!-- Single button -->
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?=  $t->s->name ?> <span class="caret"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <?php foreach( $t->switches as $switch ): ?>
                    <a class="dropdown-item <?= $t->s->id === $switch[ 'id' ] ? 'active' : '' ?>" href="<?= route( "switch@port-report", [ "switch" => $switch[ 'id' ] ] ) ?>"><?= $switch[ 'name' ] ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <a class="btn btn-white" href="<?= route ('switch@list' ) ?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <table id="list-port" class="collapse table table-striped table-bordered" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Port Name
                        </th>
                        <th>
                            Type
                        </th>
                        <th>
                            Speed/Duplex
                        </th>
                        <th>
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $t->ports as $port ): ?>
                        <tr>
                            <td>
                                <?= $port[ "id" ] ?>
                            </td>
                            <td>
                                <?= $port[ "name" ] ?>
                            </td>
                            <td>
                                <?= \IXP\Models\SwitchPort::$TYPES[ $port[ "porttype" ] ]?>
                            </td>
                            <?php if( isset( $port[ "speed" ] ) ): ?>
                                <td>
                                    <?= $port[ "speed" ] ?>/<?= $port[ "duplex" ] ?>
                                </td>
                                <td>
                                    <?= $port[ "custname" ] ?>
                                </td>
                            <?php else: ?>
                                <td></td>
                                <td></td>
                            <?php endif; ?>
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

            $('#list-port').show();

            $('#list-port').dataTable({
                responsive : true,
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                "columnDefs": [
                    { "targets": [ 0 ], "visible": false },
                    { "orderData": [ 0 ],    "targets": 1 },
                ],
                "order": [[ 0, "asc" ]],

                "iDisplayLength": 100,
                "aoColumns": [
                    { "sWidth": "50px" },
                    { "sWidth": "150px" },
                    { "sWidth": "100px" },
                    { "sWidth": "100px" },
                    { "sWidth": "100px" }
                ],
            }).show();

        });
    </script>
<?php $this->append() ?>

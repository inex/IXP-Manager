<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'switch@list' )?>">Switches</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Port Report for <?= $t->s->getName() ?></li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">

        <div class="btn-group btn-group-xs" role="group">

            <!-- Single button -->
            <div class="btn-group">

                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?=  $t->s->getName() ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                    <?php foreach( $t->switches as $id => $name ): ?>

                        <li class="<?= $t->s->getId() == $id ? 'active' : '' ?>">
                            <a href="<?= route( "switch@port-report", [ "port" => $id ] ) ?>"><?= $name ?></a>
                        </li>

                    <?php endforeach; ?>

                </ul>
            </div>

            <a type="button" class="btn btn-default" href="<?= route ('switch@list' ) ?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>

        </div>

    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-sm-12">

            <table id="list-port" class="table table-striped table-bordered">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Port Name</th>
                        <th>Type</th>
                        <th>Speed/Duplex</th>
                        <th>Customer</th>
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
                                <?= $port[ "porttype" ] ?>
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

            $('#list-port').dataTable({
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

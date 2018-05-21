<?php $this->layout( 'layouts/ixpv4' ) ?>


<?php $this->section( 'title' ) ?>

    <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>

        <a href="<?= route( 'switch@list' )?>">Switches</a>

    <?php else: ?>

        Unused Optics

    <?php endif; ?>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

<div class="row">

    <div class="col-sm-12">

        <div class="alert alert-info">
            A list of ports from <b>switches that support the IANA MAU MIB</b> where the operational status
            is down, the port is populated with an optic / SFP and the port type is not management.
            Data valid at time of last SNMP poll.
        </div>


        <?php if( count( $t->optics ) ): ?>
            <table id="list-optics" class="collapse table table-striped table-bordered">

                <thead>
                <tr>
                    <th>ifIndex</th>
                    <th>Switch</th>
                    <th>Port</th>
                    <th>Type</th>
                    <th>MAU Type</th>
                    <th>MAU State</th>
                    <th>Jack Type</th>
                </tr>
                </thead>

                <tbody>
                    <?php foreach( $t->optics as $sp ): ?>

                        <tr>
                            <td>
                                <?= $sp->getSwitcher()->getId() ?>
                            </td>
                            <td>
                                <?= $sp->getSwitcher()->getName() ?>
                            </td>
                            <td>
                                <?= $sp->getIfName() ?>
                            </td>
                            <td>
                                <?= Entities\SwitchPort::$TYPES[ $sp->getType() ] ?>
                            </td>
                            <td>
                                <?= $sp->getMauType() ?>
                            </td>
                            <td>
                                <?= $sp->getMauState() ?>
                            </td>
                            <td>
                                <?= $sp->getMauJacktype() ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>

            </table>
        <?php else: ?>

            <div class="alert alert-info" role="alert">
                <b>No Unused Optics exist.</b> <a href="<?= route('switch-ports@add') ?>">Add one...</a>
            </div>

        <?php endif; ?>

    </div>

</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<script>
    $( document ).ready( function() {

        let tableList = $( '#list-optics' );

        tableList.dataTable({
            "columnDefs": [
                { "targets": [ 0 ], "visible": false }
            ],
            "bAutoWidth": false,
            "order": [[ 1, "asc" ]],

            "iDisplayLength": 50,
            "aoColumns": [
                null,
                null,
                null,
                null,
                null,
                null,
                null
            ],
        });

        tableList.show();

    });
</script>

<?php $this->append() ?>

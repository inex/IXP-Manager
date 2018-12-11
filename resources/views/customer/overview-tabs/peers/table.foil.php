<div class="col-sm-12">
    <br>
    <table id="peers-table" class="table peers-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>ASN</th>

            <?php foreach( $t->peers[ "vlan" ] as $vlan ): ?>

                <?php $vlanid = $vlan->getNumber() ?>

                <?php if( isset( $t->peers[ "me" ][ 'vlaninterfaces' ][ $vlanid ] ) ): ?>
                    <th>
                        <?= $vlan->getName() ?>
                    </th>
                <?php endif; ?>

            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
            <?php foreach( $t->listOfCusts as  $as => $p ): ?>

                <?php $c = $t->peers[ "custs" ][ $as ] ?>
                <?php $cid = $c[ "id" ] ?>

                <?php if( $p ): ?>
                    <tr>
                        <td id="peer-name-<?= $cid ?>">
                            <?= $c[ "name" ] ?>
                        </td>
                        <td><?= $c[ "autsys" ] ?></td>

                        <?php foreach( $t->peers[ "vlan" ] as $avlan ): ?>
                            <?php $vlan = $avlan->getNumber() ?>
                            <?php if( isset( $c[ $vlan ] ) ): ?>
                                <td>
                                    <?php foreach( $t->peers[ "protos" ] as $proto ): ?>
                                        <?php if( isset( $c[ $vlan ][ $proto ] ) ): ?>
                                            <span class="label <?= ( $c[ $vlan ][ $proto ] )? "label-success" : "label-danger" ?>" >IPv<?= $proto ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                            <?php elseif( isset( $t->peers[ "me" ][ "vlaninterfaces" ][ $vlan ] ) ): ?>
                                <td></td>
                            <?php endif; ?>

                        <?php endforeach; ?>

                    </tr>
                <?php endif; ?>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>


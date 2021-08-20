<div class="col-sm-12 mt-4">
    <table id="peers-table" class="table peers-table table-striped w-100">
        <thead class="thead-dark">
            <tr>
                <th>
                    Name
                </th>
                <th>
                    ASN
                </th>
                <?php foreach( $t->peers[ 'vlan' ] as $vlan ): ?>
                    <?php $vlanid = $vlan->number ?>
                    <?php if( isset( $t->peers[ "me" ][ 'vlan_interfaces' ][ $vlanid ] ) ): ?>
                        <th>
                            <?= $vlan->name ?>
                        </th>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $t->listOfCusts as  $as => $p ):
                $c = $t->peers[ "custs" ][ $as ];
                $cid = $c[ "id" ];
                ?>

                <?php if( $p ): ?>
                    <tr>
                        <td id="peer-name-<?= $cid ?>">
                            <?= $c[ "name" ] ?>
                        </td>
                        <td>
                            <?= $c[ "autsys" ] ?>
                        </td>

                        <?php foreach( $t->peers[ "vlan" ] as $avlan ): ?>
                            <?php $vlan = $avlan->number ?>
                            <?php if( isset( $c[ $vlan ] ) ): ?>
                                <td>
                                    <?php foreach( $t->peers[ "protos" ] as $proto ): ?>
                                        <?php if( isset( $c[ $vlan ][ $proto ] ) ): ?>
                                            <span class="badge <?= ( $c[ $vlan ][ $proto ] )? "badge-success" : "badge-danger" ?>" >
                                              IPv<?= $proto ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                            <?php elseif( isset( $t->peers[ "me" ][ "vlan_interfaces" ][ $vlan ] ) ): ?>
                                <td></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
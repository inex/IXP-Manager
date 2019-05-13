
<table class="table table-striped table-responsive-ixp collapse" style="width:100%">
    <thead class="thead-dark">
        <tr>
            <th>
                VLAN
            </th>
            <th>
                Tag
            </th>
            <th>
                Location
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
                Other Members
            </th>
        </tr>
    </thead>
    <tbody>
        <?php if( !isset( $pvlans ) ): ?>
            <?php $pvlans = $t->c->getPrivateVlanDetails() ?>
        <?php endif; ?>

        <?php foreach( $pvlans as $vlanid => $pv ): ?>
            <?php foreach($pv[ "vlis" ] as $vli ): ?>
                <tr>
                    <td>
                        <?= $t->ee( $vli->getVlan()->getName() )?>
                    </td>
                    <td>
                        <?= $t->ee( $vli->getVlan()->getNumber() )?>
                    </td>
                    <td>
                        <?php $pis = $vli->getVirtualInterface()->getPhysicalInterfaces() ?>
                        <?php if( count( $pis ) > 0 ): ?>
                            <?= $t->ee( $pis[ 0 ]->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                        <?php endif; ?>

                    </td>
                    <td>
                        <?php if( count( $pis ) > 0 ): ?>
                            <?= $t->ee( $pis[ 0 ]->getSwitchPort()->getSwitcher()->getName() ) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php foreach( $vli->getVirtualInterface()->getPhysicalInterfaces() as $p ): ?>
                            <?= $t->ee( $p->getSwitchPort()->getName() ) ?><br />

                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach( $vli->getVirtualInterface()->getPhysicalInterfaces() as $p ): ?>

                            <?= $t->ee( $p->getSpeed() ) ?>/<?= $t->ee( $p->getDuplex() ) ?><br />
                        <?php endforeach; ?>
                    </td>
                    <td>

                        <?php $others =  0 ?>
                        <?php foreach( $pv[ "members" ] as $m ): ?>
                            <?php if( $m->getId() != $t->c->getId() ): ?>
                                <a href="<?= route( "customer@overview" , [ "id" => $m->getId() ]) ?>">
                                    <?= $t->ee( $m->getAbbreviatedName() ) ?>
                                </a><br />
                                <?php $others =  1 ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if( !$others): ?>
                            <em>None - single member</em>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>


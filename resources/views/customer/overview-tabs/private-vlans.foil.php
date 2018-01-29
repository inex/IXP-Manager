
<table class="table">
    <thead>
        <tr>
            <th>VLAN</th>
            <th>Tag</th>
            <th>Location</th>
            <th>Switch</th>
            <th>Port</th>
            <th>Speed</th>
            <th>Other Members</th>
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
                        <?= $vli->getVlan()->getNumber() ?>
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
                            <?= $p->getSwitchPort()->getName() ?><br />

                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach( $vli->getVirtualInterface()->getPhysicalInterfaces() as $p ): ?>

                            <?= $p->getSpeed() ?>/<?= $p->getDuplex() ?><br />
                        <?php endforeach; ?>
                    </td>

                    <td>
                        <div class="well" style="overflow-y: scroll; height:400px;">
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
                        </div>

                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>


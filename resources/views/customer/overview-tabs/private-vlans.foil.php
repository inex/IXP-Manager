<?php
    $c = $t->c; /** @var \IXP\Models\Customer $c */
    $pvlans = $c->privateVlanDetails()
?>

<table class="table table-striped table-responsive-ixp collapse w-100">
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
        <?php foreach( $pvlans as $vlanid => $pv ): ?>
            <?php foreach( $pv[ "vlis" ] as $vli ):

                /** @var $vli \IXP\Models\VlanInterface */
                $pis = $vli->virtualInterface->physicalInterfaces;
                $switcher = $pis->count() ? $pis[ 0 ]->switchPort->switcher : null;
            ?>
                <tr>
                    <td>
                        <?= $t->ee( $vli->vlan->name )?>
                    </td>
                    <td>
                        <?= $t->ee( $vli->vlan->number )?>
                    </td>

                    <?php if( $pis->count() ): ?>
                        <td>
                            <?= $t->ee( $switcher->cabinet->location->name ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $switcher->name ) ?>
                        </td>
                    <?php else: ?>
                        <td></td>
                        <td></td>
                    <?php endif; ?>

                    <td>
                        <?php foreach( $pis as $p ): ?>
                            <?= $t->ee( $p->switchPort->name ) ?><br />
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach( $pis as $p ): ?>
                            <?= $t->ee( $p->speed ) ?>/<?= $t->ee( $p->duplex ) ?><br />
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php $others =  0 ?>
                        <?php foreach( $pv[ "members" ] as $m ): ?>
                            <?php if( $m->id !== $c->id ): ?>
                                <a href="<?= route( "customer@overview" , [ 'cust' => $m->id ]) ?>">
                                    <?= $t->ee( $m->abbreviatedName ) ?>
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
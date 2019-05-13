<table class="table table-striped" width="100%">
    <thead class="thead-dark">
        <tr>
            <th>
                Prefix
            </th>
            <th>
                Customer
            </th>
            <th>
                IRRDB Entry
            </th>
            <th>
                Origin ASN
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $t->results as $prefix ): ?>
            <tr>
                <td>
                    <a href="<?= route( "rs-prefixes@view", [ "id" => $prefix->getCustomer()->getId() ] ) ?>">
                        <?= $t->ee(  $prefix->getPrefix() ) ?>
                    </a>
                </td>
                <td>
                    <a href="<?= route( "customer@overview" , [ "id" => $prefix->getCustomer()->getId() ] ) ?>">
                        <?= $t->ee( $prefix->getCustomer()->getName() ) ?>
                    </a>
                </td>
                <td>
                    <?php if( $prefix->getIrrdb() ): ?> Yes<?php else: ?>No<?php endif; ?>
                </td>
                <td>
                    <?= $prefix->getRsOrigin() ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

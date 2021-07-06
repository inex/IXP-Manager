<table class="table table-striped w-100">
    <thead class="thead-dark">
        <tr>
            <th>
                Prefix
            </th>
            <th>
                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
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
        <?php foreach( $t->results as $prefix ):
            /** @var $prefix \IXP\Models\RsPrefix */?>
            <tr>
                <td>
                    <a href="<?= route( "rs-prefixes@view", [ "cust" => $prefix->custid ] ) ?>">
                        <?= $t->ee(  $prefix->prefix ) ?>
                    </a>
                </td>
                <td>
                    <a href="<?= route( "customer@overview" , [ 'cust' => $prefix->custid ] ) ?>">
                        <?= $t->ee( $prefix->customer->name ) ?>
                    </a>
                </td>
                <td>
                    <?php if( $prefix->irrdb ): ?> Yes<?php else: ?>No<?php endif; ?>
                </td>
                <td>
                    <?= $prefix->rs_origin ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
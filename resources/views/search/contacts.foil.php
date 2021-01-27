<div class="col-sm-12">
    <h3>
        Contacts
    </h3>
    <table class="table table-striped w-100">
        <thead class="thead-dark">
            <tr>
                <th>
                    Name
                </th>
                <th>
                    E-Mail
                </th>
                <th>
                    <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                </th>
                <th>
                    Created
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $t->results[ 'contacts' ] as $contact ):
                /** @var $contact \IXP\Models\Contact */?>
                <tr>
                    <td>
                        <a href="<?= route( "customer@overview" , [ 'cust' => $contact->custid, 'tab' => 'contacts' ] ) ?>">
                            <?= $t->ee(  $contact->name ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $t->ee( $contact->email ) ?>
                    </td>
                    <td>
                        <a href="<?= route( "customer@overview" , [ 'cust' => $contact->custid ] ) ?>">
                            <?= $t->ee( $contact->customer->name ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $contact->created_at ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
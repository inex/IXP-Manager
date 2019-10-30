<div class="col-sm-12">
    <h3>
        Contacts
    </h3>
    <table class="table table-striped" width="100%">
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
            <?php foreach( $t->results[ 'contacts' ] as $contact ): ?>
                <tr>
                    <td>
                        <a href="<?= url( "contact/edit/id/" . $contact->getId() . "/cid/".$contact->getCustomer()->getId()) ?>">
                            <?= $t->ee(  $contact->getName() ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $t->ee( $contact->getEmail() ) ?>
                    </td>
                    <td>
                        <a href="<?= route( "customer@overview" , [ "id" => $contact->getCustomer()->getId() ] ) ?>">
                            <?= $t->ee( $contact->getCustomer()->getName() ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $contact->getCreated()->format( "Y-m-d H:i:s") ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


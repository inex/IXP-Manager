<div class="col-sm-12">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>E-Mail</th>
                <th>Username (history)</th>
                <th>Customer</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $t->results as $contact ): ?>
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
                        <a href="<?= url( "login-history/list/uid/" . $contact->getUser()->getId() . "/limit/1") ?>">
                            <?= $t->ee( $contact->getUser()->getUsername() ) ?>
                        </a>
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


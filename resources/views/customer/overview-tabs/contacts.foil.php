<div class="col-sm-12">
    <br>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role(s)</th>
            <th>
                Actions
                <a class="btn btn-default btn-xs" href="<?= route( "contact@add" ) . "?cust=" . $t->c->getId() ?>">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php if( count( $t->c->getContacts() ) ): ?>
            <?php foreach( $t->c->getContacts() as $co ): ?>
                <tr>
                    <td>
                        <?= $t->ee( $co->getName() ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $co->getEmail() ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $co->getPhone() ) ?>
                        <?php if( $co->getPhone() && $co->getMobile() ): ?>
                            /
                        <?php endif; ?>
                        <?=  $t->ee( $co->getMobile() ) ?>
                    </td>
                    <td>
                        <?php foreach( $co->getGroups() as $group ): ?>
                            <?php if( $group->getType() == 'ROLE' ): ?>
                                <span class="label label-info"><?= $t->ee( $group->getName() ) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-default" href="<?= route( "contact@edit", [ "id" => $co->getId() ] ) ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                            <a class="btn btn-default" id="cont-list-delete-<?= $co->getId() ?>" data-object-id="<?= $co->getId() ?>" href="#">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td>
                    No contacts found.
                    <a href="<?= route( "contact@add" ) . "?cust=" . $t->c->getId() ?>">Add a new contact...</a>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
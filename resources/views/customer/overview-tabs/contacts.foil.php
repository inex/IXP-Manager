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
                    <a class="btn btn-default btn-xs" href="<?= url( "contact/add/custid/". $t->c->getId() ."/cid/". $t->c->getId() ) ?>">
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
                                <a class="btn btn-default" href="<?= url( "contact/edit/id/" . $co->getId() . "/cid/" . $t->c->getId() ) ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                                <a class="btn btn-default" id="cont-list-delete-<?= $co->getId() ?>" data-hasuser="<?php if( $co->getUser() ): ?>1<?php else: ?>0<?php endif; ?>"
                                   href="<?= url( "contact/delete/id/". $co->getId() ) ?>">
                                <i class="glyphicon glyphicon-trash"></i>
                                </a>
                                <?php if( $co->getUser() ): ?>
                                    <a class="btn btn-default"
                                       <?php if( $co->getUser()->getDisabled()  ): ?> disabled="disabled" onclick="return( false );"<?php endif; ?>
                                        href="<?= url( "auth/switch-user/id/" . $co->getUser()->getId() )  ?>" rel="tooltip" title="Log in as this user...">
                                        <i class="glyphicon glyphicon-user"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td>
                        No contacts found.
                        <a href="<?= url( "contact/add/custid/". $t->c->getId()  ) ?>">Add a new contact...</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-sm-12">
        <br/>

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>Member</th>
                    <th>ASN</th>
                    <th>Policy</th>

                    <?php foreach( $t->vlans as $vlan ): ?>

                        <?php $vlanid = $vlan->getNumber() ?>

                        <?php if( isset( $t->me[ 'vlaninterfaces' ][ $vlanid ] ) ): ?>
                            <th>
                                <?= $vlan->getName() ?>
                            </th>
                        <?php endif; ?>

                    <?php endforeach; ?>

                    <th></th>
                </tr>
            </thead>

            <tbody>

                <?php foreach( $t->listOfCusts as  $as => $p ): ?>

                    <?php $c = $t->custs[ $as ] ?>
                    <?php $cid = $c[ "id" ] ?>

                    <?php if( $p ): ?>
                        <tr>
                            <td id="peer-name-<?= $cid ?>">
                                <?= $c[ "name" ] ?>
                            </td>
                            <td><?= $c[ "autsys" ] ?></td>
                            <td><?= $c[ "peeringpolicy" ] ?></td>

                            <?php foreach( $t->vlans as $avlan ): ?>
                                <?php $vlan = $avlan->getNumber() ?>
                                <?php if( isset( $c[ $vlan ] ) ): ?>
                                <td>
                                    <?php foreach( $t->protos as $proto ): ?>
                                        <?php if( isset( $c[ $vlan ][ $proto ] ) ): ?>
                                            <span class="label <?= ( $c[ $vlan ][ $proto ] )? "label-success" : "label-danger" ?>" >IPv<?= $proto ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                                <?php elseif( isset( $t->me[ "vlaninterfaces" ][ $vlan ] ) ): ?>
                                    <td></td>
                                <?php endif; ?>

                            <?php endforeach; ?>

                            <td>
                                <div class="btn-group">

                                    <button id="peering-request-<?= $cid ?>"
                                            data-days="<?= isset( $t->peers[ $cid ] ) ? $t->peers[ $cid ][ "email_days" ] : -1 ?>"
                                            class="btn btn-default btn-sm <?= !$c[ "ispotential" ] ? "disabled" : "" ?> ">
                                        <i id="peering-request-icon-<?= $cid ?>"
                                           class="glyphicon  <?= isset( $t->peers[ $cid ][ "emails_sent"] ) && $t->peers[ $cid ][ "emails_sent" ] ? "glyphicon-repeat" : "glyphicon-envelope" ?>"></i> Request Peering
                                    </button>

                                    <button id="peering-notes-<?= $cid ?>" class="btn btn-default btn-sm">
                                        <i id="peering-notes-icon-<?= $cid ?>" class="glyphicon <?= isset( $t->peers[ $cid ][ "notes" ] ) && strlen( $t->peers[ $cid ][ "notes" ] ) ? "glyphicon-star" : "glyphicon-star-empty" ?>"></i> Notes
                                    </button>

                                    <button id="dropdown-mark-peering-<?= $cid ?>" class="btn btn-default btn-sm <?= isset( $t->peers[ $cid ] ) && ( $t->peers[ $cid ][ "peered"] || $t->peers[ $cid ][ "rejected" ] ) ? "btn-info" : "" ?> dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>

                                    <ul class="dropdown-menu" >
                                        <li>
                                            <a id="mark-peered-<?= $cid ?>" href="<?= route( "peering-manager@mark-peering", [ "id" => $cid, "status" => "peered" ] ) ?>">
                                                <?php if( isset( $t->peers[ $cid ] ) && $t->peers[ $cid ][ "peered" ] ): ?>
                                                    Unmark as Peered
                                                <?php else: ?>
                                                    Move to Peered
                                                <?php endif; ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a id="mark-rejected-<?= $cid ?>" href="<?= route( "peering-manager@mark-peering", [ "id" => $cid, "status" => "rejected" ] ) ?>">
                                                <?php if( isset( $t->peers[ $cid ] ) && $t->peers[ $cid ][ "rejected" ] ): ?>
                                                    Unmark as Rejected / Ignored
                                                <?php else: ?>
                                                    Move to Rejected / Ignored
                                                <?php endif; ?>
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </td>

                        </tr>
                    <?php endif; ?>

                <?php endforeach; ?>

            </tbody>
        </table>
        <input id="custid" type="hidden" value="<?= $t->c->getId() ?>">
    </div>
</div>
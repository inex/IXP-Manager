<div class="row">
    <div class="col-sm-12">
        <table class="table table-bordered table-striped collapse" style="width: 100%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Member
                    </th>
                    <th>
                        ASN
                    </th>
                    <th>
                        Policy
                    </th>
                    <?php foreach( $t->vlans as $vlan ): ?>
                        <?php $vlanid = $vlan->number ?>
                        <?php if( isset( $t->me[ 'vlan_interfaces' ][ $vlanid ] ) ): ?>
                            <th>
                                <?= $vlan->name ?>
                            </th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $t->listOfCusts as  $as => $p ): ?>
                    <?php
                        $c = $t->custs[ $as ];
                        $cid = $c[ "id" ];
                    ?>

                    <?php if( $p ): ?>
                        <tr>
                            <td id="peer-name-<?= $cid ?>">
                                <?= $c[ "name" ] ?>
                            </td>
                            <td>
                                <?= $c[ "autsys" ] ?>
                            </td>
                            <td>
                                <?= $c[ "peeringpolicy" ] ?>
                            </td>
                            <?php foreach( $t->vlans as $avlan ): ?>
                                <?php $vlan = $avlan->number ?>
                                <?php if( isset( $c[ $vlan ] ) ): ?>
                                    <td>
                                        <?php foreach( $t->protos as $proto ): ?>
                                            <?php if( isset( $c[ $vlan ][ $proto ] ) ): ?>
                                                <span class="badge <?= ( $c[ $vlan ][ $proto ] )? "badge-success" : "badge-danger" ?>" >
                                                    IPv<?= $proto ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </td>
                                <?php elseif( isset( $t->me[ "vlan_interfaces" ][ $vlan ] ) ): ?>
                                    <td></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <td>
                                <div class="btn-group btn-group-sm my-auto">
                                    <button id="peering-request-<?= $cid ?>"
                                            data-object-id="<?= $cid ?>"
                                            data-days="<?= isset( $t->peers[ $cid ] ) ? $t->peers[ $cid ][ "email_days" ] : -1 ?>"
                                            class="btn btn-white btn-sm peering-request" <?= !$c[ "ispotential" ] ? "disabled" : "" ?>>
                                        <i id="peering-request-icon-<?= $cid ?>"
                                           class="fa  <?= isset( $t->peers[ $cid ][ "emails_sent"] ) && $t->peers[ $cid ][ "emails_sent" ] ? "fa-repeat" : "fa-envelope" ?>"></i> Request Peering
                                    </button>

                                    <button id="peering-notes-<?= $cid ?>" class="btn btn-white btn-sm peering-note" data-object-id="<?= $cid ?>">
                                        <i id="peering-notes-icon-<?= $cid ?>" class="fa fa-star" <?= isset( $t->peers[ $cid ][ "notes" ] ) && strlen( $t->peers[ $cid ][ "notes" ] ) ?: "style='color:lightgrey'" ?>></i> Notes
                                    </button>

                                    <button id="dropdown-mark-peering-<?= $cid ?>" class="btn btn-sm <?= isset( $t->peers[ $cid ] ) && ( $t->peers[ $cid ][ "peered"] || $t->peers[ $cid ][ "rejected" ] ) ? "btn-info" : "btn-white" ?> dropdown-toggle" data-toggle="dropdown"></button>

                                    <div class="dropdown-menu" >
                                        <a class="dropdown-item" id="mark-peered-<?= $cid ?>" href="<?= route( "peering-manager@mark-peering", [ "id" => $cid, "status" => "peered" ] ) ?>">
                                            <?php if( isset( $t->peers[ $cid ] ) && $t->peers[ $cid ][ "peered" ] ): ?>
                                                Unmark as Peered
                                            <?php else: ?>
                                                Move to Peered
                                            <?php endif; ?>
                                        </a>

                                        <a class="dropdown-item" id="mark-rejected-<?= $cid ?>" href="<?= route( "peering-manager@mark-peering", [ "id" => $cid, "status" => "rejected" ] ) ?>">
                                            <?php if( isset( $t->peers[ $cid ] ) && $t->peers[ $cid ][ "rejected" ] ): ?>
                                                Unmark as Rejected / Ignored
                                            <?php else: ?>
                                                Move to Rejected / Ignored
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <input id="custid" type="hidden" value="<?= $t->c->id ?>">
    </div>
</div>
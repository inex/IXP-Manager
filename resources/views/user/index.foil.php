<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Users
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">

        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/usage/users/">
            Documentation
        </a>

        <a id="add-user" class="btn btn-white" href="<?= route('user@add-wizard') ?>">
            <i class="fa fa-plus"></i>
        </a>

    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <span id="message-ppp"></span>

            <table id='table-list' class=" table table-striped" width="100%">
                <thead class="thead-dark">
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        Username
                    </th>
                    <th>
                        Email
                    </th>
                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <th>
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                        </th>
                    <?php endif; ?>
                    <th>
                        Privileges
                    </th>
                    <th>
                        Enabled
                    </th>
                    <th>
                        Created
                    </th>

                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <th>
                            Updated
                        </th>
                    <?php endif; ?>

                    <?php if( config( 'auth.peeringdb.enabled' ) ): ?>
                        <th>
                            OAuth
                        </th>
                    <?php endif; ?>
                    <th>
                        2FA Enabled
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
                <thead>
                <tbody>
                    <?php foreach( $t->users as $u ): ?>
                        <tr>
                            <td>
                                <?= $t->ee( $u[ "name" ] ) ?>
                            </td>
                            <td>
                                <?= $t->ee( $u[ "username" ] )?>
                            </td>
                            <td>
                                <?= $t->ee( $u[ "email" ] ) ?>
                            </td>

                            <?php if( Auth::getUser()->isSuperUser() ): ?>
                                <td>
                                    <?php if( $u['nbC2U'] > 1 ) : ?>
                                        <a href="<?= route( "user@edit" , [ "id" => $u[ 'id' ] ] ) ?>" class="badge badge-info"> Multiple (<?= $u['nbC2U'] ?>)</a>
                                    <?php else: ?>
                                        <a href="<?=  route( "customer@overview" , [ "id" => $u[ 'custid' ] ] ) ?>">
                                            <?= $t->ee( $u['customer'] ) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>

                            <td>
                                <?= \Entities\User::$PRIVILEGES_TEXT[ $u[ "privileges" ] ]  ?>
                                <?= ( $u['nbC2U'] > 1 ) ? "*": "" ?>
                            </td>

                            <td>
                                <?= $u[ "disabled" ] ? "<span class='badge badge-danger'>No</span>" : "<span class='badge badge-success'>Yes</span>" ?>
                            </td>
                            <td>
                                <?= $u[ "created" ] ? $u[ "created" ]->format( 'Y-m-d H:i:s' ) : '' ?>
                            </td>
                            <?php if( Auth::getUser()->isSuperUser() ): ?>
                                <td>
                                    <?= $u[ "lastupdated" ] ? $u[ "lastupdated" ]->format( 'Y-m-d H:i:s' ) : '' ?>
                                </td>
                            <?php endif;?>

                            <?php if( config( 'auth.peeringdb.enabled' ) ): ?>
                                <td>
                                    <?= $u['peeringdb_id'] ? 'Y' : 'N' ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?= $u['google2fa_enabled'] ? 'Yes' : 'No' ?>
                            </td>
                            <td>

                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-white" href="<?= route('user@view' , [ 'id' => $u[ 'id' ] ] ) ?>"  title="Preview">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <a class="btn btn-white" id='d2f-list-edit-<?= $u[ 'id' ] ?>' href="<?= route('user@edit' , [ 'id' => $u[ 'id' ] ] ) ?> " title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                        <a class="btn btn-white d2f-list-delete" id='d2f-list-delete-<?= $u[ 'id' ] ?>' data-nb-c2u="<?= $u[ 'nbC2U' ] ?>" href="<?= route( 'user@delete' )  ?>" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-white d2f-list-delete" id='d2f-list-delete-<?= $t->nbC2u[ $u[ 'id' ] ] > 1 ? $u[ 'c2uid' ] : $u[ 'id' ] ?>' data-nb-c2u="<?= $t->nbC2u[ $u[ 'id' ] ] ?>" href="<?= $t->nbC2u[ $u[ 'id' ] ] > 1 ? route( 'customer-to-user@delete' ) : route('user@delete' )  ?>" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                        <button id="d2f-more-options-<?= $u[ 'id' ] ?>" type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <form id="welcome-email" method="POST" action="<?= route('user@welcome-email' ) ?>">
                                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="id" value="<?= $u[ 'id' ] ?>">
                                                <input type="hidden" name="resend" value="1">
                                                <button class="dropdown-item" type="submit">
                                                    Resend welcome email
                                                </button>
                                            </form>

                                            <a class="dropdown-item" href="<?= route( "login-history@view",     [ 'id' => $u['id'] ]   )    ?>">
                                                Login history
                                            </a>
                                            <a id="d2f-option-login-as-<?= $u[ 'id' ] ?>" class="dropdown-item <?= $u[ 'disabled' ] || Auth::getUser()->getId() == $u['id'] ? "disabled" : "" ?>" href="<?= route( "switch-user@switch", [ "id" => $u['id'] ] ) ?>">
                                                Login as
                                            </a>

                                            <?php if( $u['google2fa_enabled'] ): ?>
                                                <a id="d2f-option-remove-2fa-<?= $u[ 'psid' ] ?>" class="dropdown-item remove-2fa" data-object-id="<?= $u[ 'psid' ] ?>" href="#">
                                                    Remove 2FA
                                                </a>
                                            <?php endif; ?>

                                        </ul>
                                    <?php endif;?>
                                </div>

                            </td>

                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>

    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'user/js/index' ); ?>
<?php $this->append() ?>
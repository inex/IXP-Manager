<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */

    $isSuperUser  =  Auth::user()->isSuperUser();
    $authId       = Auth::id();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Users
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/usage/users/">
            Documentation
        </a>

        <a id="add-user" class="btn btn-white" href="<?= route('user@create-wizard') ?>">
            <i class="fa fa-plus"></i>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>

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
                        <?php if( $isSuperUser ): ?>
                            <th>
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                            </th>
                        <?php endif; ?>
                        <th>
                            Privs
                        </th>
                        <th>
                            Flags
                        </th>
                        <th>
                            Created
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
                                <?= $t->ee( $u[ 'name' ] ) ?>
                            </td>
                            <td>
                                <?= $t->ee( $u[ 'username' ] )?>
                                <?php if( $u[ 'disabled' ] ): ?>
                                    <span class="badge badge-danger">X</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $t->ee( $u[ 'email' ] ) ?>
                            </td>

                            <?php if( $isSuperUser ): ?>
                                <td>
                                    <?php if( $u['nbC2U'] > 1 ) : ?>
                                        <a href="<?= route( 'user@edit' , [ 'u' => $u[ 'id' ] ] ) ?>" class="badge badge-info">
                                          Multiple (<?= $u['nbC2U'] ?>)
                                        </a>
                                    <?php else: ?>
                                        <a href="<?=  route( "customer@overview" , [ 'cust' => $u[ 'custid' ] ] ) ?>">
                                            <?= $t->ee( $u['customer'] ) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?= \IXP\Models\User::$PRIVILEGES_TEXT_VSHORT[ $u[ "privileges" ] ] ?? 'X' ?>
                                <?= ( $u[ 'nbC2U' ] > 1 ) ? "*": "" ?>
                            </td>
                            <td>
                                <?php if( $u[ 'u2fa_enabled' ] ): ?>
                                    <span class="badge badge-success">2FA</span>
                                <?php endif; ?>

                                <?php if( config( 'auth.peeringdb.enabled' ) && $u['peeringdb_id'] ): ?>
                                    <span class="badge badge-success">OAuth</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $u[ "created" ] ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-white" href="<?= route('user@view' , [ 'u' => $u[ 'id' ] ] ) ?>"  title="Preview">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a class="btn btn-white" id='btn-edit-<?= $u[ 'id' ] ?>' href="<?= route('user@edit' , [ 'u' => $u[ 'id' ] ] ) ?> " title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <?php if( $isSuperUser ): ?>
                                        <a class="btn btn-white btn-delete" id="btn-delete-<?= $u[ 'id' ] ?>" data-object-id='<?= $u[ 'id' ] ?>' data-nb-c2u="<?= $u[ 'nbC2U' ] ?>" href="<?= route( 'user@delete', [ 'u' => $u[ 'id' ] ] )  ?>" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-white btn-delete" id="btn-delete-<?= $u[ 'id' ] ?>" data-object-id='<?= $t->nbC2u[ $u[ 'id' ] ][ 'nbC2U' ] > 1 ? $u[ 'c2uid' ] : $u[ 'id' ] ?>' data-nb-c2u="<?= $t->nbC2u[ $u[ 'id' ] ][ 'nbC2U' ] ?>" href="<?= $t->nbC2u[ $u[ 'id' ] ][ 'nbC2U' ] > 1 ? route( 'customer-to-user@delete' , [ 'c2u' => $u[ 'c2uid' ] ] ) : route('user@delete' , [ 'u' => $u[ 'id' ] ] )  ?>" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if( $isSuperUser ): ?>
                                        <button id="d2f-more-options-<?= $u[ 'id' ] ?>" type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <form id="welcome-email" method="POST" action="<?= route('user@welcome-email', [ 'u' => $u[ 'id' ] ] ) ?>">
                                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                                <button class="dropdown-item" type="submit">
                                                    Resend welcome email
                                                </button>
                                            </form>

                                            <a class="dropdown-item" href="<?= route( "login-history@view", [ 'id' => $u['id'] ] ) ?>">
                                                Login history
                                            </a>
                                            <?php if( $u['c2uid'] ): ?>
                                                <a id="d2f-option-login-as-<?= $u[ 'id' ] ?>" class="dropdown-item <?= $u[ 'disabled' ] || $authId  === $u['id'] ? "disabled" : "" ?>" href="<?= route( "switch-user@switch", [ "c2u" => $u['c2uid'] ] ) ?>">
                                                    Login as
                                                </a>
                                            <?php endif; ?>

                                            <?php if( $u['u2fa_enabled'] ): ?>
                                                <a id="d2f-option-remove-2fa-<?= $u[ 'id' ] ?>" class="dropdown-item remove-2fa" data-object-id="<?= $u[ 'id' ] ?>" href="#">
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

    <div class="tw-mt-16 tw-border-2 tw-border-gray-400 tw-rounded-lg">
        <p class="tw-p-6 tw-m-0">
            <b>Privileges:</b> CU - Cust User; CA - Cust Admin; SU - Super User.<br>
            <b>Flags:</b> <span class="badge badge-success">2FA</span> - Two-factor authentication is enabled; <span class="badge badge-success">OAuth</span> - user created via PeeringDB OAuth.<br>
            <b>Disabled Users:</b> - identified with <span class="badge badge-danger">X</span> badge beside username.
        </p>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'user/js/index' ); ?>
<?php $this->append() ?>
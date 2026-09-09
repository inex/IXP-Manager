<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Users / View User
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/latest/usage/users/">
            <?= __( 'Documentation' ) ?>
        </a>

        <a id="add-user" class="btn btn-white" href="<?= route('user@list') ?>">
            <i class="fa fa-table-list"></i>
        </a>

        <a id="add-user" class="btn btn-white" href="<?= route('user@edit' , [ "u" => $t->u[ 'id' ] ] ) ?>">
            <i class="fa fa-pencil"></i>
        </a>

        <a id="add-user" class="btn btn-white" href="<?= route('user@create-wizard') ?>">
            <i class="fa fa-plus"></i>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header tw:flex">
                    <div class="mr-auto">
                        Details for User (DB ID: <?= $t->u[ 'id' ] ?>
                    </div>

                    <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\User::class, 'logSubject') && Auth::user()->isSuperUser() ): ?>
                        <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'User' , 'model_id' => $t->u[ 'id' ]  ] ) ?>">
                            <?= __( 'View logs' ) ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body row">
                    <div class="col-lg-12 col-md-12">
                        <table class="table_view_info">
                            <tr>
                                <td>
                                    <b>
                                        <?= __( 'Name' ) ?>
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->u[ 'name' ] ) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>
                                        <?= __( 'Username' ) ?>
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->u[ 'username' ] ) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>
                                        <?= __( 'Email' ) ?>
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->u[ 'email' ] ) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>
                                        <?= __( 'Privileges' ) ?>
                                    </b>
                                </td>
                                <td>
                                    <?=  \IXP\Models\User::$PRIVILEGES_TEXT_ALL[ $t->u['privileges'] ] ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>
                                        <?= __( 'Enabled' ) ?>
                                    </b>
                                </td>
                                <td>
                                    <?= $t->u['disabled'] ? "No" : "Yes" ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>
                                        <?= __( '2FA Enabled' ) ?>
                                    </b>
                                </td>
                                <td>
                                    <?= $t->u['u2fa_enabled'] ? "Yes" : "No" ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>
                                        <?= __( 'Created' ) ?>
                                    </b>
                                </td>
                                <td>
                                    <?= $t->u['created'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        <?= __( 'Created By' ) ?>
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->u['creator'] ?? '') ?>
                                </td>
                            </tr>

                            <?php if( Auth::user()->isSuperUser() ): ?>
                                <tr>
                                    <td>
                                        <b>
                                            <?= __( 'Updated' ) ?>
                                        </b>
                                    </td>
                                    <td>
                                        <?= $t->u['lastupdated'] ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if( Auth::user()->isSuperUser() ): ?>
                                <tr>
                                    <td colspan="2">
                                        <table class="table table-striped w-100">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>
                                                        <?= __c( ':Customer' ) ?>
                                                    </th>
                                                    <th>
                                                        <?= __( 'Privilege' ) ?>
                                                    </th>
                                                    <th>
                                                        <?= __( 'Created By' ) ?>
                                                    </th>
                                                    <th>
                                                        <?= __( 'Created' ) ?>
                                                    </th>
                                                    <th>
                                                        <?= __( 'Updated' ) ?>
                                                    </th>
                                                    <th>
                                                        <?= __( 'Actions' ) ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach( $t->c2us as $c2u ): ?>
                                                    <tr>
                                                        <td>
                                                            <?= $t->ee( $c2u->customer->name ) ?>
                                                        </td>
                                                        <td>
                                                            <?=  \IXP\Models\User::$PRIVILEGES_TEXT[ $c2u->privs ] ?>
                                                        </td>
                                                        <td>
                                                            <?= $t->ee( $c2u->extra_attributes['created_by']['type'] ?? '' ) ?>
                                                        </td>
                                                        <td>
                                                            <?= $c2u->created_at ?>
                                                        </td>
                                                        <td>
                                                            <?= $c2u->updated_at ?>
                                                        </td>
                                                        <td>
                                                            <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\CustomerToUser::class, 'logSubject') ): ?>
                                                                <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'CustomerToUser' , 'model_id' => $c2u->id ] ) ?>">
                                                                    <?= __( 'View logs' ) ?>
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach;?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
            <br><br><br>
            <p>
                <?= __c( 'The :createdBy column indicates how the user was linked to the :customer. The information you may see includes:', [
                    'createdBy' => '<em>' . __( 'created by' ) . '</em>' ] ) ?>
            </p>
            <ul>
                <li> <?= __c( ':label the user originally belonged to this :customer in versions of IXP Manager &lt;v5.0 when users where linked 1:1 with :customers.', [
                    'label' => '<em>' . __( 'migration-script:' ) . '</em>' ] ) ?> </li>
                <li> <?= __c( ':label the user was linked to this :customer by either a :customer admin or a super admin.', [
                    'label' => '<em>' . __( 'user:' ) . '</em>' ] ) ?> </li>
                <li> <em><?= __( 'PeeringDB:' ) ?></em> <?= __( 'the user was added via a PeeringDB OAuth login.' ) ?> </li>
            </ul>
        </div>
    </div>
<?php $this->append() ?>
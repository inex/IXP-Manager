
<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
Users / View User
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm ml-auto" role="group">

        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/usage/users/">
            Documentation
        </a>

        <a id="add-user" class="btn btn-white" href="<?= route('user@list') ?>">
            <i class="fa fa-th-list"></i>
        </a>

        <a id="add-user" class="btn btn-white" href="<?= route('user@edit' , [ "id" => $t->u[ 'id' ] ] ) ?>">
            <i class="fa fa-pencil"></i>
        </a>
        <a id="add-user" class="btn btn-white" href="<?= route('user@add-wizard') ?>">
            <i class="fa fa-plus"></i>
        </a>

    </div>

<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
<div class="row">

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                Details for User (DB ID: <?= $t->u[ 'id' ] ?> )
            </div>
            <div class="card-body row">
                <div class="col-lg-6 col-md-12">
                    <table class="table_view_info">
                        <tr>
                            <td>
                                <b>
                                    Name
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $t->u[ 'name' ] ) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>
                                    Username
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $t->u[ 'username' ] ) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>
                                    Email
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $t->u[ 'email' ] ) ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>
                                    Privileges
                                </b>
                            </td>
                            <td>
                                <?= $t->u['privileges'] ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>
                                    Enabled
                                </b>
                            </td>
                            <td>
                                <?= $t->u['disabled'] ? "No" : "Yes" ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Created
                                </b>
                            </td>
                            <td>
                                <?php if( $t->u['created'] != null): ?>
                                    <?= $t->u['created']->format( 'Y-m-d H:i:s' ) ?>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>
                                    Created By
                                </b>
                            </td>
                            <td>
                                    <?= $t->u['creator'] ?? '' ?>
                            </td>
                        </tr>

                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                            <tr>
                                <td>
                                    <b>
                                        Updated
                                    </b>
                                </td>
                                <td>
                                    <?php if( $t->u['lastupdated'] != null): ?>
                                        <?= $t->u['lastupdated']->format( 'Y-m-d H:i:s' ) ?>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                            <tr>

                                <td colspan="2">

                                    <?php $user = D2EM::getRepository( Entities\User::class )->find( $t->u[ 'id' ] ) ?>
                                    <table class="table table-striped" width="100%">
                                        <thead class="thead-dark">
                                        <th>
                                            Customer
                                        </th>
                                        <th>
                                            Privilege
                                        </th>
                                        <th>
                                            Created By
                                        </th>
                                        </thead>
                                        <tbody>
                                        <?php foreach( $user->getCustomers2User() as $c ): ?>
                                            <tr>
                                                <td>
                                                    <?= $t->ee( $c->getCustomer()->getName() ) ?>
                                                </td>
                                                <td>
                                                    <?=  \Entities\User::$PRIVILEGES_TEXT[ $c->getPrivs() ] ?>
                                                </td>
                                                <td>
                                                    <?= $t->ee( $c->getExtraAttributes()['created_by']['type'] ?? '' ) ?>
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
            The <em>created by</em> column indicates how the user was linked to the customer. The information you may see includes:
        </p>
        <ul>
            <li> <em>migration-script:</em> the user originally belonged to this customer in versions of IXP Manager &lt;v5.0 when users where linked 1:1 with customers. </li>
            <li> <em>user:</em> the user was linked to this customer by either a customer admin or a super admin. </li>
            <li> <em>PeeringDB:</em> the user was added via a PeeringDB OAuth login. </li>
        </ul>
    </div>
</div>
<?php $this->append() ?>

<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>



<?php $this->section( 'title' ) ?>
    IPv<?= $t->protocol ?> Addresses
<?php $this->append() ?>



<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route ('ip-address@add', [ 'protocol' => $t->protocol ]) ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>



<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <div class="well col-md-12">

        <form class="form-inline">

            <div class="form-group">
                <label for="vlan">VLAN</label>
                <select id="vlan" name="vlan" class="form-control">
                    <option></option>
                    <?php foreach( $t->vlans as $vid => $vname ): ?>
                        <option value="<?= $vid ?>" <?= $t->vlan && $vid == $t->vlan->getId() ? 'selected' : '' ?>><?= $vname ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if( $t->vlan ): ?>
                <a class="btn btn-sm btn-default" href="<?= route( 'ip-address@list', [ 'vid' => $t->vlan->getId(), 'protocol' => ( $t->protocol == 4 ? 6 : 4 ) ] ) ?>">
                    Switch to IPv<?= $t->protocol == 4 ? 6 : 4 ?>
                </a>
                <a type="button" class="btn btn-sm btn-default" href="<?= route ('ip-address@add', [ 'protocol' => $t->protocol ]) ?>?vlan=<?= $t->vlan->getId() ?>">
                    <span class="glyphicon glyphicon-plus"></span>
                </a>
            <?php endif; ?>

        </form>

    </div>

    <?php if( !count( $t->ips ) ): ?>


    <?php else: ?>

        <table id='ip-address-list' class="table collapse" >
            <thead>
                <tr>
                    <td>
                        IP Address
                    </td>
                    <td>
                        Customer
                    </td>
                    <td>
                        Hostname
                    </td>
                    <td>
                        Action
                    </td>
                </tr>
            <thead>
            <tbody>
                <?php foreach( $t->ips as $ip ):?>
                    <tr>
                        <td>
                            <?= $t->ee( $ip[ 'address' ] ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $ip[ 'customer' ] ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $ip[ 'hostname' ] ) ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-default <?= $ip[ 'viid' ] ? '' : 'disabled' ?>" href="<?= $ip[ 'viid' ] ? route( "interfaces/virtual/edit" , [ 'id' => $ip[ 'viid' ] ] ) : '#' ?>" title="See interface">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                </a>
                                <a class="btn btn btn-default <?= !$ip[ 'vliid' ] ? '' : 'disabled' ?>" id="delete-ip-<?=$ip[ 'id' ] ?>" href="" title="Delete">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>

    <?php endif;  /* !count( $t->ips ) */ ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'ip-address/js/list.foil.js' ) ?>
<?php $this->append() ?>
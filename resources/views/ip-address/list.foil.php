<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    IPv<?= $t->protocol ?> Addresses
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <?php if( count( $t->vlans ) ): ?>
            <a class="btn btn-white" href="<?= route ('ip-address@create', [ 'protocol' => $t->protocol ]) ?>">
                <span class="fa fa-plus"></span>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>

            <?php if( $t->vlans->isEmpty() ): ?>
                <div class="alert alert-info" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <b>No VLAN exists and so you cannot add IP addresses yet.</b>
                            Start by <a href="<?= route('vlan@list') ?>">adding a VLAN</a>.
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mb-4 bg-light">
                    <div class="card-body">
                        <div class="form-inline">
                            <div class="form-group row tw-pl-16">
                                <label for="vlan" class="col-sm-2 col-form-label">
                                    VLAN
                                </label>
                                <div class="col-sm-10">
                                    <select id="vlan" name="vlan" class="form-control tw-min-w-full">
                                        <option></option>
                                        <?php foreach( $t->vlans as $v ): ?>
                                            <option value="<?= $v->id ?>" <?= $t->vlan && $v->id === $t->vlan->id ? 'selected' : '' ?>><?= $v->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <?php if( $t->vlan ): ?>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-white" href="<?= route( 'ip-address@list', [ 'vlanid' => $t->vlan->id, 'protocol' => ( $t->protocol === 4 ? 6 : 4 ) ] ) ?>">
                                        Switch to IPv<?= $t->protocol === 4 ? 6 : 4 ?>
                                    </a>

                                    <a class="btn btn-white" href="<?= route ('ip-address@create', [ 'protocol' => $t->protocol, 'vlan' => $t->vlan->id  ]) ?>">
                                        <span class="fa fa-plus"></span>
                                    </a>

                                    <a class="btn btn-danger" href="<?= route ('ip-address@delete-by-network', [ 'vlan' => $t->vlan->id ]) ?>">
                                        <span class="fa fa-trash"></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if( !count( $t->ips ) ): ?>
                <?php if( $t->vlan ): ?>
                    <p>
                        There are no IPv<?= $t->protocol ?> addresses in this VLAN.
                        <a href="<?= route ('ip-address@create', [ 'protocol' => $t->protocol, 'vlan' => $t->vlan->id ] ) ?>">Create some...</a>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <table id='ip-address-list' class="table collapse table-stripped" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                IP Address
                            </th>
                            <th>
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                            </th>
                            <th>
                                Hostname
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    <thead>
                    <tbody>
                        <?php foreach( $t->ips as $ip ):?>
                            <tr>
                                <td data-order="<?= $t->ee( $ip[ 'aton' ] ) ?>">
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
                                        <a class="btn btn-white <?= $ip[ 'viid' ] ?: 'disabled' ?>" href="<?= $ip[ 'viid' ] ? route( 'virtual-interface@edit' , [ 'vi' => $ip[ 'viid' ] ] ) : '#' ?>" title="See interface">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-white delete-ip <?= !$ip[ 'vliid' ] ?: 'disabled' ?>" href="<?= !$ip[ 'viid' ] ? route( "ip-address@delete" , [ 'id' => $ip[ 'id' ] ] ) : '#' ?>" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <tbody>
                </table>
            <?php endif;  /* !count( $t->ips ) */ ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'ip-address/js/list.foil.php' ) ?>
<?php $this->append() ?>
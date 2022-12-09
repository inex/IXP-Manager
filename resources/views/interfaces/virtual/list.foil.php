<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Virtual Interfaces / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class=" btn-group btn-group-sm" role="group">
        <button class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route( 'virtual-interface@wizard' ) ?>" >
                Add Interface Wizard...
            </a>

            <a class="dropdown-item" href="<?= route( 'virtual-interface@create' ) ?>" >
                Add Virtual Interface Object Only...
            </a>
        </ul>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <table id='table-vi' class="collapse table table-stripped no-wrap table-responsive-ixp-with-header w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                        </th>
                        <th>
                            Facility
                        </th>
                        <th>
                            Switch
                        </th>
                        <th>
                            Port(s)
                        </th>
                        <th>
                            Speed
                        </th>
                        <th>
                            Raw Speed
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                <thead>
                <tbody>
                    <?php foreach( $t->vis as $vi ): ?>
                        <tr>
                            <td>
                                <a href="<?= route( "customer@overview" , [ 'cust' => $vi[ 'custid' ] ] ) ?>">
                                    <?= $t->ee( $vi[ 'custname' ] ) ?>
                                </a>
                            </td>
                            <?php if( $vi[ 'nbpi' ] > 0 ): ?>
                                <td>
                                    <?= $t->ee( $vi[ 'locationname' ] ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $vi[ 'switchname' ] ) ?>
                                </td>
                                <td>
                                    <?php

                                    $sps = explode( ',', $vi[ 'switchport' ] );
                                    foreach( $sps as $sp ){
                                      echo $sp . '<br/>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?= $t->scaleBits( ( $vi['rate_limit'] ?: $vi[ 'speed' ] ) * 1000 * 1000, 0 ) ?>
                                    <?php if( $vi['rate_limit'] ): ?>
                                        <span class="badge badge-info" data-toggle="tooltip" title="Rate Limited">RL</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $vi['rate_limit'] ?: $vi[ 'speed' ] ?>
                                </td>
                            <?php else: ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php endif; ?>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn btn-white" href="<?= route( 'virtual-interface@edit' , [ 'vi' => $vi[ 'id' ] ] ) ?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <a class="btn btn btn-white btn-delete" href="<?= route( 'virtual-interface@delete', [ 'vi' => $vi[ 'id' ] ] ) ?>" <?php if( $t->resellerMode() && ( $vi[ 'peering' ] || $vi[ 'fanout' ] ) ) :?> data-related="1" <?php endif; ?> <?php if( $vi[ 'switchport' ] ): ?> data-type="<?= explode( ',', $vi[ 'switchporttype' ] )[0] ?>" <?php endif; ?> title="Delete Virtual Interface">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;?>
                <tbody>
            </table>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/virtual/js/interface' ); ?>

    <script>
        /**
         * on click even allow to delete a Virtual Interface
         */
        $( '.btn-delete' ).click( function( e ){
            e.preventDefault();
            deletePopup( $( this ), 'vi');
        });
    </script>
<?php $this->append() ?>
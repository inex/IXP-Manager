<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filtering :: Customers with Filters
<?php $this->append() ?>


<?php $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>

            <?php if( $t->customers->count() ): ?>
                <table id='table-list' class="table table-striped table-responsive-ixp-with-header" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Customer
                            </th>
                            <th>
                                # Rules in Production
                            </th>
                        </tr>
                    <thead>
                    <tbody>
                        <?php foreach( $t->customers as $c ):
                            /** @var $c \IXP\Models\Customer */?>
                            <tr>
                                <td>
                                    <a href="<?= route( 'customer@overview', [ 'cust' => $c->id ] ) ?>">
                                        <?= $t->ee( $c->name ) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= route( 'rs-filter@list', [ 'cust' => $c->id ] ) ?>">
                                        <?= $t->ee( $c->prod_rules ) ?>
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach;?>
                    <tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12 d-flex">
                            <b class="mr-auto my-auto">
                                No customer has route server filters configured.
                            </b>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'rs-filter/js/list' ); ?>
<?php $this->append() ?>
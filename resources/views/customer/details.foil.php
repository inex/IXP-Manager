<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>

<?php $this->append() ?>

<?php $this->section( 'title' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( 'customer@list' )?>">Customers</a>
    <?php else: ?>
        Member Details
    <?php endif; ?>
<?php $this->append() ?>

<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

    <?php $this->section( 'page-header-postamble' ) ?>
        <li>Details</li>
    <?php $this->append() ?>

<?php endif; ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>
<table id='customer-list' class="table collapse" >
    <thead>
        <tr>
            <td>
                Member
            </td>
            <td>
                Peering Email
            </td>
            <td>
                ASN
            </td>
            <td>
                NOC Phone
            </td>
            <td>
                NOC Hours
            </td>
            <td>
                Action
            </td>
        </tr>
    <thead>
    <tbody>
        <?php foreach( $t->custs as $c ):
            /** @var Entities\Customer $c */
            ?>
            <tr>
                <td>
                    <a href="<?= route( "customer@overview" , [ "id" => $c->getId() ] ) ?>">
                        <?= $t->ee( $c->getName() ) ?>
                    </a>

                </td>
                <td>
                    <?= $t->ee( $c->getPeeringemail() ) ?>
                </td>
                <td>
                    <?php if( $c->getType() == \Entities\Customer::TYPE_ASSOCIATE ): ?>
                        <em>(associate)</em>
                    <?php else: ?>
                        <?php if( $c->getAutsys() ): ?>
                            <a href="#">
                                <?=  $t->asNumber( $c->getAutsys() ) ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                </td>
                <td>
                    <?= $c->getNocphone() ?>
                </td>
                <td>
                    <?= $c->getNochours() ?>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn btn-default" href="<?= route ( 'customer@detail' , [ 'id' => $c->getId() ] )  ?>" title="View">
                            <i class="glyphicon glyphicon-eye-open"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach;?>
    <tbody>
</table>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<script>
    $(document).ready( function() {
        $( '#customer-list' ).dataTable( { "autoWidth": false, "iDisplayLength": 100 } );

        $( '#customer-list' ).show();
    });
</script>
<?php $this->append() ?>

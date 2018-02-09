<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'title' ) ?>
<a href="<?= route ( 'customer@list' )?>">
    Customer
</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <li>Logos</li>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="col-md-12">
        <?php $count = 0 ?>
        <?php foreach( $t->logos as $logo ): ?>

            <div class="col-sm-3">

                <a href="<?= route( "logo@manage" , [ "id" => $logo->getCustomer()->getId() ] ) ?>">
                    <img class="www80-padding" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>" />
                </a>

            </div>

            <?php $count++ ?>

            <?php if( $count%4 == 0 ): ?>
                </div><br /><div class="col-md-12">
            <?php endif; ?>

        <?php endforeach; ?>

        <?php if( $count%4 != 0 ): ?>
            <div class="span3"></div>
            <?php $count++ ?>
            <?php if( $count%4 != 0): ?>
                <div class="span3"></div>
                <?php $count++ ?>
                <?php if( $count%4 != 0): ?>
                    <div class="span3"></div>
                    <?php $count++ ?>
                <?php endif; ?>
            <?php endif; ?>
         <?php endif; ?>

    </div>

<?php $this->append() ?>


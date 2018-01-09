<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    Customer
<?php $this->append() ?>



<?php $this->section( 'page-header-postamble' ) ?>
    <li>Unread Notes for You</li>

<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( count( $t->notes ) ): ?>
        <li class="pull-right">
            <div class="btn-group btn-group-xs" role="group">
                <a type="button" class="btn btn-default" href="<?= route('customerNotes@readAll') ?>">
                    Mark All As Read
                </a>
            </div>
        </li>
    <?php endif;?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

    <?= $t->alerts() ?>

    <div class="row">
        <div class="col-md-12">

            <?php if( count( $t->notes ) ): ?>
                The following customers have new or updated notes that you have not seen.
            <?php else: ?>
                There are no notes for any customers that you have not seen.
            <?php endif; ?>

        </div>
    </div>

    <?php if( count( $t->notes ) ): ?>
        <div class="row">
            <div class="col-md-12">

                <table class="table" id="list-table-notes">
                    <thead>
                    <th>Customer</th>
                    <th>Notes Last Created / Updated</th>
                    </thead>
                    <tbody>
                        <?php foreach( $t->notes as $n ): ?>
                        <tr>
                            <td>
                                <a href="<?=  url( "customer/overview/id/" )."/".$n[ 'cid' ] ?>" >
                                    <?= $n[ 'cname' ] ?>
                                </a>
                            </td>
                            <td>
                                <?= $n[ 'latest' ] ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


            </div>
        </div>
    <?php endif; ?>

<?php $this->append() ?>


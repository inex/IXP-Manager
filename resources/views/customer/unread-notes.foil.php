<?php $this->layout( 'layouts/ixpv4' ); ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Customer / Unread Notes for You</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( count( $t->notes ) ): ?>
        <div class="btn-group btn-group-sm" role="group">
            <a class="btn btn-white" href="<?= route('customerNotes@readAll') ?>">
                Mark All As Read
            </a>
        </div>
    <?php endif;?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>
            <div class="alert alert-info" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-info-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        <?php if( count( $t->notes ) ): ?>
                            The following customers have new or updated notes that you have not seen.
                        <?php else: ?>
                            There are no notes for any customers that you have not seen.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?= $t->alerts() ?>
            <?php if( count( $t->notes ) ): ?>
                <table class="table table-striped table-responsive-ixp" id="list-table-notes" width="100%">
                    <thead class="thead-dark">
                        <th>
                            Customer
                        </th>
                        <th>
                            Notes Last Created / Updated
                        </th>
                    </thead>
                    <tbody>
                        <?php foreach( $t->notes as $n ): ?>
                            <tr>
                                <td>
                                    <a href="<?= route( "customer@overview" , [ 'cust' => $n[ 'cid' ], 'tab' => 'notes' ] ) ?>" >
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

            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $(document).ready( function() {
            $('.table-responsive-ixp').datatable( {
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive: true,
                ordering: false,
                searching: false,
                paging:   false,
                info:   false,
            } ).show();
        });
    </script>
<?php $this->append() ?>
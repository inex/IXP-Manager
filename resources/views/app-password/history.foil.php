<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
<?php if( isset( $t->feParams->pagetitle )  ): ?>
    <?=  $t->feParams->pagetitle  ?> - Usage Records
<?php endif; ?>
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">
        <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
            <a target="_blank" class="btn btn-white" href="<?= $t->feParams->documentation ?>">
                Documentation
            </a>
        <?php endif; ?>
        <a class="btn btn-white" href="<?= route('app-password@list') ?>">
            Back to List
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $t->alerts() ?>
            
            <?php if( !count( $t->data[ 'rows' ] ) ): ?>
                <div class="alert alert-info" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <b>No usage records for this application-specific password exists.</b>
                        </div>
                    </div>
                </div>
            <?php else:  /* !count( $t->data[ 'rows' ] ) */ ?>

                <table id="table-list" class="table collapse table-striped" width="100%">
                        <thead class="thead-dark">
                        <tr>
                            <th>
                                Seen At
                            </th>
                            <th>
                                Seen From
                            </th>
                        </thead>
                    <tbody>
                    <?php foreach( $t->data[ 'rows' ] as $idx => $row ): ?>
                            <tr>
                                <td>
                                    <?= $t->ee( $row->last_seen_at ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $row->last_seen_from ) ?>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif;  /* /* !count( $t->data[ 'rows' ] ) */ ?>
            
        </div>
    </div>
<?php $this->append() ?>



<?php $this->section( 'scripts' ) ?>
<script>
    let tableList = $( '#table-list' );
    
    tableList.dataTable({
        stateSave: true,
        stateDuration : DATATABLE_STATE_DURATION,
        responsive: true,
        "aLengthMenu": [ [ 20, 50, 100, 500, -1 ], [ 20, 50, 100, 500, "All" ] ],
    
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: -1 }
        ],
    
        "aoColumns": [
            { 'bSortable': true, "bSearchable": true },
            { 'bSortable': true, "bSearchable": true }
        ]
    }).show();
</script>
<?php $this->append() ?>

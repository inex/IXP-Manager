<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Prefix Filtering Analysis Tool
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li role="presentation" class="nav-item">
                            <a href="#adv_nacc" class="nav-link active" aria-controls="adv_nacc" role="tab" data-toggle="tab">
                                Advertised but Not Accepted
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a href="#adv_acc"  class="nav-link" aria-controls="adv_acc"  role="tab" data-toggle="tab">
                                Advertised & Accepted
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a href="#nadv_acc" class="nav-link" aria-controls="nadv_acc" role="tab" data-toggle="tab">Not Advertised but Accepted</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <div role="tab-list" class="tab-pane active show" id="adv_nacc">
                        <?= $t->insert( 'rs-prefixes/list-summary', [ 'type' => 'adv_nacc'  ] ); ?>
                    </div>
                    <div role="tab-list" class="tab-pane" id="adv_acc">
                        <?= $t->insert( 'rs-prefixes/list-summary', [ 'type' => 'adv_acc'   ] ); ?>
                    </div>
                    <div role="tab-list" class="tab-pane" id="nadv_acc">
                        <?= $t->insert( 'rs-prefixes/list-summary', [ 'type' => 'nadv_acc'  ] ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $(document).ready( function() {
            $( '.table' ).dataTable({
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive : true,
                pageLength: 100
            } ).show();
        });
    </script>
<?php $this->append() ?>
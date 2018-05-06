<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    Route Server Prefix Filtering Analysis Tool
    <?php if( Auth::guest() || !Auth::user()->isSuperUser() ): ?>
        <small>
            <br>Summary by Customer
        </small>
    <?php endif; ?>
<?php $this->append() ?>



<?php if( Auth::user()->isSuperUser() ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <li>
            Summary by Customer
        </li>
    <?php $this->append() ?>
<?php endif; ?>



<?php $this->section( 'content' ) ?>

<div class="row">

    <div class="col-sm-12">

        <ul class="nav nav-tabs" role="tab-list">
            <li role="presentation" class="active">
                <a href="#adv_nacc" aria-controls="adv_nacc" role="tab" data-toggle="tab">Advertised but Not Accepted</a>
            </li>
            <li role="presentation">
                <a href="#adv_acc"  aria-controls="adv_acc"  role="tab" data-toggle="tab">Advertised & Accepted</a>
            </li>
            <li role="presentation">
                <a href="#nadv_acc" aria-controls="nadv_acc" role="tab" data-toggle="tab">Not Advertised but Accepted</a>
            </li>
        </ul>

        <!-- Tab panel -->
        <div class="tab-content">
            <div role="tab-list" class="tab-pane active" id="adv_nacc">
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



<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $(document).ready( function() {
            $( '.table' ).dataTable( { "autoWidth": false, pageLength: 100 } ).show();
        });
    </script>
<?php $this->append() ?>


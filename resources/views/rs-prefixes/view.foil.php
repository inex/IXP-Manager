<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
        Route Server Prefix /
    <?php endif; ?>
    Route Server Prefix Filtering Analysis Tool
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?= $t->insert( 'rs-prefixes/list-filter' ) ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?php if( $t->totalVl !== $t->filteredVl ): ?>
                <div class="alert alert-warning mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-exclamation-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <b>Warning!</b>
                            <?php if( $t->filteredVl ): ?>
                                Not all ports have IRRDB filtered applied.
                            <?php else: ?>
                                No ports have IRRDB filtering applied so, while this information is useful,
                                it has no impact on services for this member.
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card mt-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li role="presentation" class="nav-item" id="nav-adv_nacc">
                            <a href="#adv_nacc" class="nav-link active" aria-controls="adv_nacc" role="tab" data-toggle="tab">
                                Advertised but Not Accepted  (<?= count( $t->aggRoutes[ 'adv_nacc' ] )  ?>)
                            </a>
                        </li>
                        <li role="presentation" class="nav-item" id="nav-adv_acc">
                            <a href="#adv_acc" class="nav-link" aria-controls="adv_acc" role="tab" data-toggle="tab" >
                                Advertised & Accepted (<?= count( $t->aggRoutes[ 'adv_acc' ] )   ?>)
                            </a>
                        </li>
                        <li role="presentation" class="nav-item" id="nav-nadv_acc">
                            <a href="#nadv_acc" class="nav-link" aria-controls="nadv_acc" role="tab" data-toggle="tab"   >
                                Not Advertised but Accepted (<?= count( $t->aggRoutes[ 'nadv_acc' ] )  ?>)
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a href="#help" class="nav-link" aria-controls="help" role="tab" data-toggle="tab">
                                Help
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Tab panes -->
                <div class="card-body tab-content" id='type-tabs'>
                    <div role="tab-list" class="tab-pane active show" id="adv_nacc">
                        <?= $t->insert( 'rs-prefixes/view-route', [ 'type' => 'adv_nacc',   'protocol'  => $t->protocol, 'aggRoutes' => $t->aggRoutes ] ); ?>
                    </div>
                    <div role="tab-list" class="tab-pane" id="adv_acc">
                        <?= $t->insert( 'rs-prefixes/view-route', [ 'type' => 'adv_acc',    'protocol'  => $t->protocol, 'aggRoutes' => $t->aggRoutes ] ); ?>
                    </div>
                    <div role="tab-list" class="tab-pane" id="nadv_acc">
                        <?= $t->insert( 'rs-prefixes/view-route', [ 'type' => 'nadv_acc',   'protocol'  => $t->protocol, 'aggRoutes' => $t->aggRoutes ] ); ?>
                    </div>
                    <div role="tab-list" class="tab-pane" id="help">
                        <?= $t->insert( 'rs-prefixes/help', [ 'c' => $t->c, 'aggRoutes' => $t->aggRoutes ] ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( document ).ready( function() {
            <?php if( $t->type ): ?>
                $( '.tab-pane' ).removeClass( 'active' );
                $( '.nav-tabs li' ).removeClass( 'active' );
                $( '#<?=  $t->type ?>' ).addClass( 'active' );
                $( '#nav-<?=  $t->type ?>' ).addClass( 'active' );
            <?php endif; ?>

            $( '.table' ).dataTable({
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive : true,
                pageLength: 50
            }).show();
        });
    </script>
<?php $this->append() ?>
<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>


<?php $this->section( 'title' ) ?>
    <?php if( Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( "rs-prefixes@list" ) ?>">Route Server Prefix Filtering Analysis Tool</a>
    <?php else: ?>
        Route Server Prefix Filtering Analysis Tool
        <small>
            <br>
            <?= $t->ee( $t->c->getName() ) ?> [<?= $t->asNumber( $t->c->getAutsys() ) ?>]
            <?php if( $t->protocol ): ?>[IPv<?= $t->protocol ?>]<?php endif; ?>
        </small>
    <?php endif; ?>
<?php $this->append() ?>



<?php if( Auth::user()->isSuperUser() ): ?>

    <?php $this->section( 'page-header-postamble' ) ?>
        <li>
            <a href="<?=  url( "customer/overview/id/" )."/".$t->c->getId() ?>" >
                <?= $t->ee( $t->c->getName() ) ?>
            </a>

            [<?= $t->asNumber( $t->c->getAutsys() ) ?>]

            <?php if( $t->protocol ): ?>
                [IPv<?= $t->protocol ?>]
            <?php endif; ?>
        </li>
    <?php $this->append() ?>

<?php endif; ?>



<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::user()->isSuperUser() ): ?>
        <li class="pull-right">
            <?= $t->insert( 'rs-prefixes/list-filter' ) ?>
        </li>
    <?php else: ?>
        <div class="pull-right">
            <?= $t->insert( 'rs-prefixes/list-filter' ) ?>
        </div>
    <?php endif; ?>
<?php $this->append() ?>



<?php $this->section( 'content' ) ?>

    <?php if( $t->totalVl != $t->filteredVl ): ?>
        <div class="alert alert-warning" role="alert">
            <b>Warning!</b>
            <?php if( $t->filteredVl ): ?>
                Not all ports have IRRDB filtered applied.
            <?php else: ?>
                No ports have IRRDB filtering applied so, while this information is useful,
                it has no impact on services for this member.
            <?php endif;?>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs" role="tab-list">
        <li role="presentation" class="active" id="nav-adv_nacc">
            <a href="#adv_nacc" aria-controls="adv_nacc" role="tab" data-toggle="tab"   >Advertised but Not Accepted (<?= count( $t->aggRoutes[ 'adv_nacc' ] )  ?>)</a>
        </li>
        <li role="presentation" id="nav-adv_acc">
            <a href="#adv_acc" aria-controls="adv_acc" role="tab" data-toggle="tab"     >Advertised & Accepted (<?=       count( $t->aggRoutes[ 'adv_acc' ] )   ?>)</a>
        </li>
        <li role="presentation" id="nav-nadv_acc">
            <a href="#nadv_acc" aria-controls="nadv_acc" role="tab" data-toggle="tab"   >Not Advertised but Accepted (<?= count( $t->aggRoutes[ 'nadv_acc' ] )  ?>)</a>
        </li>
        <li role="presentation">
            <a href="#help" aria-controls="help" role="tab" data-toggle="tab">Help</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content" id='type-tabs'>
        <div role="tab-list" class="tab-pane active" id="adv_nacc">
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

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $(document).ready( function() {
            <?php if( $t->type ): ?>
                $( '.tab-pane' ).removeClass( 'active' );
                $( '.nav-tabs li' ).removeClass( 'active' );
                $( '#<?=  $t->type ?>' ).addClass( 'active' );
                $( '#nav-<?=  $t->type ?>' ).addClass( 'active' );
            <?php endif; ?>

            $( '.table' ).dataTable( { "autoWidth": false, pageLength: 50 } ).show();
        });
    </script>
<?php $this->append() ?>

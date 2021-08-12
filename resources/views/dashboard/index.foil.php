<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */
    $this->layout( 'layouts/ixpv4' );
    $c = $t->c; /** @var $c \IXP\Models\Customer */
    $isSuperUser = Auth::getUser()->isSuperUser();
?>

<?php $this->section( 'page-header-preamble' ) ?>
  Your <?= config('identity.sitename' ) ?> Dashboard
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $t->alerts() ?>


            <?= $this->insertIf( 'dashboard/dashboard-info' ) ?>


            <?php if( !$c->typeAssociate() ): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link <?php if( !$t->tab || $t->tab === 'overview' || $t->tab === 'index' ): ?>active<?php endif; ?>" data-toggle="tab" href="#overview">
                                  Overview
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?php if( $t->tab === 'details' ): ?>active<?php endif; ?>" data-toggle="tab" href="#details">
                                  Details
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?php if( $t->tab === 'ports' ): ?>active<?php endif; ?>" data-toggle="tab" href="#ports" data-toggle="tab">
                                  Ports
                                </a>
                            </li>

                            <?php if( $t->resellerMode() && $c->isReseller ): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php if( $t->tab === 'resold-customers' ): ?>active<?php endif; ?>" data-toggle="tab" href="#resold-customers" data-toggle="tab">
                                      Resold Customers
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if( $t->notes->count() ): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php if( $t->tab === 'notes' ): ?>active<?php endif; ?>" data-toggle="tab" href="#notes" id="tab-notes" data-toggle="tab">
                                        Notes
                                        <?php if( $t->notesInfo[ "unreadNotes"] > 0 ): ?>
                                          <span id="notes-unread-indicator" class="badge badge-success"><?= $t->notesInfo[ "unreadNotes"] ?></span>
                                        <?php endif ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a class="nav-link <?php if( $t->tab === 'cross-connect' ): ?>active<?php endif; ?>" data-toggle="tab" href="#cross-connects" data-toggle="tab">
                                  Cross Connects
                                </a>
                            </li>

                            <?php if( !$c->typeAssociate() && !$c->hasLeft() ): ?>
                                <?php if( $c->routeServerClient() ): ?>
                                    <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes' ) ): ?>
                                    <li class="nav-item" onclick="window.location.href = '<?= route( "rs-prefixes@view", [ 'cust' =>  $c->id ] ) ?>'">
                                        <a class="nav-link" data-toggle="tab"  href="">
                                            RS Prefixes &raquo;
                                        </a>
                                    </li>
                                    <?php endif ?>

                                    <?php if( !config( 'ixp_fe.frontend.disabled.filtered-prefixes' ) ): ?>
                                        <li class="nav-item" onclick="window.location.href = '<?= route( "filtered-prefixes@list", [ 'cust' =>  $c->id ] ) ?>'">
                                          <a class="nav-link" data-toggle="tab"  href="">
                                            Filtered Prefixes &raquo;
                                          </a>
                                        </li>
                                    <?php elseif( $c->irrdbFiltered() ): ?>
                                        <li class="nav-item" onclick="window.location.href = '<?= route( "irrdb@list", [ "cust" => $c->id, "type" => 'prefix', "protocol" => $c->isIPvXEnabled( 4 ) ? 4 : 6 ] ) ?>'">
                                          <a class="nav-link" data-toggle="tab"  href="">
                                            IRRDB Entries &raquo;
                                          </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif ?>

                            <?php endif ?>

                            <?php if( !config( 'ixp_fe.frontend.disabled.peering-manager' ) ): ?>
                                <li class="nav-item">
                                    <a class="nav-link" id="peering-manager-a" href="<?= route('peering-manager@index') ?>">
                                      Peering Manager &raquo;
                                    </a>
                                </li>
                            <?php endif ?>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= route( "statistics@member") ?>">
                                    Statistics &raquo;
                                </a>
                            </li>

                            <?php if( config( 'grapher.backends.sflow.enabled' )  ): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= route( "statistics@p2p" , [ "cust" => $c->id ] ) ?>">
                                        Peer to Peer Traffic &raquo;
                                    </a>
                                </li>
                            <?php endif ?>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <div id="overview" class="tab-pane fade <?php if( !$t->tab || $t->tab === 'overview' || $t->tab === 'index' ): ?> show active <?php endif; ?>">
                                <?= $t->insert( 'dashboard/dashboard-tabs/overview' ); ?>
                            </div>

                            <div id="details" class="tab-pane fade <?php if( $t->tab === 'details' ): ?> show active <?php endif; ?>">
                                <?= $t->insert( 'dashboard/dashboard-tabs/details' ); ?>
                            </div>

                            <div id="ports" class="tab-pane fade <?php if( $t->tab === 'ports' ): ?> show active <?php endif; ?>">
                                <?php if( $t->resellerMode() && $c->isReseller ): ?>
                                    <?= $t->insert( 'customer/overview-tabs/reseller-ports', [ 'isSuperUser' => $isSuperUser ] ); ?>
                                <?php else: ?>
                                    <?= $t->insert( 'customer/overview-tabs/ports', [ 'isSuperUser' => $isSuperUser ] ); ?>
                                <?php endif ?>
                            </div>

                            <?php if( $t->resellerMode() && $c->isReseller ): ?>
                                <div id="resold-customers" class="tab-pane fade <?php if( $t->tab === 'resold-customers' ): ?> show active <?php endif; ?>">
                                    <?= $t->insert( 'customer/overview-tabs/resold-customers', [ 'isSuperUser' => $isSuperUser ] ); ?>
                                </div>
                            <?php endif ?>

                            <?php if( $t->notes ): ?>
                                <div id="notes" class="tab-pane fade <?php if( $t->tab === 'notes' ): ?> show active <?php endif; ?> ">
                                    <?= $t->insert( 'customer/overview-tabs/notes', [ 'isSuperUser' => $isSuperUser ] ); ?>
                                </div>
                            <?php endif ?>

                            <div id="cross-connects" class="tab-pane fade <?php if( $t->tab === 'cross-connects' ): ?> show active <?php endif; ?>">
                                <?= $t->insert( 'customer/overview-tabs/cross-connects', [ 'isSuperUser' => $isSuperUser ] ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?= $t->insert( 'dashboard/dashboard-tabs/associate' ); ?>
            <?php endif; ?>
        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<?= $t->insert( 'customer/js/overview/notes', [ 'isSuperUser' => $isSuperUser ] ); ?>
  <script>
      $('.table-responsive-ixp').dataTable( {
          stateSave: true,
          stateDuration : DATATABLE_STATE_DURATION,
          responsive: true,
          ordering: false,
          searching: false,
          paging:   false,
          info:   false,
      } ).show();

      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          $($.fn.dataTable.tables(true)).DataTable()
              .columns.adjust()
              .responsive.recalc();
      });

      <?php if( Auth::getUser()->isCustUser() ): ?>
      $( document ).ready(function() {
          $( "#details input" ).attr( "disabled", "disabled" );
          $( "#details select" ).attr( "disabled", "disabled" )
      });
      <?php endif; ?>
  </script>
<?php $this->append() ?>

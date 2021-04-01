<?php
    $this->layout( 'layouts/ixpv4' );
    $isSuperUser = Auth::getUser()->isSuperUser();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if(  $isSuperUser  ): ?>
        <a href="<?= route( "customer@overview" , [ 'cust' => $t->customer->id ] ) ?>">
            <?= $t->customer->name ?>
        </a>
        /
    <?php endif; ?>
    IRRDB <?= $t->type === "asn" ? 'ASNs' : 'Prefixes' ?> IPv<?= $t->protocol ?> Entries (<?= count( $t->irrdbList ) ?>)
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
      <div class="btn-group btn-group-sm" role="group">
          <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              View / Update IRRDB Entries
          </button>

          <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
              <?php if( $t->customer->isIPvXEnabled( 4 ) ): ?>
                  <a class="dropdown-item <?= !request()->is( "irrdb/*/asn/4" ) ?: 'active' ?>" href="<?= route( "irrdb@list", [ "cust" => $t->customer->id, "type" => "asn", "protocol" => 4 ] ) ?>">IPv4 IRRDB ASNs</a>
                  <a class="dropdown-item <?= !request()->is( "irrdb/*/prefix/4" ) ?: 'active' ?>" href="<?= route( "irrdb@list", [ "cust" => $t->customer->id, "type" => "prefix", "protocol" => 4 ] ) ?>">IPv4 IRRDB Prefixes</a>
              <?php endif; ?>
              <?php if( $t->customer->isIPvXEnabled( 6 ) ): ?>
                  <a class="dropdown-item <?= !request()->is( "irrdb/*/asn/6" ) ?: 'active' ?>" href="<?= route( "irrdb@list", [ "cust" => $t->customer->id, "type" => "asn", "protocol" => 6 ] ) ?>">IPv6 IRRDB ASNs</a>
                  <a class="dropdown-item <?= !request()->is( "irrdb/*/prefix/6" ) ?: 'active' ?>" href="<?= route( "irrdb@list", [ "cust" => $t->customer->id, "type" => "prefix", "protocol" => 6 ] ) ?>">IPv6 IRRDB Prefixes</a>
              <?php endif; ?>
          </div>

          <a class="btn btn-primary" href="<?= route( "irrdb@update", [ "cust" => $t->customer->id, "type" => $t->type, "protocol" => $t->protocol ] ) ?>">
              Update <?= $t->type === "asn" ? 'ASNs' : 'Prefixes' ?>
          </a>
      </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
  <div class="row">
      <div class="col-md-12">
          <?= $t->alerts() ?>
          <?php if( $t->updatingIrrdb || $t->updatedIrrdb ): ?>
              <div class="alert alert-info" role="alert">
                  <?php if( $t->updatingIrrdb ): ?>
                      <p>
                          Our queue runner is updating the IRRDB entries for you.
                      </p>
                      <p>
                          Please wait a few moments and then <a href="<?= route( "irrdb@list", [ "cust" => $t->customer->id, "type" => $t->type, "protocol" => $t->protocol ] ) ?>">click
                              here to refresh the page</a>.
                      </p>
                  <?php else: ?>
                      The IRRDB update process has completed. <?= $t->updatedIrrdb[ "msg" ] ?>
                      <br/><br/>
                      There are a total of <?= $t->updatedIrrdb[ "v" . $t->protocol ][ "count" ] ?> IPv<?= $t->protocol ?> <?= $t->type === "asn" ? 'ASNs' : 'prefixes' ?>
                      of which <b><?= count( $t->updatedIrrdb[ "v" . $t->protocol ][ "new" ] ) ?> are new and have been added;
                      and <?= count( $t->updatedIrrdb[ "v" . $t->protocol ][ "stale" ] ) ?> are stale and have been removed</b>.

                      <?php if( count( $t->updatedIrrdb[ "v" . $t->protocol ][ "new" ] ) > 0): ?>
                          <br><br>
                          <h6>New/Added IPv<?= $t->protocol ?> <?= $t->type === "asn" ? 'ASNs' : 'Prefixes' ?></h6>
                          <ul>
                              <?php foreach( $t->updatedIrrdb[ "v" . $t->protocol ][ "new" ] as $new ): ?>
                                  <li> <?= $new ?> </li>
                              <?php endforeach; ?>
                          </ul>
                      <?php endif; ?>

                      <?php if( count( $t->updatedIrrdb[ "v" . $t->protocol ][ "stale" ] ) > 0): ?>
                          <br><br>
                          <h6>Stale/Removed IPv<?= $t->protocol ?> <?= $t->type === "asn" ? 'ASNs' : 'Prefixes' ?></h6>
                          <ul>
                              <?php foreach( $t->updatedIrrdb[ "v" . $t->protocol ][ "stale" ] as $stale ): ?>
                                  <li> <?= $stale[$t->type] ?> </li>
                              <?php endforeach; ?>
                          </ul>
                      <?php endif; ?>
                  <?php endif; ?>
              </div>
          <?php endif; ?>
          <table id='irrdb-list' class="table collapse table-striped table-responsive-ixp-with-header" width="100%">
              <thead class="thead-dark">
                  <tr>
                      <th>
                          <?= $t->type === "asn" ? 'ASN' : 'Prefix' ?>
                      </th>
                      <th>
                          First seen
                      </th>
                      <th>
                          Last seen
                      </th>
                  </tr>
              <thead>
              <tbody>
                  <?php foreach( $t->irrdbList as $irrdb ): ?>
                      <tr>
                          <td>
                              <?= $t->type === "asn" ? $t->asNumber( $irrdb[ "asn" ] ) : $t->whoisPrefix( $irrdb[ "prefix" ] ) ?>
                          </td>
                          <td>
                              <?= $irrdb[ "first_seen" ] ?? "" ?>
                          </td>
                          <td>
                              <?= $irrdb[ "last_seen" ] ?? "" ?>
                          </td>
                      </tr>
                  <?php endforeach;?>
              <tbody>
          </table>

          <?php if( $t->updatedIrrdb ): ?>
              <div class="alert alert-info tw-mt-16">
                  <p>
                      Note that we cache this information for 15 minutes. The above prefix(es) were found at
                      <?= $t->updatedIrrdb['found_at']->format('Y-m-d H:i:s') ?> and will not be refreshed until <?= $t->updatedIrrdb['found_at']->copy()->addMinutes(15)->format('H:i:s') ?>.

                  </p>
                  <?php if( $isSuperUser ): ?>
                      <p>
                          The network / database / processing effort was:
                          <?= $t->updatedIrrdb['netTime'] ?>s / <?= $t->updatedIrrdb['dbTime'] ?>s / <?= $t->updatedIrrdb['procTime'] ?>s.
                      </p>
                      <p>
                          <b>It looks like you're a super admin!</b>
                          You can <a href="<?= route( 'irrdb@update', [ 'cust' => $t->customer->id, 'type' => $t->type, 'protocol' => $t->protocol ] ) ?>?reset_cache=1">force a cache refresh by clicking here</a>.
                      </p>
                  <?php endif; ?>
              </div>
          <?php endif; ?>
      </div>
  </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $(document).ready( function() {
            $('.table-responsive-ixp-with-header').show();

            $('.table-responsive-ixp-with-header').DataTable( {
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive: true,
                paging: false,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 }
                ],
            } );
        });
    </script>
<?php $this->append() ?>
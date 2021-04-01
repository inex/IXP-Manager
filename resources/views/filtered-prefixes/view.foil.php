<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filtered Prefixes for <?= $t->cust->getFormattedName() ?>
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( $t->cust->routeServerClient() && $t->cust->irrdbFiltered() ): ?>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                View / Update IRRDB Entries
            </button>

            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <?php if( $t->cust->isIPvXEnabled( 4 ) ): ?>
                    <a class="dropdown-item" href="<?= route( "irrdb@list", [ "cust" => $t->cust->id, "type" => "asn", "protocol" => 4 ] ) ?>">IPv4 IRRDB ASNs</a>
                    <a class="dropdown-item" href="<?= route( "irrdb@list", [ "cust" => $t->cust->id, "type" => "prefix", "protocol" => 4 ] ) ?>">IPv4 IRRDB Prefixes</a>
                <?php endif; ?>
                <?php if( $t->cust->isIPvXEnabled( 6 ) ): ?>
                    <a class="dropdown-item" href="<?= route( "irrdb@list", [ "cust" => $t->cust->id, "type" => "asn", "protocol" => 6 ] ) ?>">IPv6 IRRDB ASNs</a>
                    <a class="dropdown-item" href="<?= route( "irrdb@list", [ "cust" => $t->cust->id, "type" => "prefix", "protocol" => 6 ] ) ?>">IPv6 IRRDB Prefixes</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-12">
            <?= $t->alerts() ?>
            <?php if( $t->filteredPrefixes === [] ): ?>
                <p>
                    <b>Good news!</b> We didn't find any filtered prefixes on any of your route server peering sessions.
                </p>
            <?php elseif( $t->filteredPrefixes === false ): ?>
                <p>
                    <b>Just give us a few seconds!</b>
                </p>
                <p>
                    Our queue runner is checking the appropriate route servers for you and gathering the required information.
                </p>
                <p>
                    Please wait a few moments and then <a href="<?= route( 'filtered-prefixes@list', [ 'cust' => $t->cust->id ] ) ?>">click here to refresh the page</a>.
                </p>
            <?php else: ?>
                <p>
                    <b>Bad news!</b> We found <?= count( $t->filteredPrefixes ) ?> prefix(es) that are currently being filtered.
                </p>

                <p class="tw-my-6">
                    These are listed below with the reason for the filtering and the route server where filtering has been applied.
                </p>

                <table class="table">
                    <thead class="thead-dark">
                        <th>Prefix</th>
                        <th>Filtered Because</th>
                        <th>Filtered On Router(s)</th>
                    </thead>
                    <tbody>
                        <?php $found_at = false;
                            foreach( $t->filteredPrefixes as $network => $detail ):
                                if( !$found_at ) { $found_at = $detail['found_at']; } ?>
                            <tr>
                                <td>
                                  <span class="tw-font-mono">
                                      <?= $t->whoisPrefix( $network ) ?>
                                  </span>
                                </td>
                                <td>
                                    <?php foreach( $detail['reasons'] as $r ): ?>
                                        <?php if( $lcinfo = $t->bird()->translateBgpFilteringLargeCommunity( substr( $r, strpos( $r, ':' ) ) ) ): ?>
                                            <span class="badge badge-<?= $lcinfo[1] ?>"><?= $lcinfo[0] ?></span>
                                        <?php else: ?>
                                            <?= $r ?>
                                        <?php endif; ?>
                                        <br>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php foreach( $detail['routers'] as $handle => $protocol ): ?>
                                        <a href="<?= route( 'lg::route-protocol', [ 'handle' => $handle, 'protocol' => $protocol ] ) ?>" target="_ixpm_lg">
                                            <span class="tw-inline-block tw-bg-grey-lighter tw-rounded-full tw-px-3 tw-py-1 tw-text-sm tw-mr-2 tw-my-1">
                                                <?= $handle ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="alert alert-info tw-text-xs tw-mt-16">
                    <p>
                        Note that we cache this information for 15 minutes. The above prefix(es) were found at
                        <?= $found_at->format('H:i:s') ?> and will not be refreshed until <?= $found_at->addMinutes(15)->format('H:i:s') ?>.
                    </p>
                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <p>
                            <b>Oh, wait, it looks like you're a super admin!</b>
                            You can <a href="<?= route( 'filtered-prefixes@list', [ 'cust' => $t->cust->id ] ) ?>?reset_cache=1">bust the cache
                            by clicking here</a>.
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>
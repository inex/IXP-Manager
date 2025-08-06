<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    IRRDB Summary
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/latest/features/irrdb/">
            Documentation
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

    <div class="alert alert-info mt-4 mb-4" role="alert">
        This page shows the last update times of each <?= config( 'ixp_fe.lang.customer.many' ) ?> IRRDB entries.
        Entries are considered stale if they have not been updated in the last 24 hours.
    </div>


    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>

            <table id="ixpDataTable" class="table table-striped table-bordered collapse" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th><?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?></th>
                        <th class="tw-text-center">V4 Prefixes</th>
                        <th class="tw-text-center">V6 Prefixes</th>
                        <th class="tw-text-center">V4 ASNs</th>
                        <th class="tw-text-center">V6 ASNs</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach( $t->customers_logs as $c ): ?>
                        <tr>
                            <td data-sort="<?= $c->abbreviatedName ?>">
                                <a href="<?= route( 'customer@overview', [ 'cust' => $c->id ] ) ?>">
                                    <?= $c->abbreviatedName ?> (AS<?= $c->autsys ?>)
                                </a>
                            </td>

                            <?php foreach( [ 'prefix_v4', 'prefix_v6', 'asn_v4', 'asn_v6' ] as $type ): ?>

                                <td class="tw-text-center"
                                        data-sort="<?= !$c->isIPvXEnabled( substr( $type, -1 ) ) ? -1000 : $c?->irrdbUpdateLog?->$type?->timestamp ?? 0 ?>">

                                    <?php if( !$c->isIPvXEnabled( substr( $type, -1 ) ) ):  ?>

                                        <a class="badge badge-secondary">n/a</a>

                                    <?php else: ?>

                                        <?php if( $c->irrdbUpdateLog === null || $c->irrdbUpdateLog->$type === null  ): ?>

                                            <a class="badge badge-danger"
                                               href="<?= route( 'irrdb@list', [
                                                        'cust' => $c,
                                                        'type' => str_starts_with( $type, 'prefix' ) ? 'prefix' : 'asn',
                                                        'protocol' => str_ends_with( $type, '4' ) ? '4' : '6',
                                               ] ) ?>"
                                                >NEVER</a>

                                        <?php else: ?>

                                            <span class="tw-tabular-nums" title="<?= $c->irrdbUpdateLog->$type->format('Y-m-d H:i') ?>">
                                                <?= $c->irrdbUpdateLog->$type->diffForHumans() ?>
                                            </span>

                                            <?php if( $c->irrdbUpdateLog->$type->isBefore( now()->subDay() ) ): ?>

                                                <a class="badge badge-warning"
                                                   href="<?= route( 'irrdb@list', [
                                                       'cust' => $c,
                                                       'type' => str_starts_with( $type, 'prefix' ) ? 'prefix' : 'asn',
                                                       'protocol' => str_ends_with( $type, '4' ) ? '4' : '6',
                                                   ] ) ?>"
                                                >STALE</a>

                                            <?php else: ?>

                                                <a href="<?= route( 'irrdb@list', [
                                                    'cust' => $c,
                                                    'type' => str_starts_with( $type, 'prefix' ) ? 'prefix' : 'asn',
                                                    'protocol' => str_ends_with( $type, '4' ) ? '4' : '6',
                                                ] ) ?>" <i class="ml-2 fa fa-arrow-circle-o-right"></i></a>

                                            <?php endif; ?>

                                        <?php endif; ?>

                                    <?php endif; ?>

                                </td>

                            <?php endforeach; ?>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'irrdb/js/summary' ); ?>
<?php $this->append() ?>
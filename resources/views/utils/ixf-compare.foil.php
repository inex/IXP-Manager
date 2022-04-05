<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    IX-F Compare
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

<?= $t->alerts() ?>

<?php if( $t->results ): ?>
    <p>
    The comparison results are shown below:
    </p>

    <h3><?= count( $t->results['aonly'] ) ?> Network(s) only at <?= request()->input( 'sourcea_dd' ) ?>:</h3>

    <table id="aonly" class="table table-striped table-responsive-ixp-with-header w-100">
        <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th>ASN</th>
                <th>Speed</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach( $t->results['aonly'] as $asn => $details ): ?>

                <tr>
                    <td><?= $details['name'] ?> <a class="tw-text-gray-500 tw-text-xs tw-border-1 tw-border-gray-500 tw-rounded-md tw-ml-4" target="_blank" href="https://www.peeringdb.com/asn/<?= $asn ?>">PDB</a></td>
                    <td><?= $t->asNumber( $asn ) ?></td>
                    <td><?= $t->scaleSpeed( $details['speed'] ) ?></td>
                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

    <br><br><br>
    <h3><?= count( $t->results['bonly'] ) ?> Network(s) only at <?= request()->input( 'sourceb_dd' ) ?>:</h3>

    <table id="bonly" class="table table-striped table-responsive-ixp-with-header w-100">
        <thead class="thead-dark">
        <tr>
            <th>Name</th>
            <th>ASN</th>
            <th>Speed</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach( $t->results['bonly'] as $asn => $details ): ?>

            <tr>
                <td><?= $details['name'] ?> <a class="tw-text-gray-500 tw-text-xs tw-border-1 tw-border-gray-500 tw-rounded-md tw-ml-4" target="_blank" href="https://www.peeringdb.com/asn/<?= $asn ?>">PDB</a></td></td>
                <td><?= $t->asNumber( $asn ) ?></td>
                <td><?= $t->scaleSpeed( $details['speed'] ) ?></td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <br><br><br>
    <h3><?= count( $t->results['shared'] ) ?> Network(s) at both <?= request()->input( 'sourcea_dd' ) ?> and <?= request()->input( 'sourceb_dd' ) ?>:</h3>

    <table id="shared" class="table table-striped table-responsive-ixp-with-header w-100">
        <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th>ASN</th>
                <th><?= request()->input( 'sourcea_dd' ) ?></th>
                <th><?= request()->input( 'sourceb_dd' ) ?></th>
            </tr>
        </thead>

        <tbody>

        <?php foreach( $t->results['shared'] as $asn => $details ): ?>

            <tr>
                <td><?= $details['name'] ?> <a class="tw-text-gray-500 tw-text-xs tw-border-1 tw-border-gray-500 tw-rounded-md tw-ml-4" target="_blank" href="https://www.peeringdb.com/asn/<?= $asn ?>">PDB</a></td></td>
                <td><?= $t->asNumber( $asn ) ?></td>
                <td><?= $t->scaleSpeed( $details['aspeed'] ) ?></td>
                <td><?= $t->scaleSpeed( $details['bspeed'] ) ?></td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <br><br><br>

<?php else: ?>
<p>
    This tool will allow you to compare members between IXPs that support the <a href="https://docs.ixpmanager.org/features/ixf-export/">IX-F Member Export</a> schema.
    The pre-populated list is currently taken from a static configuration file but we will make that more dynamic / configurable in a future release.
</p>
<?php endif; ?>


    <?= Former::open()
        ->method( 'POST' )
        ->action( route('utils/do-ixf-compare' ) )
        ->customInputWidthClass( 'col-sm-6' )
        ->addClass( 'col-md-10' )
        ->actionButtonsCustomClass( "grey-box");
    ?>



    <?= Former::select( 'sourcea_dd' )
        ->label( '1st Source' )
        ->options( $t->sources )
        ->placeholder( 'Choose the first source' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::select( 'sourceb_dd' )
        ->label( '2nd Source' )
        ->options( $t->sources )
        ->placeholder( 'Choose the second source' )
        ->addClass( 'chzn-select' );
    ?>


    <?=Former::actions( Former::primary_submit( 'Compare' )->id('btn-submit-form')->class( "mb-2 mb-sm-0") );?>

    <?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script type="text/javascript">
    </script>
<?php $this->append() ?>
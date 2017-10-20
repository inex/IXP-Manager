<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
<a href="<?= action($t->controller.'@list') ?>">
    <?=  $t->data[ 'feParams' ]->pagetitle  ?>
</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<li>
    Private VLAN Details
</li>
<?php $this->append() ?>

<?php $this->section('content') ?>
<?= $t->alerts() ?>
    <?php if( $t->params[ 'infra' ] ): ?>
        <div class="row-fluid">
            <div class="alert alert-info">
                Only showing
                VLANs for: <strong><?=  $t->params[ 'infra' ]->getName() ?></strong>.

                <div class="pull-right">
                    <div class="btn-group btn-group-xs" role="group">
                        <a href="<?= action( 'VlanController@list' ) ?>" class='btn btn-default'>Show All VLANs</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <table id="table-list" class="table">

        <thead>

            <tr>

                <th>
                    VLAN Name
                </th>


                <th>
                    Tag
                </th>

                <th>
                    Infrastructure
                </th>

                <th>
                    Members
                </th>

                <th>
                    Locations
                </th>

                <th>
                    Switches
                </th>

            </tr>

        </thead>

        <tbody>

            <?php foreach( $t->data[ 'data' ] as $idx => $row ): ?>
                <tr>

                    <td>
                        <?= $row[ 'name' ]?>
                    </td>

                    <td>
                        <?= $row[ 'number' ]?>
                    </td>

                    <td>
                        <?= $row[ 'infrastructure' ]?>
                    </td>

                    <td>
                        <?php foreach( $row[ "members" ] as $custid => $cust ): ?>
                            <a href="<?= url('' ) . 'customer/overview/id/' . $custid ?>"><?= $cust[ 'name' ] ?></a>
                            (<a href="">interface details</a>)<br />
                            <?php if( count( $row[ 'members'][ $custid ][ 'locations' ] ) > 1 ) : ?>
                                <?php for( $i=2; $i <= count( $row[ 'members'][ $custid ][ 'locations' ] ) ; $i++ ):  ?>
                                    <br />
                                <?php endfor; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach( $row[ 'members'] as $custid => $cust ): ?>
                            <?php foreach( $cust[ 'locations' ] as $l ): ?>
                                <?= $l ?><br />
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach( $row[ 'members'] as $custid => $cust ): ?>
                            <?php foreach( $cust[ 'switches' ] as $s ): ?>
                                <?= $s ?><br />
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </td>

                </tr>

            <?php endforeach; ?>
        </tbody>

    </table>




<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>





<?php $this->append() ?>

<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
<a href="<?= action($t->controller.'@list') ?>">
    <?=  $t->feParams->pagetitle  ?>
</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<li>
    Private VLAN Details
</li>
<?php $this->append() ?>

<?php $this->section('content') ?>
<?= $t->alerts() ?>
    <?php if( $t->data[ 'params'][ 'infra' ] ): ?>
        <div class="row-fluid">
            <div class="alert alert-info">
                Only showing
                VLANs for: <strong><?=  $t->data[ 'params'][ 'infra' ]->getName() ?></strong>.

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
                            <a href="<?= url('' ) . '/customer/overview/id/' . $custid ?>"><?= $cust[ 'name' ] ?></a>
                            (<a href=" <?= route( 'interfaces/virtual/edit', [ 'id' => $cust['viid'] ] ) ?>">interface details</a>)<br />
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach( $row[ 'locations'] as $locid => $locname ): ?>
                            <?= $locname ?><br />
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach( $row[ 'switches'] as $swid => $swname ): ?>
                            <?= $swname ?><br />
                        <?php endforeach; ?>
                    </td>

                </tr>

            <?php endforeach; ?>
        </tbody>

    </table>




<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>





<?php $this->append() ?>

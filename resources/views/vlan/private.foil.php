<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>

    <?=  $t->feParams->pagetitle  ?> / Private VLAN Details

<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <?php if( $t->data[ 'params'][ 'infra' ] ): ?>
            <div class="alert alert-info mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-info-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12 d-flex">
                        <div class=" mr-auto">
                            Only showing
                            VLANs for: <b><?=  $t->ee( $t->data[ 'params'][ 'infra' ]->getName() ) ?></b>.
                        </div>

                        <a href="<?= route( $t->feParams->route_prefix . '@list' ) ?>" class='btn btn-sm btn-outline-info'>
                            Show All VLANs
                        </a>


                    </div>
                </div>
            </div>
        <?php endif; ?>


        <table id="table-list" class="table table-striped" width="100%">
            <thead class="thead-dark">
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
                        Facilities
                    </th>

                    <th>
                        Switches
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $t->data[ 'rows' ] as $idx => $row ): ?>
                    <tr>

                        <td>
                            <?= $t->ee( $row[ 'name' ] ) ?>
                        </td>

                        <td>
                            <?= $t->ee( $row[ 'number' ] ) ?>
                        </td>

                        <td>
                            <?= $t->ee( $row[ 'infrastructure' ] ) ?>
                        </td>

                        <td>
                            <?php foreach( $row[ "members" ] as $custid => $cust ): ?>
                                <a href="<?= route( "customer@overview" , [ "id" => $custid ] ) ?>"><?= $t->ee( $cust[ 'name' ] ) ?></a>
                                (<a href=" <?= route( 'interfaces/virtual/edit', [ 'id' => $cust['viid'] ] ) ?>">interface details</a>)<br />
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php foreach( $row[ 'locations'] as $locid => $locname ): ?>
                                <?= $t->ee( $locname ) ?><br />
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php foreach( $row[ 'switches'] as $swid => $swname ): ?>
                                <?= $t->ee( $swname ) ?><br />
                            <?php endforeach; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>
            </tbody>

        </table>


    </div>

</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<script>

    $(document).ready( function() {
        $('#table-list').DataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
        } );

    });
</script>
<?php $this->append() ?>

<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Virtual Interfaces / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class=" btn-group btn-group-sm" role="group">
        <button class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route( 'interfaces/virtual/wizard' ) ?>" >
                Add Interface Wizard...
            </a>

            <a class="dropdown-item" href="<?= route( 'interfaces/virtual/add' ) ?>" >
                Add Virtual Interface Object Only...
            </a>
        </ul>
    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <table id='table-vi' class="collapse table table-stripped no-wrap" style="width: 100%!important">
            <thead class="thead-dark">
                <tr>
                    <th>
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                    </th>
                    <th>
                        Facility
                    </th>
                    <th>
                        Switch
                    </th>
                    <th>
                        Port(s)
                    </th>
                    <th>
                        Speed
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            <thead>
            <tbody>
                <?php foreach( $t->vis as $vi ):
                    /** @var Entities\VirtualInterface $vi */ ?>
                    <tr>
                        <td>
                            <a href="<?= route( "customer@overview" , [ "id" => $vi->getCustomer()->getId() ] ) ?>">
                                <?= $t->ee( $vi->getCustomer()->getName() ) ?>
                            </a>
                        </td>
                        <?php if( count( $vi->getPhysicalInterfaces() ) ): ?>
                            <td>
                                <?= $t->ee( $vi->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                            </td>
                            <td>
                                <?= $t->ee( $vi->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getName() ) ?>
                            </td>
                            <td>
                                <?php
                                $speed = 0;
                                foreach( $vi->getPhysicalInterfaces() as $pi ) {
                                    echo $t->ee( $pi->getSwitchPort()->getName() ) . "<br>";
                                    $speed += $pi->getSpeed();
                                }
                                ?>
                            </td>
                            <td data-order="<?= $speed ?>">
                                <?= $t->scaleBits( $speed*1000*1000, 0 ) ?>
                            </td>
                        <?php else: ?>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        <?php endif; ?>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-white" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vi->getId() ]) ?>" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <a class="btn btn btn-white" id="delete-vi-<?= $vi->getId() ?>" href="" <?php if( $t->resellerMode() && ( count( $vi->getPeeringPhysicalInterface()) > 0  || count( $vi->getFanoutPhysicalInterface() ) > 0 ) ) :?> data-related="1" <?php endif; ?> <?php if( $vi->getSwitchPort() ): ?> data-type="<?= $vi->getSwitchPort()->getType() ?>" <?php endif; ?> title="Delete Virtual Interface">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>


    </div>

</div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

    <?= $t->insert( 'interfaces/virtual/js/interface' ); ?>

    <script>

        $( document ).ready(function() {

           $('#table-vi').show();

           $('#table-vi').DataTable( {
               stateSave: true,
               stateDuration : DATATABLE_STATE_DURATION,
               responsive: true,
               columnDefs: [
                   { responsivePriority: 1, targets: 0 },
                   { responsivePriority: 2, targets: -1 },
                   { type: 'num', targets: [ 4 ] },
               ],
           } );
        });


        /**
         * on click even allow to delete a Virtual Interface
         */
        $(document).on('click', "a[id|='delete-vi']" ,function(e){
            e.preventDefault();
            let vi = (this.id).substring(10);
            deletePopup( vi , false, 'vi');
        });

    </script>
<?php $this->append() ?>


<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panels (<?= $t->active ? 'Active' : 'Inactive' ?> Only)
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a id="btn-filter-options" class="btn btn-white" href="<?= url()->current() ?>">
            Filter Options
        </a>
        <a class="btn btn-white" href="<?= route( $t->active ? "patch-panel/list/inactive" : 'patch-panel/list' ) ?>">
            Show <?= $t->active ? 'Inactive' : 'Active' ?>
        </a>
        <a class="btn btn-white" href="<?= route('patch-panel/add' ) ?>">
            <span class="fa fa-plus"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">

        <div class="col-md-12">

            <nav id="filter-row" class="collapse navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">

                <a class="navbar-brand" href="<?= route( "patch-panel/list" ) ?>">Graph Options:</a>

                <button class="navbar-toggler float-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex">

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <select id="adv-search-select-locations" name="location" class="form-control">
                                        <option value="all">All Facilities</option>
                                        <?php foreach( $t->locations as $i => $l ): ?>
                                            <option value="<?= $i ?>"><?= $l['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <select id="adv-search-select-cabinets" name="cabinet" class="form-control">
                                        <option value="all">All Racks</option>
                                    </select>

                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <select id="adv-search-select-types" name="type" class="form-control">
                                        <option value="all">All Types</option>
                                        <?php foreach( \Entities\PatchPanel::$CABLE_TYPES as $i => $type ): ?>
                                            <option value="<?= $i ?>"><?= $type ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label>
                                        <input name="available" value='true' id="available" class="mr-2" type="checkbox">
                                        Available for use
                                    </label>
                                </div>
                            </li>

                            <li class="nav-item">
                                <button type="submit" class="float-right btn btn-white">
                                    Filter Ports
                                </button>
                            </li>

                        </form>
                    </ul>
                </div>
            </nav>

            <?= $t->alerts() ?>

            <?php if( !count( $t->patchPanels ) && $t->active ): ?>
                <div class="alert alert-info" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-question-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <b>No active patch panels exist.</b> <a class="btn btn-white" href="<?= route( 'patch-panel/add' ) ?>">Add one...</a>
                        </div>
                    </div>
                </div>
            <?php else:  /* !count( $t->patchPanels ) */ ?>
                <table id='patch-panel-list' class="table collapse table-striped table-responsive-ixp-with-header" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Name
                            </th>
                            <th>
                                Rack
                            </th>
                            <th>
                                Colocation
                            </th>
                            <th>
                                Type
                            </th>
                            <th>
                                Ports Available
                            </th>
                            <th>
                                Installation Date
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    <thead>
                    <tbody>
                        <?php foreach( $t->patchPanels as $pp ):
                            /** @var Entities\PatchPanel $pp */
                            ?>
                            <tr>
                                <td>
                                    <a href="<?= route( 'patch-panel-port/list/patch-panel' , [ 'id' => $pp->getId() ] ) ?>">
                                        <?= $t->ee( $pp->getName() ) ?>
                                    </a>

                                </td>
                                <td>
                                    <a href="<?= route( 'rack@view', [ 'id' => $pp->getCabinet()->getId() ] ) ?>">
                                        <?= $t->ee( $pp->getCabinet()->getName() ) ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $t->ee( $pp->getColoReference() ) ?>
                                </td>
                                <td>
                                    <?= $pp->resolveCableType() ?> / <?= $pp->resolveConnectorType() ?>
                                </td>
                                <td>
                                    <span title="" class="badge badge-<?= $pp->getCssClassPortCount() ?>">
                                        <?php if( $pp->hasDuplexPort() ): ?>
                                            <?= $pp->getAvailableOnTotalPort( true ) ?>
                                        <?php else: ?>
                                            <?= $pp->getAvailableOnTotalPort( false ) ?>
                                        <?php endif; ?>
                                    </span>

                                    <?php if( $pp->hasDuplexPort() ): ?>
                                        &nbsp;
                                        <span class="badge badge-info">
                                            <?= $pp->getAvailableOnTotalPort( false ) ?>
                                        </span>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $pp->getInstallationDate() ? $pp->getInstallationDate()->format('Y-m-d') : 'Unknown' ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-white" href="<?= route( 'patch-panel@view' , [ 'id' =>  $pp->getId() ] ) ?>" title="Preview">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-white" href="<?= route( 'patch-panel/edit' , [ 'id' =>  $pp->getId() ] ) ?>" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        <?php if( $pp->getActive() ): ?>
                                            <a class="btn btn-white list-delete" id='list-delete-<?= $pp->getId() ?>' href="#" title="Make Inactive">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a class="btn btn-white" id='list-reactivate-<?= $pp->getId() ?>' href="<?= route( 'patch-panel@change-status' , [ 'id' => $pp->getId(), 'status' => ( $pp->getActive() ? '0' : '1' ) ] ) ?>" title="Reactive">
                                                <i class="fa fa-repeat"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a class="btn btn-white" href="<?= route ( 'patch-panel-port/list/patch-panel' , [ 'id' => $pp->getId() ] ) ?>" title="See Ports">
                                            <i class="fa fa-th"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <tbody>
                </table>
            <?php endif;  /* !count( $t->patchPanels ) */ ?>
        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<script>

    let locations = JSON.parse( '<?= json_encode( $t->locations ) ?>' );

    $(document).ready( function() {

        $('.table-responsive-ixp-with-header').show();

        $('.table-responsive-ixp-with-header').DataTable( {
            responsive: true,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
        } );



        $('#btn-filter-options').on( 'click', function( e ) {
            e.preventDefault();
            $('#filter-row').slideToggle();
        });

        $('#adv-search-select-locations').on( 'change', function( e ) {
            let opts = `<option value="all">All Racks</option>` ;

            if( $('#adv-search-select-locations').val() != 'all' ) {
                for ( let i in locations[ $( '#adv-search-select-locations' ).val() ][ 'cabinets' ] ) {
                    opts += `<option value='${i}'> ${ locations[ $( '#adv-search-select-locations' ).val() ][ 'cabinets' ][ i ][ 'name' ] }</option>`;
                }
            }

            $('#adv-search-select-cabinets').html( opts );
        });



        $( '#patch-panel-list' ).on( 'click', '.list-delete', function( event ) {

            event.preventDefault();
            let ppid = ( this.id ).substring( 12 );
            let url = "<?= route( 'patch-panel@change-status' , [ 'id' => $pp->getId(), 'status' => ( $pp->getActive() ? '0' : '1' ) ] ) ?>";

            console.log($(this).attr('href'));
            bootbox.dialog({
                message: 'Are you sure that you want to delete this Patch Panel ? It will become deactivated.',
                title: "Delete Patch Panel",
                buttons: {
                    cancel: {
                        label: 'Close',
                        className: 'btn-secondary',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    },
                    submit: {
                        label: 'Delete',
                        className: 'btn-danger',
                        callback: function () {
                            window.location = url;
                        }
                    },
                }
            });
        });
    });
</script>
<?php $this->append() ?>
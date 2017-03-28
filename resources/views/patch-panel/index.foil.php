<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    Patch Panels (<?= $t->active ? 'Active' : 'Inactive' ?> Only)
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a id="btn-filter-options" class="btn btn-default" href="<?= url()->current() ?>">
                Filter Options
            </a>
            <a class="btn btn-default" href="<?= url('patch-panel/list' . ( $t->active ? '/inactive' : '' ) ) ?>">
                Show <?= $t->active ? 'Inactive' : 'Active' ?>
            </a>
            <a type="button" class="btn btn-default" href="<?= url('patch-panel/add') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div id="filter-row" class="row" style="display: none">

        <div class="well pull-right">
            <form class="form-inline" method="post" action="<?= url('patch-panel-port/advanced-list' ) ?>">
                <div class="form-group">
                    <select id="adv-search-select-locations" name="location">
                        <option value="all">All Locations</option>
                        <?php foreach( $t->locations as $i => $l ): ?>
                            <option value="<?= $i ?>"><?= $l['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select id="adv-search-select-cabinets" name="cabinet">
                        <option value="all">All Cabinets</option>
                    </select>
                </div>
                <div class="form-group">
                    <select id="adv-search-select-types" name="type">
                        <option value="all">All Types</option>
                        <?php foreach( \Entities\PatchPanel::$CABLE_TYPES as $i => $type ): ?>
                            <option value="<?= $i ?>"><?= $type ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <button type="submit" class="btn btn-default">Filter Ports</button>
            </form>
        </div>

    </div>

    <?= $t->alerts() ?>
    <?php if( !count( $t->patchPanels ) && $t->active ): ?>
        <div class="alert alert-info" role="alert">
            <b>No active patch panels exist.</b> <a href="<?= url( 'patch-panel/add' ) ?>">Add one...</a>
        </div>
    <?php else:  /* !count( $t->patchPanels ) */ ?>
        <table id='patch-panel-list' class="table">
            <thead>
                <tr>
                    <td>
                        Name
                    </td>
                    <td>
                        Cabinet
                    </td>
                    <td>
                        Colocation
                    </td>
                    <td>
                        Type
                    </td>
                    <td>
                        Ports Available
                    </td>
                    <td>
                        Installation Date
                    </td>
                    <td>
                        Action
                    </td>
                </tr>
            <thead>
            <tbody>
                <?php foreach( $t->patchPanels as $pp ):
                    /** @var Entities\PatchPanel $pp */ ?>
                    <tr>
                        <td>
                            <a href="<?= url( '/patch-panel-port/list/patch-panel' ).'/'.$pp->getId()?>">
                                <?= $pp->getName() ?>
                            </a>

                        </td>
                        <td>
                            <a href="<?= url( '/cabinet/view/id' ).'/'.$pp->getCabinet()->getId()?>">
                                <?= $pp->getCabinet()->getName() ?>
                            </a>
                        </td>
                        <td>
                            <?= $pp->getColoReference() ?>
                        </td>
                        <td>
                            <?= $pp->resolveCableType() ?> / <?= $pp->resolveConnectorType() ?>
                        </td>
                        <td>
                            <span title="" class="label label-<?= $pp->getCssClassPortCount() ?>">
                                <?php if( $pp->hasDuplexPort() ): ?>
                                    <?= $pp->getAvailableOnTotalPort( true ) ?>
                                <?php else: ?>
                                    <?= $pp->getAvailableOnTotalPort( false ) ?>
                                <?php endif; ?>
                            </span>

                            <?php if( $pp->hasDuplexPort() ): ?>
                                &nbsp;
                                <span class="label label-info">
                                    <?= $pp->getAvailableOnTotalPort( false ) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $pp->getInstallationDateFormated() ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-default" href="<?= url( '/patch-panel/view' ).'/'.$pp->getId()?>" title="Preview">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                </a>
                                <a class="btn btn btn-default" href="<?= url( '/patch-panel/edit' ).'/'.$pp->getId()?>" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>

                                <?php if( $pp->getActive() ): ?>
                                    <a class="btn btn btn-default" id='list-delete-<?= $pp->getId() ?>' href="<?= url( 'patch-panel/change-status/' . $pp->getId()
                                            . '/' . ( $pp->getActive() ? '0' : '1' ) ) ?>" title="Make Inactive">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn btn-default" id='list-reactivate-<?= $pp->getId() ?>' href="<?= url( 'patch-panel/change-status/' . $pp->getId()
                                        . '/' . ( $pp->getActive() ? '0' : '1' ) ) ?>" title="Reactive">
                                        <i class="glyphicon glyphicon-repeat"></i>
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn btn-default" href="<?= url( '/patch-panel-port/list/patch-panel' ).'/'.$pp->getId()?>" title="See Ports">
                                    <i class="glyphicon glyphicon-th"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>
    <?php endif;  /* !count( $t->patchPanels ) */ ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<script>

    var locations = JSON.parse( '<?= json_encode( $t->locations ) ?>' );

    //console.log( locations[1]['cabinets'] );

    $(document).ready( function() {
        $( '#patch-panel-list' ).dataTable( {
            "autoWidth": false
        } );


        $('#btn-filter-options').on( 'click', function( e ) {
            e.preventDefault();
            $('#filter-row').slideToggle();
        });

        $('#adv-search-select-locations').on( 'change', function( e ) {
            var opts = '<option value="all">All Cabinets</option>';

            if( $('#adv-search-select-locations').val() != 'all' ) {
                for (var i in locations[$('#adv-search-select-locations').val()]['cabinets']) {
                    opts += '<option value="' + i
                        + '">' + locations[$('#adv-search-select-locations').val()]['cabinets'][i]['name'] + '</option>';
                }
            }

            $('#adv-search-select-cabinets').html( opts );
        });
    });
</script>
<?php $this->append() ?>
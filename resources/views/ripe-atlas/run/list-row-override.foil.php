<?php
    // due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
    // see http://www.foilphp.it/docs/DATA/PASS-DATA.html
use IXP\Models\Router;

$row = $t->row;
?>

<tr>
    <td>
        <a href="<?= route( 'vlan@view', [ 'id' => $row[ 'vlan_id' ] ] ) ?>">
            <?= $t->ee( $row[ 'vlan_name' ] )?>
        </a>
    </td>
    <td>
        <?= Router::$PROTOCOLS[ $row[ 'protocol' ] ] ?>
    </td>
    <td>
        <span class='badge badge-secondary' data-toggle='tooltip' data-placement='top' title='Number of ATLAS measurements created'>
            <?= $row[ 'nb_am_created' ] ?> / <?= $row[ 'nb_am' ] ?>
        </span>

        <span class='badge badge-secondary' data-toggle='tooltip' data-placement='top' title='Number of ATLAS measurements started'>
            <?= $row[ 'nb_am_started' ] ?> / <?= $row[ 'nb_am' ] ?>
        </span>

        <span class='badge badge-secondary' data-toggle='tooltip' data-placement='top' title='Number of ATLAS measurements completed'>
            <?= $row[ 'nb_am_stopped' ] ?> / <?= $row[ 'nb_am' ] ?>
        </span>
    </td>

    <td>
        <?= Carbon\Carbon::parse( $row[ 'created_at' ] ) ?>
    </td>

    <td>
        <?= $row[ 'scheduled_at' ] ? Carbon\Carbon::parse( $row['scheduled_at'] ) : '' ?>
    </td>

    <td>
        <?php if( $row[ 'started_at' ] && $t->row[ 'nb_am_created' ] !== 0 ): ?>
            <?= Carbon\Carbon::parse( $row['started_at'] ) ?>
        <?php else: ?>
            <span class="badge badge-info">
                Waiting for run Action
            </span>
        <?php endif; ?>
    </td>

    <td>
        <?php if( $row[ 'completed_at' ] ): ?>
            <?= Carbon\Carbon::parse( $row['completed_at'] ) ?>
        <?php else: ?>
            <span class="badge badge-info">
                Running
            </span>
        <?php endif; ?>
    </td>

    <td>
        <div class="btn-group btn-group-sm">
            <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview">
                <i class="fa fa-eye"></i>
            </a>

            <a class="btn btn-white <?= $t->row[ 'nb_am' ] !== 0 ?: 'disabled' ?>"  href="<?= route('ripe-atlas/measurements@list' , [ 'atlasrun' => $t->row[ 'id' ] ] ) ?>" title="Show atlas measurements">
                <i class="fa fa-th-list"></i>
            </a>

            <a class="btn btn-white"  href="<?= route('ripe-atlas/measurements@matrix' , [ 'atlasRun' => $t->row[ 'id' ] ] ) ?>" title="Show atlas measurements matrix">
                <i class="fa fa-file-text"></i>
            </a>

            <a class="btn btn-white btn-2f-list-delete <?= $t->row[ 'completed_at' ] ?: 'disabled'  ?>" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="<?= route( $t->feParams->route_prefix.'@delete' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Delete">
                <i class="fa fa-trash"></i>
            </a>

            <button id="d2f-more-options" type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item atlas-run-action <?= ( $t->row[ 'completed_at' ] || $t->row[ 'started_at' ] ) && $t->row[ 'nb_am_created' ] !== 0 ? 'disabled' : ''  ?>" name="atlas-run-measurements-run"
                   href="<?= route( 'ripe-atlas/measurements@run-measurements' , [ 'atlasrun' => $t->row[ 'id' ] ] ) ?>" title='Run atlas measurements' data-method='post' >
                    Run Measurements
                </a>
                <a class="dropdown-item atlas-run-action <?= $t->row[ 'completed_at' ] || ( $t->row[ 'nb_am_created' ] === $t->row[ 'nb_am_stopped' ] && $t->row[ 'nb_am_created' ] !== 0  )? 'disabled' : ''  ?>" name="atlas-run-measurements-update" data-method='put'
                   href="<?= route('ripe-atlas/measurements@update-measurements', [ 'atlasrun' => $t->row[ 'id' ] ] ) ?>" title='Update atlas measurements' >
                    Update Measurements
                </a>
                <a class="dropdown-item atlas-run-action" name="atlas-run-measurements-stop" href="<?= route( "ripe-atlas/measurements@stop-measurements" , [ 'atlasrun' => $t->row[ 'id' ] ] ) ?> "
                   data-method='put' title='Stop all atlas measurements' >
                    Stop All Measurements
                </a>
                <a class="dropdown-item atlas-run-action <?= !$t->row[ 'completed_at' ] ?: 'disabled'  ?>" name="atlas-run-request-complete" href="<?= route( $t->feParams->route_prefix . "@complete-run", [ 'atlasrun' => $t->row[ 'id' ] ] ) ?>"
                   data-method='put' title='Complete atlas measurements'>
                    Complete Request
                </a>
            </ul>
        </div>
    </td>
</tr>
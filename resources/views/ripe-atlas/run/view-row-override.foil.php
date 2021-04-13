<?php
    // due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
    // see http://www.foilphp.it/docs/DATA/PASS-DATA.html
    $row = $t->data[ 'item' ];
?>
    <tr>
        <th>
            Vlan
        </th>
        <td>
            <a href="<?= route( 'vlan@view', [ 'id' => $row[ 'vlan_id' ] ] ) ?>">
                <?= $t->ee( $row[ 'vlan_name' ] )?>
            </a>
        </td>
    </tr>
    <tr>
        <th>
            Protocol
        </th>
        <td>
            <?= \IXP\Models\Router::$PROTOCOLS[ $row[ 'protocol' ] ] ?>
        </td>
    </tr>
    <tr>
        <th>
            Total
        </th>
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
    </tr>
    <tr>
        <th>
            Created At
        </th>
        <td>
            <?= $row[ 'created_at' ] ? Carbon\Carbon::parse( $row[ 'created_at' ] ) : '' ?>
        </td>
    </tr>
    <tr>
        <th>
            Scheduled At
        </th>
        <td>
            <?= $row[ 'scheduled_at' ] ? Carbon\Carbon::parse( $row[ 'scheduled_at' ] ) : '' ?>
        </td>
    </tr>
    <tr>
        <th>
            Started At
        </th>
        <td>
            <?php if( $row[ 'started_at' ] ): ?>
                <?= Carbon\Carbon::parse( $row['started_at'] ) ?>
            <?php else: ?>
                <span class="badge badge-info">
                    Waiting for run Action
                </span>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th>
            Completed At
        </th>
        <td>
            <?php if( $row[ 'completed_at' ] ): ?>
                <?= Carbon\Carbon::parse( $row['completed_at'] ) ?>
            <?php else: ?>
                <span class="badge badge-info">
                Running
            </span>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th>
            Updated At
        </th>
        <td>
            <?= $row[ 'updated_at' ] ? Carbon\Carbon::parse( $row[ 'updated_at' ] ) : '' ?>
        </td>
    </tr>

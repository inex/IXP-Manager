<li class="pull-right">

    <div class="btn-group btn-group-xs" role="group">

        <?php if( isset( $t->data[ 'params'][ "switch" ] ) ): ?>

            <?php if( $t->data[ 'params'][ "switch" ] ): ?>

                <!-- Single button -->
                <div class="btn-group">

                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php if( $t->action ==  'listOpStatus' ): ?>

                            Live Port States (with SNMP poll)

                        <?php elseif( $t->action == 'list' ): ?>

                            Ports (database only)

                        <?php elseif( $t->action == 'snmpPoll' ): ?>

                            Ports (with SNMP poll)

                        <?php elseif( $t->action == 'listMau' ): ?>

                            Port MAU Detail (database only)

                        <?php else: ?>

                            Unknown action?

                        <?php endif; ?>

                        <span class="caret"></span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                        <li <?php if( $t->action == 'listOpStatus' ): ?> class="active"<?php endif; ?>>
                            <a href="<?= route( "switch-port@list-op-status", [ "switch" => $t->data[ 'params'][ "switch" ] ] ) ?>">View Live Port States (with SNMP poll)</a>
                        </li>

                        <li <?php if( $t->action == 'list'): ?>class="active"<?php endif; ?>>
                            <a href="<?= route( "switch-port@list", [ "switch" => $t->data[ 'params'][ "switch" ] ] ) ?>">View / Edit Ports (database only)</a>
                        </li>

                        <li <?php if( $t->action == 'snmpPoll'): ?>class="active"<?php endif; ?>>
                            <a href="<?= route( "switch-port@snmp-poll", [ "switch" => $t->data[ 'params'][ "switch" ] ] ) ?>">View / Edit Ports (with SNMP poll)</a>
                        </li>

                        <?php if( $t->action == 'listMau' ): ?>
                            <li <?php if( $t->action == 'listMau'): ?>class="active"<?php endif; ?>>
                                <a href="<?= route( "switch-port@list-mau", [ "switch" => $t->data[ 'params'][ "switch" ] ] ) ?>">View / Edit Ports (with SNMP poll)</a>
                            </li>
                        <?php endif; ?>

                    </ul>

                </div>

            <?php endif; ?>

            <!-- Single button -->
            <div class="btn-group">

                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->data[ 'params'][ "switch" ] ? $t->data[ 'params'][ "switches" ][ $t->data[ 'params'][ "switch" ] ] : "All Switch" ?><span class="caret"></span>
                </button>

                <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                    <li class="<?= $t->data[ 'params'][ "switch" ] ? "" : "active" ?>">
                        <a href="<?= route( "switch-port@list" , [ "switch" => 0 ] ) ?>">All Switches</a>
                    </li>

                    <li role="separator" class="divider"></li>

                    <?php foreach( $t->data[ 'params'][ "switches" ] as $id => $name ): ?>

                        <li class="<?= isset( $t->data[ 'params'][ "switch" ]) && $t->data[ 'params'][ "switch" ] === $id ? 'active' : '' ?>">
                            <a href="<?= route( $t->feParams->route_prefix . "@" . $t->feParams->route_action, [ "switch" => $id ] ) ?>"><?= $name ?></a>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

            <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@add' ) ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

        <?php endif; ?>

    </div>

</li>

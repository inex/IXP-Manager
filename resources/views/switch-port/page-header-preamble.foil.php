<li class="pull-right">

    <div class="btn-group btn-group-xs" role="group">

        <?php if( isset( $t->data[ 'params'][ "switch" ] ) ): ?>

            <?php if( $t->data[ 'params'][ "switch" ] ):

                /** @var Entities\Switcher $s */
                $s = D2EM::getRepository( Entities\Switcher::class )->find( $t->data[ 'params'][ "switch" ] );
            ?>

                <!-- Single button -->
                <div class="btn-group">

                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php if( request()->is( 'switch-port/op-status/*' ) ): ?>

                            Live Port States (via SNMP poll)

                        <?php elseif( request()->is( 'switch-port/snmp-poll/*' ) ): ?>

                            View/Edit Ports (via SNMP poll)

                        <?php elseif( request()->is( 'switch-port/list' ) ): ?>

                            View/Edit Ports (database only)

                        <?php elseif( request()->is( 'switch-port/list-mau/*' ) ): ?>

                            Port MAU Detail (database only)

                        <?php else: ?>

                            Unknown action?

                        <?php endif; ?>

                        &nbsp;
                        <span class="caret"></span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                        <li class="dropdown-header">SNMP Actions</li>

                        <li class="<?= !request()->is( 'switch-port/op-status/*' ) ?: 'active' ?> <?= $s->getActive() ?: 'a-disabled' ?> " >
                            <a href="<?= route( "switch-port@list-op-status", [ "switch" => $s->getId() ] ) ?>">Live Port States</a>
                        </li>

                        <li class="<?= !request()->is( 'switch-port/snmp-poll/*' ) ?: 'active' ?> <?= $s->getActive() ?: 'a-disabled' ?> " >
                            <a href="<?= route( "switch-port@snmp-poll", [ "switch" => $s->getId() ] ) ?>">View / Edit Ports</a>
                        </li>

                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Database Actions</li>

                        <li class="<?= !request()->is( 'switch-port/list' ) ?: 'active' ?>" >
                            <a href="<?= route( "switch-port@list", [ "switch" => $s->getId() ] ) ?>">View / Edit Ports</a>
                        </li>

                        <?php if( $s->getMauSupported() ): ?>
                            <li class="<?= !request()->is( 'switch-port/list-mau/*' ) ?: 'active' ?>">
                                <a href="<?= route( "switch-port@list-mau", [ "switch" => $s->getId() ] ) ?>">Port MAU Detail</a>
                            </li>
                        <?php endif; ?>

                    </ul>

                </div>


            <?php endif; ?>

            <!-- Single button -->
            <div class="btn-group">

                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->data[ 'params'][ "switch" ] ? $t->data[ 'params'][ "switches" ][ $t->data[ 'params'][ "switch" ] ] : "All Switches" ?>
                    &nbsp;
                    <span class="caret"></span>
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

            <a id="add-switch-port" type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@add' ) ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

        <?php endif; ?>

    </div>

</li>

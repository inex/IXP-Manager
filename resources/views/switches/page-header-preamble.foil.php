<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">

            <!-- Single button -->
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= isset($t->data[ 'params'][ "switchType" ]) && isset( Entities\Switcher::$TYPES[ $t->data[ 'params'][ "switchType" ] ] ) ? Entities\Switcher::$TYPES[ $t->data[ 'params'][ "switchType" ] ] : "All Type" ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li class="<?= ( isset( $t->data[ 'params'][ "switchType" ] ) &&  $t->data[ 'params'][ "switchType" ] == 0 ) || $t->data[ 'params'][ "switchType" ] == null ? "active" : "" ?>">
                        <a href="<?= route( $t->feParams->route_prefix."@list", [ "type" => 0 ] ) ?>">All type</a>
                    </li>

                    <li role="separator" class="divider"></li>
                    <?php foreach( Entities\Switcher::$TYPES as $id => $type ): ?>
                        <li class="<?= isset($t->data[ 'params'][ "switchType" ]) && $t->data[ 'params'][ "switchType" ] == $id ? 'active' : '' ?>">
                            <a  href="<?= route( $t->feParams->route_prefix."@list" , [ "type" => $id ] ) ?>"><?= $type  ?></a>
                        </li>
                    <?php endforeach; ?>

                </ul>

                <?php if( isset($t->data[ 'params'][ "activeOnly" ] ) && $t->data[ 'params'][ "activeOnly" ] ): ?>
                    <a class="btn btn-default btn-xs" href="<?= route( $t->feParams->route_prefix."@list" , [ "active-only" => 0 ] ) ?>">
                        Include Inactive
                    </a>
                <?php else: ?>
                    <a class="btn btn-default btn-xs" href="<?= route( $t->feParams->route_prefix."@list" , [ "active-only" => 1 ] ) ?>">
                        Show Active
                    </a>
                <?php endif; ?>

                <?php if( isset($t->data[ 'params'][ "osView" ] ) && $t->data[ 'params'][ "osView" ] == true ): ?>
                    <a class="btn btn-default btn-xs" href="<?= route($t->feParams->route_prefix.'@list', [ "os-view" => false ] ) ?>">Standard View</a>
                <?php else: ?>
                    <a class="btn btn-default btn-xs" href="<?= route($t->feParams->route_prefix.'@list' , [ "os-view" => true ] ) ?>">OS View</a>
                <?php endif; ?>


                <a type="button" class="btn btn-default btn-xs"  target="_blank" href="https://github.com/inex/IXP-Manager/wiki/Switch-and-Switch-Port-Management">
                    Help
                </a>

            </div>

            <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@add-by-snmp-step-1') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

        </div>
    </li>
<?php endif;?>
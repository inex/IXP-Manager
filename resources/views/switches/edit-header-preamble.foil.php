<li class="pull-right">

    <div class="btn-group btn-group-xs" role="group">

        <div class="btn-group">

            <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>

                <a type="button" class="btn btn-default btn-xs" href="<?= route($t->feParams->route_prefix.'@list') ?>">
                    <span class="glyphicon glyphicon-th-list"></span>
                </a>

            <?php endif; ?>

            <a type="button" title="add SNMP" class="btn btn-default btn-xs" href="<?= route($t->feParams->route_prefix.'@add-by-snmp') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

        </div>

    </div>

</li>
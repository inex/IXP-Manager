<div class="tab-content well">
    <?php foreach ( $t->tabRouters as $infra => $info ): ?>
        <?php $formatedName = Str::kebab( strtolower( $infra ) ) ?>
        <div class="tab-pane <?= !($infra === array_key_first( $t->tabRouters ) ) ?: 'active show'?>" id="<?= $t->ee( $formatedName , "attr" ) ?>">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills card-header-pills">
                        <?php foreach( $info as $protocol => $routers ): ?>
                            <li class="nav-item" >
                                <a class="nav-link <?= !($protocol === array_key_first( $info ) ) ?: 'active'?>" data-toggle="pill" href="#<?= $t->ee( $formatedName . '-' . $protocol , "attr" ) ?>">
                                    IPv<?= $t->ee( $protocol ) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <?php foreach( $info as $protocol => $routers ): ?>
                            <div id="<?= $t->ee( $formatedName . '-' . $protocol , "attr" ) ?>" class="tab-pane <?= !($protocol === array_key_first( $info ) ) ?: 'active show'?>">
                                <table class="table table-striped hover table-router">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>
                                                Router
                                            </th>
                                            <th>
                                                Config Last Updated
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ( $routers as $router ): ?>
                                            <tr data-href="<?= route('lg::bgp-sum', [ 'handle' => $t->ee( $router[ 'handle' ] ) ] ) ?>">
                                                <td class="align-middle">
                                                    <?= $t->ee( $router[ 'name' ] ) ?>
                                                </td>
                                                <td class="align-middle">
                                                    <?= $router[ 'updated_at' ] ? $router[ 'updated_at' ]->format( "Y-m-d H:i:s" ) : '(unknown)' ?>
                                                </td>
                                                <td>
                                                    <a class="btn btn-primary" href="<?= route('lg::bgp-sum', [ 'handle' => $t->ee( $router[ 'handle' ] ) ] ) ?>">
                                                        Looking Glass
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
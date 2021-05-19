<div class="tab-content well">
    <?php foreach ( $t->tabRouters as $infra => $info ): ?>
        <?php $formatedName = Str::kebab( strtolower( $t->ee( $infra ) ) ) ?>
        <div class="tab-pane <?= !($infra === array_key_first( $t->tabRouters ) ) ?: 'active show'?>" id="<?= $formatedName ?>">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills card-header-pills">
                        <?php foreach( $info as $protocol => $routers ): ?>
                            <li class="nav-item" >
                                <a class="nav-link <?= !($protocol === array_key_first( $info ) ) ?: 'active'?>" data-toggle="pill" href="#<?= $formatedName . '-' . $protocol ?>">
                                    IPv<?= $protocol ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <?php foreach( $info as $protocol => $routers ): ?>
                            <div id="<?= $formatedName . '-' . $protocol ?>" class="tab-pane <?= !($protocol === array_key_first( $info ) ) ?: 'active show'?>">
                                <table class="table table-striped hover table-router">
                                    <thead class="table-dark">
                                        <th>
                                            Router
                                        </th>
                                        <th>
                                            Config Last Updated
                                        </th>
                                        <th></th>
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
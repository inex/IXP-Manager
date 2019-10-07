<div class="tab-content">
    <?php foreach ( $t->tabRouters as $infra => $info ): ?>
        <?php $formatedName = Str::kebab( strtolower( $t->ee( $infra ) ) ) ?>
        <div id="<?= $formatedName ?>" class="tab-pane fade <?= !($infra === array_key_first( $t->tabRouters ) ) ?: 'active show'?>">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills card-header-pills">

                        <?php foreach( $info as $protocol => $routers ): ?>
                            <li class="nav-item" id="peering-potential-li" role="<?= $formatedName . '-' . $protocol ?>" >
                                <a class="nav-link <?= !($protocol === array_key_first( $info ) ) ?: 'active'?>" data-toggle="tab" href="#<?= $formatedName . '-' . $protocol ?>">IPv<?= $protocol ?></a>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <?php foreach( $info as $protocol => $routers ): ?>
                            <div id="<?= $formatedName . '-' . $protocol ?>" class="tab-pane fade <?= !($protocol === array_key_first( $info ) ) ?: 'active show'?>">

                                <table class="table table-striped">
                                    <thead class="table-dark">
                                    <th>
                                        Router
                                    </th>
                                    <th>
                                        Last Updated
                                    </th>
                                    <th></th>
                                    </thead>
                                    <tbody>
                                    <?php foreach ( $routers as $router ): ?>
                                        <tr>
                                            <td>
                                                <?= $t->ee( $router[ 'name' ] ) ?>
                                            </td>
                                            <td>
                                                <?= $router[ 'last-updated' ]->format( "Y-m-d H:i:s" ) ?>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary" href="<?= url('/lg/' . $t->ee( $router[ 'handle' ] ) ) ?>">Looking Glass</a>
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
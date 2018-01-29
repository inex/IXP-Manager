<div class="col-sm-12">
    <br>
    <table class="table">
        <thead>
        <tr>
            <th>Description</th>
            <th>Location</th>
            <th>Console Server</th>
            <th>Port</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach( $t->c->getConsoleServerConnections() as $c ): ?>
                <tr>
                    <td>
                        <?= $t->ee( $c->getDescription() ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $c->getSwitcher()->getCabinet()->getLocation()->getName() )?>
                    </td>
                    <td>
                        <?= $t->ee( $c->getSwitcher()->getName() )?>
                    </td>
                    <td>
                        <?= $t->ee( $c->getPort() ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


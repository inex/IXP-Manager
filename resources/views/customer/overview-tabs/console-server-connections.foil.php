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
            <?php foreach( $t->c->getConsoleServerConnections() as $csc ): ?>
                <tr>
                    <td>
                        <?= $t->ee( $csc->getDescription() ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $csc->getSwitcher()->getCabinet()->getLocation()->getName() )?>
                    </td>
                    <td>
                        <?= $t->ee( $csc->getSwitcher()->getName() )?>
                    </td>
                    <td>
                        <?= $t->ee( $csc->getPort() ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


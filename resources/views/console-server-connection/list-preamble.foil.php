<?php if( isset($t->data[ 'params'][ "cs" ]) ): ?>
    <div>
        <h2>
            Ports for Console Server: <?= $t->data[ 'params'][ "css" ][$t->data[ 'params'][ "cs" ] ][ 'name' ] ?><br><br>
        </h2>
    </div>
<?php endif;?>
<div class="btn-group btn-group-sm">
    <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?=  $t->data[ 'params'][ 'model' ] ?: "Limit to model..." ?>
    </button>
    <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">
        <a class="dropdown-item <?= $t->data[ 'params'][ 'model' ] ?: "active" ?>" href="<?= route( 'log@list' ) ?>">
            All Models
        </a>

        <div class="dropdown-divider"></div>
        <?php foreach( $t->data[ 'params'][ 'models' ] as $model ): ?>
            <a class="dropdown-item <?= $t->data[ 'params'][ 'model' ] !== $model ?: "active" ?>" href="<?= route( 'log@list', [ 'model' => $model ] ) ?>">
                <?= $model ?>
            </a>
        <?php endforeach; ?>
    </ul>
</div>

<div class="btn-group btn-group-sm">
    <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?=  $t->data[ 'params'][ 'user' ] ?: "Limit to user..." ?>
    </button>
    <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">
        <a class="dropdown-item <?= $t->data[ 'params'][ 'user' ] ?: "active" ?>" href="<?= route( 'log@list' ) ?>">
            All Users
        </a>

        <div class="dropdown-divider"></div>
        <?php foreach( $t->data[ 'params'][ 'users' ] as  $user ): ?>
            <a class="dropdown-item <?= $t->data[ 'params'][ 'user' ] !== $user ?: "active" ?>" href="<?= route( 'log@list', [ 'user' => $user ] ) ?>">
                <?= $user ?>
            </a>
        <?php endforeach; ?>
    </ul>
</div>
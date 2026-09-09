<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle center-dd-caret d-flex" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?= __( 'Documentation' ) ?>
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="<?= route( 'public-content', [ 'page' => 'example' ] ) ?>"><?= __( 'Example Page' ) ?></a>
        <div class="dropdown-divider"></div>
        <h6 class="dropdown-header"><?= __( 'Official IXP Manager Sites' ) ?></h6>
        <a class="dropdown-item" href="https://www.ixpmanager.org/" target="_blank"><?= __( 'Homepage' ) ?></a>
        <a class="dropdown-item" href="https://docs.ixpmanager.org/latest/" target="_blank"><?= __( 'Documentation' ) ?></a>
    </div>
</li>
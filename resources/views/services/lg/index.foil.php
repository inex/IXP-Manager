<?php $this->layout('services/lg/layout') ?>

<?php $this->section('title') ?>
    <small>BGP Protocol Summary</small>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?php if( count( $t->tabRouters ) === 1 ): ?>
        <?= $t->insert('services/lg/router-tab' ) ?>
    <?php else: ?>
        <div class="card mt-4">
            <div class="card-header">
                <ul class="nav nav-tabs">
                    <?php foreach ( $t->tabRouters as $infra => $router): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= !($infra === array_key_first( $t->tabRouters ) ) ?: 'active'?>" href="#<?= Str::kebab( strtolower( $infra ) ) ?>" data-toggle="tab">
                                <?=  $t->ee( $infra ) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="card-body">
                <?= $t->insert('services/lg/router-tab' ) ?>
            </div>
        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $(".table-router tbody tr").click(function() {
            window.document.location = $( this ).data( "href");
        });

        $( document ).on( 'mouseenter', '[data-toggle="tab"], [data-toggle="pill"]', function () {
            $( this ).tab( 'show' );
        });
    </script>
<?php $this->append() ?>
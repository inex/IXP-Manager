<?php $this->layout('services/lg/layout') ?>

<?php $this->section('title') ?>
    <small>BGP Protocol Summary</small>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?php if( count( $t->tabRouters ) == 1 ): ?>

    <?= $t->insert('services/lg/router-tab' ) ?>

<?php else: ?>
    <div class="card mt-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="infra-tab">
                <?php foreach ( $t->tabRouters as $infra => $router): ?>
                    <li role="<?= Str::kebab( strtolower( $infra ) ) ?>" class="nav-item">
                        <a class="nav-link <?= !($infra === array_key_first( $t->tabRouters ) ) ?: 'active'?>" data-toggle="tab" href="#<?= Str::kebab( strtolower( $infra ) ) ?>">
                            <?= $infra ?>
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
    $(document).ready(function(){

        $('.nav-link').hover( function (e) {
            e.preventDefault();
            $(this).tab('show')
        })

    });
</script>
<?php $this->append() ?>
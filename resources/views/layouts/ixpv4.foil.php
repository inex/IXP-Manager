<!DOCTYPE html>
<html lang="en">

<head>

    <!--  IXP MANAGER - template directory: resources/[views|skins] -->

    <base href="<?= url('') ?>/index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />
    <meta charset="utf-8">

    <title><?= config('identity.sitename' ) ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="<?=  csrf_token() ?>">

    <link rel="stylesheet" type="text/css" href="<?= asset('bower_components/bootstrap/dist/css/bootstrap.min.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= asset('bower_components/select2/dist/css/select2.min.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= asset('css/ixp-manager.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= asset('css/draganddrop.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= asset('css/font-awesome.min.css') ?>" />


    <?php $this->section('headers') ?>
    <?php $this->stop() ?>


    <?php if( !Auth::check() || !Auth::user()->isSuperUser() ):
        // and ( not isset( $mode ) or $mode neq 'fluid' )} ?>

        <style>
            html, body {
              background-color: #eee;
            }

            body {
                padding-top: 40px;
            }
        </style>
    <?php endif; ?>



</head>

<body>

<?php
    // We used to manage these menus with a lot of if / elseif / else clauses. It was a mess.
    // Despite the drawbacks of replication, it's easier - by a distance - to mainatin standalone
    // menu templates per user type:

    if( !Auth::check() ) {
        echo $t->insert("layouts/menus/public");
    } elseif( Auth::user()->isCustUser() && Auth::user()->getCustomer()->isTypeAssociate() ) {
        echo $t->insert("layouts/menus/associate");
    } elseif( Auth::user()->isCustUser() ) {
        echo $t->insert("layouts/menus/custuser");
    } elseif( Auth::user()->isCustUser() ) {
        echo $t->insert("layouts/menus/custadmin");
    } elseif( Auth::user()->isSuperUser() ) {
        echo $t->insert("layouts/menus/superuser");
    }
?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

        <div class="padding20LR container-fluid">

            <?= $t->insert( 'menu' ); ?>

    <?php else: ?>

        <div class="container">

    <?php endif; ?>


    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <div id="breadcrumb-area">
            <ol class="breadcrumb">
                <?php $this->section('page-header-preamble') ?>
                <?php $this->stop() ?>
                <li>
                    <a href="<?= url('') ?>">Home</a>
                </li>
                <li class="active">
                    <?php $this->section('title') ?>
                    <?php $this->stop() ?>
                </li>
                <?php $this->section('page-header-postamble') ?>
                <?php $this->stop() ?>
            </ol>
        </div>
    <?php else: ?>
        <div class="page-content">
            <div class="page-header">
                <?php $this->section('page-header-preamble') ?>
                <?php $this->stop() ?>
                <h1>
                    <?php $this->section('title') ?>
                    <?php $this->stop() ?>
                </h1>
                <?php $this->section('page-header-postamble') ?>
                <?php $this->stop() ?>
            </div>
    <?php endif; ?>


<?php $this->section('content') ?>
<?php $this->stop() ?>



<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        </div><!--/span-->
    </div><!--/row-->

<?php else: ?>

    </div>

<?php endif; ?>

<?= $t->insert( 'footer-content' ); ?>

</div> <!-- </div class="container"> -->

    <script type="text/javascript" src="<?= asset('/bower_components/jquery/dist/jquery.min.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/bower_components/jquery-ui/jquery-ui.min.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/bower_components/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/bower_components/vue/dist/vue.min.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/bower_components/select2/dist/js/select2.min.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/js/900-oss-framework.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/js/ixp-manager.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/bower_components/bootbox.js/bootbox.js') ?>"></script>

    <script>
        $( ".chzn-select" ).select2({ width: '100%' });

        <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
            $( "#menu-select-customer" ).select2({ placeholder: "Jump to customer...", allowClear: true }).change( function(){
                document.location.href = '<?= url( "/customer/overview" ) ?>/id/' + $( "#menu-select-customer" ).val();
            });
        <?php endif; ?>
    </script>


    <?php $this->section('scripts') ?>
    <?php $this->stop() ?>

    <?=
        // Skin this file to add your own footer content such as
        // Piwik / Google Analytics integration:
        $t->insert( 'footer-custom' );
    ?>

</body>
</html>

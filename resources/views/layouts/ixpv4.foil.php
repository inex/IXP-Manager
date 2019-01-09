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

    <link rel="stylesheet" type="text/css" href="<?= asset('css/ixp-pack.css') ?>" />
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
    } elseif( Auth::user()->isCustAdmin() ) {
        echo $t->insert("layouts/menus/custadmin");
    } elseif( Auth::user()->isSuperUser() ) {
        echo $t->insert("layouts/menus/superuser");
    }
?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

        <div class="container-fluid">
            <div class="row" >
                <?= $t->insert( 'menu' ); ?>


    <?php else: ?>

        <div class="container">
            <div class="row" >

    <?php endif; ?>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 mt-2">

        <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">

                        <?php $this->section('page-header-preamble') ?>
                        <?php $this->stop() ?>

                        <li class="breadcrumb-item">
                            <a href="<?= url('') ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php $this->section('title') ?>
                            <?php $this->stop() ?>
                        </li>
                        <?php $this->section('page-header-postamble') ?>
                        <?php $this->stop() ?>



                    <?php $this->section('page-header-postamble-extra') ?>
                    <?php $this->stop() ?>
                </ol>
            </nav>
        <?php else: ?>
            <div class="page-content">
                <div class="page-header">
                    <?php $this->section('page-header-preamble') ?>
                    <?php $this->stop() ?>
                    <h1 style="display: inline">
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

    </div><!--/row-->

<?php else: ?>

    </div>

<?php endif; ?>

<?= $t->insert( 'footer-content' ); ?>
            </main>
</div> <!-- </div class="container"> -->

    <script> const RIPE_ASN_URL = "<?= url( "api/v4/aut-num" ) ?>"; </script>
    <script> const MARKDOWN_URL = "<?= route( "utils@markdown" ) ?>"; </script>
    <script type="text/javascript" src="<?= asset('js/ixp-pack.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('js/ixp-manager.js') ?>"></script>

    <script>

        $( ".chzn-select" ).select2({ width: '100%', placeholder: function() {
            $(this).data('placeholder');
        }});

        $( ".chzn-select-tag" ).select2({ width: '100%', tags: true, placeholder: function() {
            $(this).data('placeholder');
        }});

        $( ".chzn-select-deselect" ).select2({ width: '100%', allowClear: true, placeholder: function() {
            $(this).data('placeholder');
        }});

        $( ".chzn-select-deselect-tag" ).select2({ width: '100%', allowClear: true, tags: true, placeholder: function() {
            $(this).data('placeholder');
        }});

        <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
            $( "#menu-select-customer" ).select2({ placeholder: "Jump to customer...", allowClear: true }).change( function(){
                document.location.href = '<?= url( "/customer/overview" ) ?>/' + $( "#menu-select-customer" ).val();
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

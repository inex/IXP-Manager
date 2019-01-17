<!DOCTYPE html>
<html lang="en">

    <head>
        <!--  IXP MANAGER - template directory: resources/[views|skins] -->

        <base href="<?= url('') ?>/index.php">

        <meta http-equiv="Content-Type" content="text/html; charset=utf8" />
        <meta charset="utf-8">

        <title>
            <?= config('identity.sitename' ) ?>
        </title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="<?=  csrf_token() ?>">

        <link rel="stylesheet" type="text/css" href="<?= asset('css/ixp-pack.css') ?>" />
        <?php $this->section('headers') ?>
        <?php $this->stop() ?>

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
        <div class="container-fluid">
            <div class="row" >

    <?php endif; ?>
                <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
                    <main role="main" class="col-md-10 ml-sm-auto col-lg-10 mt-2 pb-4">
                <?php else: ?>
                    <main role="main" class="col-md-10 mx-auto mt-2 pb-4">
                <?php endif; ?>

                    <?php /*if( Auth::check() && Auth::user()->isSuperUser() ): */?>

                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h1 class="h2">
                                <?php $this->section('page-header-preamble') ?>
                                <?php $this->stop() ?>
                            </h1>
                            <div class="btn-toolbar mb-2 mb-md-0">
                                <?php $this->section('page-header-postamble') ?>
                                <?php $this->stop() ?>
                            </div>
                        </div>

                    <?php /*else: */?><!--
                        <div class="page-content">
                            <div class="page-header">
                                <?php /*$this->section('page-header-preamble') */?>
                                <?php /*$this->stop() */?>
                                <h1 style="display: inline">
                                    <?php /*$this->section('title') */?>
                                    <?php /*$this->stop() */?>
                                </h1>
                                <?php /*$this->section('page-header-postamble') */?>
                                <?php /*$this->stop() */?>
                            </div>
                    --><?php /*endif; */?>

                    <div class="container-fluid">
                        <?php $this->section('content') ?>
                        <?php $this->stop() ?>
                    </div>


                </main>

            </div> <!-- </div class="row"> -->

        </div> <!-- </div class="container"> -->

        <?= $t->insert( 'footer-content' ); ?>


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

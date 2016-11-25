<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?= url('') ?>/index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />
    <meta charset="utf-8">

    <title>IXP Manager</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="stylesheet" type="text/css" href="<?= asset('bower_components/bootstrap/dist/css/bootstrap.min.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= asset('bower_components/chosen/chosen.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= asset('css/ixp-manager.css') ?>" />

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
        echo $t->insert("menus/public");
    } elseif( Auth::user()->isCustUser() && Auth::user()->getCustomer()->isTypeAssociate() ) {
        echo $t->insert("menus/associate");
    } elseif( Auth::user()->isCustUser() ) {
        echo $t->insert("menus/custuser");
    } elseif( Auth::user()->isCustUser() ) {
        echo $t->insert("menus/custadmin");
    } elseif( Auth::user()->isCustUser() ) {
        echo $t->insert("menus/superuser");
    }
?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

        <div class="container-fluid">

            <?= $t->insert( 'menu' ); ?>

    <?php else: ?>

        <div class="container">

    <?php endif; ?>


    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <ul class="breadcrumb">
            <li>
                <a href="<?= url('') ?>">Home</a> <span class="divider">/</span>
            </li>
            <li class="active">
                <?php $this->section('title') ?>
                    Title
                <?php $this->stop() ?>
            </li>
        </ul>
    <?php else: ?>
        <div class="page-content">
            <div class="page-header">
                <h1>
                    <?php $this->section('title') ?>
                        Title
                    <?php $this->stop() ?>
                </h1>
            </div>
    <?php endif; ?>


<?php $this->section('content') ?>
No page content...
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
    <script type="text/javascript" src="<?= asset('/bower_components/chosen/chosen.jquery.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/bower_components/vue/dist/vue.min.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/js/900-oss-framework.js') ?>"></script>
    <script type="text/javascript" src="<?= asset('/js/ixp-manager.js') ?>"></script>


    <?php if( Auth::check() ): ?>
    <script>
        $( ".chzn-select" ).chosen( { width: '100%' } );

        <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
            $( "#menu-select-customer" ).chosen().change( function(){
                document.location.href = '<?= url( "/customer/overview" ) ?>/id/' + $( "#menu-select-customer" ).val();
            });

            <?php /* {if isset( $acust )}
                $( "#menu-select-customer" ).val( {$acust.id} );
                $( "#menu-select-customer" ).trigger( "chosen:updated" );
            {/if} */ ?>
        <?php endif; ?>
    </script>
    <?php endif; ?>


    <?php $this->section('scripts') ?>
    <?php $this->stop() ?>
</body>
</html>

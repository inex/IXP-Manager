<!DOCTYPE html>
<html lang="en">

<head>

    <!--  IXP MANAGER - template directory: resources/[views|skins] -->

    <base href="<?= url('') ?>/index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />
    <meta charset="utf-8">
    
    <title>IXP Manager</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="<?=  csrf_token() ?>">

    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/200-jquery-ui-1.8.23.custom.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/230-jquery.contextMenu.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/250-jquery-colorbox.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/bower_components/chosen/chosen.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/800-bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/805-bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/810-override_container_app.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/820-bootstrap-override.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/830-bootstrap-wysihtml5.css" />
    <link rel="stylesheet" type="text/css" href="<?= url('') ?>/css/900-ixp-manager.css" />
	
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

    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>container-fluid<?php else: ?>container<?php endif; ?>">
                 <a id="collapsed_menu" class="btn btn-navbar" data-target=".nav-collapse" data-toggle="collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a class="brand" href="<?= url('') ?>">
                    <?= config('identity.sitename' ) ?>
                </a>
                <?php if( Auth::check() ): ?>
                    <div class="nav-collapse">
                         <ul class="nav">
                            <?php if( Auth::user()->isCustUser() ): ?>
                                <li {if $controller eq "dashboard"}class="active"{/if}>
                                    <a href="{genUrl}">Home</a>
                                </li>
                            <?php endif; ?>
                            <?php if( !Auth::user()->isCustAdmin() ): ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Member Information <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{genUrl controller="switch" action="configuration"}">Switch Configuration</a>
                                        </li>
                                        <li>
                                            <a href="{genUrl controller="customer" action="details"}">Member Details</a>
                                        </li>
                                        <?php if( !config( 'ixp_fe.frontend.disabled.meeting', true ) ): ?>
                                            <li>
                                                <a href="{genUrl controller="meeting" action="read"}">Meetings</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Peering<b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <?php if( Auth::user()->isCustUser() && !Auth::user()->getCustomer()->isTypeAssociate() ): ?>
                                            <?php if( !config( 'ixp_fe.frontend.disabled.peering-manager', false ) ): ?>
                                                <li><a href="{genUrl controller="peering-manager"}">Peering Manager</a></li>
                                            <?php endif; ?>
                                            <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes', false ) ): ?>
                                                <?php if( Auth::user()->getCustomer()->isRouteServerClient() ): ?>
                                                    <li><a href="{genUrl controller="rs-prefixes" action="list"}">Route Server Prefixes</a></li>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if( !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                                            <li><a href="{genUrl controller="peering-matrix"}">Public Peering Matrix</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>

                                <?php
                                    // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                                    // Add a skinned file in views/_skins/xxx/header-documentation.phtml for your IXP to override the sample
                                    echo $this->insert('header-documentation');
                                ?>

                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Statistics<b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <?php if( Auth::user()->isCustUser() && !Auth::user()->getCustomer()->isTypeAssociate() ): ?>
                                            <li>
                                                <a href="<?= url( 'statistics/member' ) ?>">My Statistics</a>
                                            </li>

                                            <?php if( config('grapher.backends.sflow.enabled') ): ?>
                                                <li>
                                                    <a href="<?= url( 'statistics/p2p' ) ?>">My Peer to Peer Traffic</a>
                                                </li>
                                            <?php endif; ?>

                                            <li class="divider"></li>
                                        <?php endif; ?>
                                        <li>
                                            <a href="<?= url('statistics/public') ?>">Overall Peering Graphs</a>
                                        </li>
                                        <li>
                                            <a href="<?= url('statistics/trunks') ?>">Inter-Switch / PoP Graphs</a>
                                        </li>
                                        <li>
                                            <a href="<?= url('statistics/switches') ?>">Switch Aggregate Graphs</a>
                                        </li>
                                        <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>
                                            <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                                                <li>
                                                    <a href="<?= url( '/weather-map/index/id/' . $k ) ?>"><?= $w['menu'] ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <li class="">
                                <a href="<?= url( '/static/support' ) ?>">Support</a>
                            </li>
                            <?php if( Auth::user()->isSuperUser() ):
                                    echo $this->insert('staff-links');
                                endif; ?>

                            <?php if( !Auth::user()->isSuperUser() ): ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Account<b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?= url( '/profile' ) ?>">Profile</a>
                                        </li>
                                        <li>
                                            <a href="<?= url( '/api-key' ) ?>">API Keys</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="<?php url( 'auth/logout' ) ?>">Logout</a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>

                        </ul>
                        <ul class="nav pull-right">
                            <?php if( Auth::user()->isSuperUser() ): ?>
                                <form class="navbar-search pull-left">
                                    <select data-placeholder="View a Customer..." id="menu-select-customer" type="select" name="id" class="chzn-select">
                                        <option></option>
                                        <?php foreach( $t->customers as $k => $i ): ?>
                                            <option value="<?= $k ?>"><?= $i ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            <?php endif; ?>

<?php /*                            {if isset( $session->switched_user_from ) and $session->switched_user_from}
                                <li><a href="{genUrl controller="auth" action="switch-user-back"}">Switch Back</a></li>
                            {elseif */ ?>
                            <?php if( Auth::user()->isSuperUser() ): ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Account<b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?= url('profile') ?>">Profile</a>
                                        </li>
                                        <li>
                                            <a href="<?= url('api-key' )?>">API Keys</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="<?= url( 'customer/unread-notes' ) ?>">Unread Notes</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="<?= url( 'auth/logout' ) ?>">Logout</a>
                                        </li>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li><a href="<?= url( 'auth/logout' ) ?>">Logout</a></li>
                            <?php endif; ?>
                        </ul>
                    </div><!--/.nav-collapse -->
                <?php else: ?>
                    <div class="nav-collapse">
                        <ul class="nav pull-right">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Statistics<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php url( 'public-statistics/public' ) ?>">Overall Peering Graphs</a>
                                    </li>
                                    <li>
                                        <a href="<?php url( 'public-statistics/trunks' ) ?>">Inter-Switch / PoP Graphs</a>
                                    </li>
                                    <li>
                                        <a href="<?php url( 'public-statistics/switches' ) ?>">Switch Aggregate Graphs</a>
                                    </li>
                                    <li class="divider"></li>
                                    <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>
                                        <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                                            <li>
                                                <a href="<?= url( '/weather-map/index/id/' . $k ) ?>"><?= $w['menu'] ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </li>

                            <li class="">
                                <a href="<?= url( '/static/support' ) ?>">Support</a>
                            </li>
                            <li>
                                <a href="http://www.ixpmanager.org/" target="_blank">About</a>
                            </li>
                            <li class="">
                                <a href="<?= url( '/auth/login' ) ?>">Login</a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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


    <script type="text/javascript" src="<?= url('') ?>/bower_components/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/210-jquery-ui-1.10.3.custom.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/220-jquery.dataTables-1.9.4.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/231-jquery.contextMenu-1.5.24.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/240-jquery.json-2.3.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/245-jquery-cookie.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/250-jquery-colorbox-1.4.21.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/bower_components/chosen/chosen.jquery.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/310-throbber.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/700-php.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/800-bootstrap-2.3.2.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/830-bootstrap-wysihtml5.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/890-bootbox.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/900-oss-framework.js"></script>
    <script type="text/javascript" src="<?= url('') ?>/js/999-ixpmanager.js"></script>


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

</body>
</html>

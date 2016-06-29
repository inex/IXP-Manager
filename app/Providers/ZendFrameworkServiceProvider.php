<?php namespace IXP\Providers;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */



use Illuminate\Support\ServiceProvider;
use IXP\Services\Helpdesk\ConfigurationException;
use Config;
use Zend_Registry;

/**
 * ZendFramework Service Provider
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ZendFrameworkServiceProvider extends ServiceProvider {

    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // reset options from Laravel
        $zf = $this->app->make('ZendFramework');
        $options = $zf->getOptions();

        // let's not muck about with core options
        unset( $options['bootstrap'] );

        $options = $this->setupUrls($options);
        $options = $this->setupAuth($options);
        $options = $this->setupSmarty($options);
        $options = $this->setupSmokeping($options);
        $options = $this->setupIdentity($options);
        $options = $this->setupPeeringManager($options);
        $options = $this->setupDisabledFrontendControllers($options);

        // now we need to shove these options back into ZendFramework.
        // There's a but of duplication and complexity here:
        $zf->setOptions( $options );
        $zf->getBootstrap()->setOptions( $options );
        Zend_Registry::set( 'options', $options );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton( 'ZendFramework', function($app) {

            // Define path to application directory
            defined('APPLICATION_PATH')
                || define('APPLICATION_PATH', realpath( __DIR__ . '/../../application' ) );

            include( __DIR__ . '/../../bin/utils.inc' );

            // Define application environment
            if( php_sapi_name() == 'cli-server' ) {
                // running under PHP's built in web server: php -S
                // as such, .htaccess is not processed
                define( 'APPLICATION_ENV', scriptutils_get_application_env() );
            } else {
                // probably Apache or other web server
                defined('APPLICATION_ENV')
                    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : scriptutils_get_application_env() ));
            }

            /** Zend_Application */
            require_once 'Zend/Application.php';

            require_once( APPLICATION_PATH . '/../library/IXP/Version.php' );

            // Create application, bootstrap, and run
            $application = new \Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
            );

            return $application->bootstrap();
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ZendFramework'];
    }


    /**
     * Force URL (and http/s schema) if necessary
     */
    private function setupUrls( array $options ): array {
        if( config('identity.urls.forceUrl') ) {
            $options['utils']['genurl']['host_mode']    = 'REPLACE';
            $options['utils']['genurl']['host_replace'] = config('identity.urls.forceUrl');
        }
        return $options;
    }

    /**
     * Set authentication options
     */
    private function setupAuth( array $options ): array {
        $options['resources']['auth']['oss']['pwhash']    = config('auth.zf1.pwhash');
        $options['resources']['auth']['oss']['hash_cost'] = config('auth.zf1.hash_cost');
        return $options;
    }

    /**
     * Set identity
     */
    private function setupIdentity( array $options ): array {
        $options['identity']['orgname']               = config( 'identity.orgname' );
        $options['identity']['legalname']             = config( 'identity.legalname' );
        $options['identity']['location']['city']      = config( 'identity.location.city' );
        $options['identity']['location']['country']   = config( 'identity.location.country' );
        $options['identity']['ixfid']                 = config( 'identity.ixfid' );
        $options['identity']['name']                  = config( 'identity.name' );
        $options['identity']['email']                 = config( 'identity.email' );
        $options['identity']['email']                 = config( 'identity.email' );
        $options['identity']['autobot']['name']       = config( 'identity.autobot.name' );
        $options['identity']['autobot']['email']      = config( 'identity.autobot.email' );
        $options['identity']['mailer']['name']        = config( 'identity.mailer.name' );
        $options['identity']['mailer']['email']       = config( 'identity.mailer.email' );
        $options['identity']['sitename']              = config( 'identity.sitename' );
        $options['identity']['url']                   = config( 'app.url' );
        $options['identity']['logo']                  = config( 'identity.logo' );
        $options['identity']['biglogo']               = config( 'identity.biglogo' );
        $options['identity']['biglogoconf']['offset'] = config( 'identity.biglogoconf.offset' );
        $options['identity']['misc']['irc_password']  = config( 'identity.misc.irc_password' );
        $options['identity']['vlans']['default']      = config( 'identity.vlans.default' );

        return $options;
    }

    /**
     * Setup peering manager
     */
    private function setupPeeringManager( array $options ): array {
        $options['peering_manager']['testmode']  = config( 'ixp.peering_manager.testmode' );
        $options['peering_manager']['testemail'] = config( 'ixp.peering_manager.testemail' );
        $options['peering_manager']['testnote']  = config( 'ixp.peering_manager.testnote' );
        $options['peering_manager']['testdate']  = config( 'ixp.peering_manager.testdate' );

        return $options;
    }

    /**
     * Setup frontend disabled controllers
     */
    private function setupDisabledFrontendControllers( array $options ): array {
        if( is_array( config('ixp_fe.frontend.disabled' ) ) ) {
            foreach( config('ixp_fe.frontend.disabled' ) as $controller => $state ) {
                $options['frontend']['disabled'][$controller]  = $state;
            }
        }

        return $options;
    }

    /**
     * Setup Smokeping
     */
    private function setupSmokeping( array $options ): array {
        if( is_array( config('smokeping.conf' ) ) ) {
            foreach( config('smokeping.conf' ) as $k => $v ) {
                $options['smokeping']['conf'][$k]  = $v;
            }
        }

        if( is_array( config('smokeping.oconf' ) ) ) {
            foreach( config('smokeping.oconf' ) as $k => $v ) {
                $options['smokeping']['oconf'][$k]  = $v;
            }
        }

        return $options;
    }


    /**
     * Setup Smarty
     */
    private function setupSmarty( array $options ): array {
        if( strlen( config('ixp_fe.skinning.smarty' ) ) ) {
            $options['resources']['smarty']['skin']  = config('ixp_fe.skinning.smarty' );
            Zend_Registry::get( 'smarty' )->setSkin( config('ixp_fe.skinning.smarty' ) );
        }

        return $options;
    }


}

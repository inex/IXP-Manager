<?php namespace IXP\Providers;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Log;

/**
 * ZendFramework Service Provider
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
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
    {}

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
                $this->createOptions()
            );

            return $application->bootstrap();
        });
    }

    /**
     * Create the Zend Framework options array
     * @return array
     */
    private function createOptions(): array {
        $options = [];
        $options = $this->setupBaseOptions($options);
        $options = $this->setupPhpSettings($options);
        $options = $this->setupUrls($options);
        $options = $this->setupAuth($options);
        $options = $this->setupSmarty($options);
        $options = $this->setupIdentity($options);
        $options = $this->setupPeeringManager($options);
        $options = $this->setupIxpTools($options);
        $options = $this->setupDisabledFrontendControllers($options);
        $options = $this->setupMailingLists($options);
        $options = $this->setupContactGroups($options);
        $options = $this->setupLogger($options);
        $options = $this->setupMailer($options);
        $options = $this->setupSession($options);
        return $options;
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
     * Set up the base Zend Framework options
     * @param array $options Existing options array
     * @return array
     */
    private function setupBaseOptions(array $options): array {
        $options['bootstrap']['path']  = base_path() . "/application/Bootstrap.php";
        $options['bootstrap']['class'] = "Bootstrap";

        $options['includePaths']['library']         = base_path() . "/library";
        $options['includePaths']['twitter']         = base_path() . "/library/Bootstrap-Zend-Framework/library";
        $options['includePaths']['smarty']          = base_path() . "/vendor/smarty/smarty";
        $options['autoloaderNamespaces']['Twitter'] = "Twitter_";

        $options['pluginPaths']['OSS_Resource'] = base_path() . "/library/OSS/Resource";
        $options['pluginPaths']['IXP_Resource'] = base_path() . "/library/IXP/Resource";

        $options['resources']['frontController']['controllerDirectory'] = base_path() . "/application/controllers";
        $options['resources']['modules'] = [];

        return $options;
    }

    /**
     * PHP Settings
     */
    private function setupPhpSettings( array $options ): array {
        $options['phpSettings']['display_startup_errors'] = config('app.debug');
        $options['phpSettings']['display_errors']         = config('app.debug');

        return $options;
    }

    /**
     * Force URL
     */
    private function setupUrls( array $options ): array {
        $options['utils']['genurl']['host_mode']    = 'REPLACE';
        $options['utils']['genurl']['host_replace'] = config('app.url');
        return $options;
    }

    /**
     * Set authentication options
     */
    private function setupAuth( array $options ): array {
        $options['resources']['auth']['oss']['adapter']                  = "OSS_Auth_Doctrine2Adapter";
        $options['resources']['auth']['enabled']                         = true;
        $options['resources']['auth']['oss']['entity']                   = "\\Entities\\User";
        $options['resources']['auth']['oss']['login_history']['enabled'] = true;
        $options['resources']['auth']['oss']['login_history']['entity']  = "\\Entities\\UserLoginHistory";
        $options['resources']['auth']['oss']['pwhash']                   = config('auth.zf1.pwhash');
        $options['resources']['auth']['oss']['hash_cost']                = config('auth.zf1.hash_cost');

        $options['resources']['namespace']['checkip']           = 0;
        $options['resources']['namespace']['timeout']           = config('session.lifetime')*60;

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
        $options['identity']['sitename']              = config( 'identity.sitename' );
        $options['identity']['url']                   = config( 'app.url' );
        $options['identity']['logo']                  = config( 'identity.logo' );
        $options['identity']['biglogo']               = config( 'identity.biglogo' );
        $options['identity']['biglogoconf']['offset'] = config( 'identity.biglogoconf.offset' );
        $options['identity']['vlans']['default']      = config( 'identity.vlans.default' );

        return $options;
    }

    /**
     * Setup peering manager
     */
    private function setupPeeringManager( array $options ): array {
        $options['peeringmanager']['testmode']  = config( 'ixp.peering_manager.testmode' );
        $options['peeringmanager']['testemail'] = config( 'ixp.peering_manager.testemail' );
        $options['peeringmanager']['testnote']  = config( 'ixp.peering_manager.testnote' );
        $options['peeringmanager']['testdate']  = config( 'ixp.peering_manager.testdate' );

        $options['peeringdb']['url'] = "https://www.peeringdb.com/view.php?asn=%ASN%";

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
     * Setup Smarty
     */
    private function setupSmarty( array $options ): array {

        $options['resources']['smarty']['enabled']   = true;
        $options['resources']['smarty']['templates'] = base_path() . "/application/views";
        $options['resources']['smarty']['compiled']  = base_path() . "/var/templates_c";
        $options['resources']['smarty']['cache']     = base_path() . "/var/cache";
        $options['resources']['smarty']['config']    = base_path() . "/application/configs/smarty";
        $options['resources']['smarty']['plugins'][] = base_path() . "/library/inex-smarty/functions";
        $options['resources']['smarty']['plugins'][] = base_path() . "/library/OSS/Smarty/functions";
        $options['resources']['smarty']['plugins'][] = base_path() . "/vendor/smarty/smarty/libs/plugins";
        $options['resources']['smarty']['plugins'][] = base_path() . "/vendor/smarty/smarty/libs/sysplugins";

        $options['resources']['smarty']['debugging'] = false;

        if( strlen( config('ixp_fe.skinning.smarty' ) ) ) {
            $options['resources']['smarty']['skin']  = config('ixp_fe.skinning.smarty' );
            // Zend_Registry::get( 'smarty' )->setSkin( config('ixp_fe.skinning.smarty' ) );
        }

        return $options;
    }

    /**
     * Setup Mailing Lists
     */
    private function setupMailingLists( array $options ): array {
        if( !config('mailinglists.enabled') ) {
            $options['mailinglist']['enabled'] = false;
            return $options;
        }

        $options['mailinglist']['enabled'] = true;

        if( is_array( config('mailinglists.lists') ) ) {
            foreach( config('mailinglists.lists') as $list => $details ) {
                foreach( $details as $k => $v ) {
                    $options['mailinglists'][$list][$k]  = $v;
                }
            }
        }

        if( is_array( config('mailinglists.mailman.cmds') ) ) {
            foreach( config('mailinglists.mailman.cmds') as $k => $v ) {
                $options['mailinglist']['cmd'][$k]  = $v;
            }
        }

        return $options;
    }

    /**
     * Setup IXP Tools. A mixed bag that needs to be refactored in time.
     */
    private function setupIxpTools( array $options ): array {

        if( is_array( config('ixp_tools.peering_matrix') ) ) {
            foreach( config('ixp_tools.peering_matrix') as $id => $details ) {
                foreach( $details as $k => $v ) {
                    $options['peering_matrix']['public'][$id][$k]  = $v;
                }
            }
        }

        if( config( 'ixp_tools.primary_peering_lan_vlan_tag' ) ) {
            $options['primary_peering_lan']['vlan_tag'] = config( 'ixp_tools.primary_peering_lan_vlan_tag' );
        }

        if( config( 'ixp_tools.peeringdb_url' ) ) {
            $options['peeringdb']['url'] = config( 'ixp_tools.peeringdb_url' );
        }

        if( config( 'ixp_tools.billing_updates_notify' ) ) {
            $options['billing']['updates_notify'] = config( 'ixp_tools.billing_updates_notify' );
        }

        if( config( 'ixp_tools.rir_ripe_password' ) ) {
            $options['rir']['ripe_password'] = config( 'ixp_tools.rir_ripe_password' );
        }

        return $options;
    }

    /**
     * Setup contact groups
     */
    private function setupContactGroups( array $options ): array {
        if( is_array( config('contact_group.types') ) ) {
            foreach( config('contact_group.types') as $k => $v ) {
                $options['contact']['group']['types'][$k] = $v;
            }
        }
        return $options;
    }

    /**
     * Setup logger
     */
    private function setupLogger( array $options ): array {
        $options['ondemand_resources']['logger']['enabled'] = 1;

        if( isset( Log::getMonolog()->getHandlers()[0] ) ) {
            $options['ondemand_resources']['logger']['writers']['stream']['path'] = Log::getMonolog()->getHandlers()[0]->getUrl();
        }

        if( is_array( config( 'ixp_tools.logger.email' ) ) ) {
            foreach( config('ixp_tools.logger.email') as $k => $v ) {
                $options['ondemand_resources']['logger']['writers']['email'][$k] = $v;
            }
        }

        return $options;
    }

    /**
     * Setup mailer
     */
    private function setupMailer( array $options ): array {

        $options['resources']['mailer']['smtphost'] = config('mail.host');

        $options['resources']['mailer']['port'] = config('mail.port',null) ?? 25;

        $options['resources']['mailer']['auth']     = config('mail.auth',null);
        $options['resources']['mailer']['username'] = config('mail.username',null);
        $options['resources']['mailer']['password'] = config('mail.password',null);

        return $options;
    }

    /**
     * Setup session
     */
    private function setupSession( array $options ): array {
        $options['resources']['session'] = [];
        return $options;
    }
}

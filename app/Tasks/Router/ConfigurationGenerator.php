<?php

declare(strict_types=1);
namespace IXP\Tasks\Router;

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

use Entities\Vlan;
use Illuminate\Contracts\View\View as ViewContract;
use IXP\Exceptions\GeneralException;
use View;

/**
 * ConfigurationGenerator
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Router
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConfigurationGenerator
{
    /**
     * Handle - key for router in config/routers.php
     * @var string
     */
    private $handle = null;

    /**
     * Router details array.
     *
     * See config/routers.php
     *
     * @var array
     */
    private $router = null;

    public function __construct( string $handle ) {
        $this->setRouterByHandle( $handle );
        $this->setHandle( $handle );

        // make sure the template exists or there's no point continuing:
        if( !isset( $this->router()['template'] ) ) {
            throw new GeneralException( "Template not set in router settings" );
        }

        $template = preg_replace( '/[^\da-z_\-\/]/i', '', $this->router()['template'] );
        if( $template[0] == '/' ) {
            $template = substr( $template, 1 );
        }

        if( !View::exists( $template ) ) {
            throw new GeneralException( "Template does not exist" );
        }

    }

    /**
     * Set the router handle string
     *
     * @param string $handle Router handle
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setHandle( string $handle ): ConfigurationGenerator {
        $this->handle = $handle;
        return $this;
    }

    /**
     * Get the router handle
     *
     * @return string
     */
    public function handle(): string {
        return $this->handle;
    }

    /**
     * Set the router options array
     *
     * @throws IXP\Exceptions\GeneralException
     * @param array $router Router details (see config/routers.php)
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setRouter( array $router ): ConfigurationGenerator {
        $this->router = $router;
        return $this;
    }

    /**
     * Get the router options array
     *
     * @return array
     */
    public function router(): array {
        return $this->router;
    }

    /**
     * Set (and validate) the router by handle
     *
     * @throws IXP\Exceptions\GeneralException
     * @param string $handle Router handle to generate configuration for
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setRouterByHandle( string $handle ): ConfigurationGenerator {
        // handle existance should be validated up the chain and errored appropriately (api, cli) - but belt'n'braces:
        if( ! ( $router = config( 'routers.'.$handle, false ) ) ) {
            throw new GeneralException( "Router handle does not exist: " . $handle );
        }

        return $this->setRouter( config( 'routers.'.$handle ) );
    }

    /**
     * Generate and return the configuration
     *
     * @throws IXP\Exceptions\GeneralException
     * @return Illuminate\Contracts\View The configuration
     */
    public function render(): ViewContract {

        // does the VLAN exist?
        if( !isset($this->router['vlan_id']) || !( $vlan = d2r('Vlan')->find( $this->router()['vlan_id'] ) ) ) {
            throw new GeneralException( "Invalid/missing vlan_id in router object" );
        }

        $ints = d2r( 'VlanInterface' )->sanitiseVlanInterfaces($vlan, $this->router()['protocol'], $this->router()['type'], $this->router()['quarantine']);

        return view( $this->router()['template'] )->with(
            ['handle' => $this->handle(), 'ints' => $ints, 'router' => $this->router(), 'vlan' => $vlan]
        );
    }


}

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
use IXP\Exceptions\ConfigurationException;
use IXP\Utils\Router;
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
     * Router details object.
     *
     * See config/routers.php
     *
     * @var IXP\Utils\Router
     */
    private $router = null;

    public function __construct( string $handle ) {
        $this->setRouter( new Router( $handle ) );
        $this->router()->checkTemplate();
    }

    /**
     * Set the router options array
     *
     * @throws IXP\Exceptions\GeneralException
     * @param array $router Router details (see config/routers.php)
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setRouter( Router $router ): ConfigurationGenerator {
        $this->router = $router;
        return $this;
    }

    /**
     * Get the router options array
     *
     * @return array
     */
    public function router(): Router {
        return $this->router;
    }

    /**
     * Generate and return the configuration
     *
     * @throws IXP\Exceptions\GeneralException
     * @return Illuminate\Contracts\View The configuration
     */
    public function render(): ViewContract {

        // does the VLAN exist?
        if( !$this->router()->vlanId() || !( $vlan = d2r('Vlan')->find( $this->router()->vlanId() ) ) ) {
            throw new ConfigurationException( "Invalid/missing vlan_id in router object" );
        }

        $ints = d2r( 'VlanInterface' )->sanitiseVlanInterfaces($vlan, $this->router()->protocol(), $this->router()->type(), $this->router()->quarantine() );

        return view( $this->router()->template() )->with(
            ['handle' => $this->router()->handle(), 'ints' => $ints, 'router' => $this->router(), 'vlan' => $vlan]
        );
    }


}

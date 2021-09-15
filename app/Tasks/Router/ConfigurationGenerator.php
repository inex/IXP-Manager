<?php

namespace IXP\Tasks\Router;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Log;
use Illuminate\Contracts\View\View as ViewContract;

use IXP\Models\{
    Aggregators\VlanInterfaceAggregator,
    Router
};

/**
 * ConfigurationGenerator
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Router
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConfigurationGenerator
{
    /**
     * Router details object.
     *
     * @var Router $r
     */
    private $router = null;

    /**
     * ConfigurationGenerator constructor
     * .
     * @param Router $r
     *
     * @throws
     */
    public function __construct( Router $r )
    {
        $this->setRouter( $r );
    }

    /**
     * Set the router options array
     *
     * @param Router $router Router details
     *
     * @return ConfigurationGenerator
     *
     * @throws
     */
    public function setRouter( Router $router ): ConfigurationGenerator
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Get the router options array
     *
     * @return Router
     */
    public function router(): Router
    {
        return $this->router;
    }

    /**
     * Generate and return the configuration
     *
     * @return ViewContract The configuration
     *
     * @throws
     */
    public function render(): ViewContract
    {
        $ints = VlanInterfaceAggregator::sanitiseVlanInterfaces(
            $this->router()->vlan, $this->router()->protocol, $this->router()->type, $this->router()->quarantine
        );

        $v = view( $this->router()->template )->with(
            [ 'handle'  => $this->router()->handle,
              'ints'    => $ints,
              'router'  => $this->router(),
              'vlan'    => $this->router()->vlan
            ]
        );

        return $v;
    }
}
<?php

declare(strict_types=1);
namespace IXP\Tasks\Router;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM;
use IXP\Exceptions\GeneralException;
use Illuminate\Contracts\View\View as ViewContract;

use Entities\{Router as RouterEntity, Router, VlanInterface as VlanInterfaceEntity};

/**
 * ConfigurationGenerator
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Router
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConfigurationGenerator
{
    /**
     * Router details object.
     *
     * @var RouterEntity $r
     */
    private $router = null;

    public function __construct( RouterEntity $r ) {
        $this->setRouter( $r );
    }

    /**
     * Set the router options array
     *
     * @throws GeneralException
     * @param RouterEntity $router Router details
     * @return ConfigurationGenerator
     */
    public function setRouter( RouterEntity $router ): ConfigurationGenerator {
        $this->router = $router;
        return $this;
    }

    /**
     * Get the router options array
     *
     * @return RouterEntity
     */
    public function router(): RouterEntity {
        return $this->router;
    }

    /**
     * Generate and return the configuration
     *
     * @throws GeneralException
     * @return ViewContract The configuration
     */
    public function render(): ViewContract {
        $ints = D2EM::getRepository( VlanInterfaceEntity::class )->sanitiseVlanInterfaces(
            $this->router()->getVlan(), $this->router()->getProtocol(), $this->router()->getType(), $this->router()->getQuarantine() );

        return view( $this->router()->getTemplate() )->with( [
            'handle'  => $this->router()->getHandle(),
            'ints'    => $ints,
            'router'  => $this->router(),
            'vlan'    => $this->router()->getVlan(),
            'rs_asns' => D2EM::getRepository( RouterEntity::class )->getASNsForType( RouterEntity::TYPE_ROUTE_SERVER, $this->router()->getVlan()->getId() ),
        ] );
    }

}

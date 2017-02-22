<?php

declare(strict_types=1);
namespace IXP\Tasks\Salt;

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

use Entities\Switcher as SwitchEntity;
use Illuminate\Contracts\View\View as ViewContract;
use IXP\Exceptions\GeneralException;

/**
 * ConfigurationGenerator
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Router
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchConfigurationGenerator
{
    /**
     *
     * @var SwitchEntity
     */
    private $switch = null;

    public function __construct( SwitchEntity $switch ) {
        $this->setSwitch( $switch );
    }

    /**
     * Set the switch
     *
     * @param SwitchEntity $switch
     * @return SwitchConfigurationGenerator
     */
    public function setSwitch( SwitchEntity $switch ): SwitchConfigurationGenerator {
        $this->switch = $switch;
        return $this;
    }

    /**
     * Get the switch options array
     *
     * @return SwitchEntity
     */
    public function getSwitch(): SwitchEntity {
        return $this->switch;
    }

    private function template(): string {
        $tmpl = preg_replace( '/[^\da-z_\-\/]/i', '', strtolower( 'api/v4/provisioner/salt/switch/' . $this->getSwitch()->getVendor()->getShortname() ) );

        if( !view()->exists( $tmpl ) ) {
            throw new GeneralException( "Template does not exist: " . $tmpl );
        }

        return $tmpl;
    }

    /**
     * Generate and return the configuration
     *
     * @throws GeneralException
     * @return ViewContract The configuration
     */
    public function render(): ViewContract {

        return view( $this->template() )->with(
            [ 'switch' => $this->getSwitch() ]
        );
    }


}

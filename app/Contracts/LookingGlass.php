<?php

declare(strict_types=1);
namespace IXP\Contracts;

use IXP\Utils\Router;


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

 /**
  * LookingGlassContract Contract - any concrete implementation of a LookingGlassContract
  * provider must implement this interface
  *
  * @see        http://laravel.com/docs/5.0/contracts
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   LookingGlassContract
  * @package    IXP\Contracts
  * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
interface LookingGlass {

    /**
     * Set the router object
     * @param Router $r
     * @return IXP\Services\LookingGlass\BirdsEye For fluent interfaces
     */
    public function setRouter( Router $r ): LookingGlass;

    /**
     * Get the router object
     * @return IXP\Utils\Router
     */
    public function router(): Router;

    /**
     * Get BGP Summary information as JSON
     *
     * Response must use equivalent structure as Bird's Eye:
     *     https://github.com/inex/birdseye/
     *
     * @return string
     */
    public function bgpSummary(): string;


}

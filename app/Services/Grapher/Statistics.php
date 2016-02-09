<?php namespace IXP\Services\Grapher;

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

use IXP\Services\Grapher;
use IXP\Services\Grapher\Graph;

/**
 * Grapher -> Statistics
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Statistics {

    /**
     * Graph under consideration
     * @var IXP\Services\Grapher\Graph
     */
    private $graph;


    /**
     * Constructor
     */
    public function __construct( Graph $g ) {
        $this->graph = $g;
    }

    /**
     * Access for the graph object under consideration
     * @return IXP\Services\Grapher\Graph
     */
    public function graph(): Graph {
        return $this->graph;
    }

    /**
     * Access for the graph object under consideration
     * @return IXP\Services\Grapher\Graph
     */
    public function data(): array {
        return $this->graph()->data();
    }

}

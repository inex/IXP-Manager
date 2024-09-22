<?php

namespace IXP\Services\Diagnostics;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Diagnostics Service - Result Container
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class DiagnosticResultSet
{
    public DiagnosticSuite $suite;

    /** @var DiagnosticResult[]  */
    public array $results = [];

    /** @var DiagnosticResultSet[]  */
    protected array $subsets = [];


    /**
     * @param DiagnosticResult|null $result
     */
    public function __construct( DiagnosticSuite $suite, ?DiagnosticResult $result = null ) {
        $this->suite = $suite;

        if ($result !== null) {
            $this->results[] = $result;
        }
    }

    /**
     * Adds a diagnostic result to the result set.
     *
     * @param DiagnosticResult|DiagnosticResult[] $result The diagnostic result to add.
     * @return DiagnosticResultSet This diagnostic result set.
     */
    public function add( DiagnosticResult|array $result ): DiagnosticResultSet {

        $this->results = array_merge(
            $this->results,
            is_array($result) ? $result : [$result]
        );

        return $this;
    }


    public function addSubset(DiagnosticResultSet $subset) {
        $this->subsets[] = $subset;
    }

    /**
     * @return DiagnosticResultSet[]
     */
    public function subsets(): array {
        return $this->subsets;
    }



}
<?php namespace IXP\Utils\Foil\Extensions;

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

use Foil\Engine;
use Foil\Contracts\ExtensionInterface;

use IXP\Utils\View\Alert\Container as AlertContainer;

/**
 * Grapher -> Renderer view extensions
 *
 * See: http://www.foilphp.it/docs/EXTENDING/CUSTOM-EXTENSIONS.html
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP implements ExtensionInterface {

    private $args;

    public function setup(array $args = []) {
        $this->args = $args;
    }

    public function provideFilters() {
       return [];
    }

    public function provideFunctions() {
        return [
            'alerts'   => [ AlertContainer::class, 'html' ],
            'softwrap' => [$this, 'softwrap'],
        ];
    }


   /**
    * Soft wrap
    *
    * Print an array of data separated by $elementSeparator within the same line and only
    * print $perline elements per line terminated each line with $lineEnding (and an implicit \n).
    *
    * Set $indent to indent //subsequent// lines (i.e. not the first)
    *
    * @param array  $data
    * @param int    $perline
    * @param string $elementSeparator
    * @param string $lineEnding
    * @param int    $indent
    * @return string            Scaled / formatted number / type.
    */
    public function softwrap( array $data, int $perline, string $elementSeparator, string $lineEnding, int $indent = 0 ): string {
        if( !( $cnt = count( $data ) ) ) {
            return "";
        }

        $itrn = 0;
        $str  = "";

        foreach( $data as $d ) {
            if( $itrn == $cnt ) {
                break;
            }

            $str .= $d;

            if( ($itrn+1) != $cnt && ($itrn+1) % $perline != 0 ) {
                $str .= $elementSeparator;
            } else if( $itrn > 0 && ($itrn+1) != $cnt && ($itrn+1) % $perline == 0 ) {
                $str .= $lineEnding . "\n" . str_repeat( ' ', $indent );
            }

            $itrn++;
        }

        return $str;
    }

}

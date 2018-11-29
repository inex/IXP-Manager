<?php

namespace IXP\Utils;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Array Utilities
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   Utils
 * @package    IXP\Utils
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ArrayUtilities
{

    /**
     * Reindex an array of objects by a member of that object.
     *
     * Typically used for Doctrine2 collections.
     *
     * @param array     $objects    Array of objects to reindex
     * @param string    $indexFn    The method of the object that will return the new index. Must be a unique key.
     *
     * @return array
     */
    public static function reindexObjects( $objects, $indexFn ){
        $new = [];

        foreach( $objects as $obj )
            $new[ $obj->$indexFn() ] = $obj;

        return $new;
    }


    /**
     * Reorder an array of objects by a member of that object.
     *
     * Typically used for Doctrine2 collections.
     *
     * @param array     $objects        Array of objects to reindex
     * @param string    $orderFn        The method of the object that will return the new ordering index (should be unique!).
     * @param int       $orderParam     Order of the array
     *
     * @return array
     */
    public static function reorderObjects( $objects, $orderFn, $orderParam = SORT_REGULAR ){
        $new = [];

        foreach( $objects as $obj )
            $new[ $obj->$orderFn() ] = $obj;

        ksort( $new, $orderParam );

        return $new;
    }

}

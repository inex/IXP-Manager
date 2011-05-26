<?php


/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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
 * INEX's IXP Manager Framework
 *
 * http://www.inex.ie/
 *
 *  @package INEX_Debug
 */
class INEX_Debug
{


   /**
    * This function will 'dump and die' - it will (if HTML) surround the
    * output with <pre> tags.
    *
    * The dump command is Zend_Debug::dump()
    *
    *
    * @param object $object The variable / object to dump
    * @param bool $html If true (default) surround the output with <pre> tags
    * @author Barry O'Donovan <barryo@inex.ie> 20100323
    */
    public static function dd( $object, $html = true )
    {
        if( $html ) echo '<pre>';
        Zend_Debug::dump( $object );
        if( $html ) echo '</pre>';
        die();
    }


    /**
    * A wrapper and extension for print_r(). The output looks the same in the browser as the output of print_r() in the source, as it turns the pure
    * text output of print_r() into HTML (XHTML).
    *
    * @param mixed $data the data to be printed or returned
    * @param mixed $var_name null if we don't want to display the variable name, otherwise the name of the variable
    * @param boolean $return default false; if true it returns with the result, if true then prints it
    * @param boolean $pAddPre default true adds the '<pre> ... </pre>' tags to the output, useful for HTML output
    * @param boolean $pAddDollarSign default true adds a $ sign to the $var_name if it is set to true
    * @return mixed void (null) or a string
    * @author Roland Huszti <roland@opensolutions.ie>
    * @license Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
    */
    public static function prr($data, $var_name=null, $return=false, $pAddPre=true, $pAddDollarSign=true)
    {
        $vRetVal =  ($pAddPre == true ? "\n<pre>\n" : '') .
                    ($var_name == '' ? '' : ($pAddDollarSign == true ? "\$" : '') . "{$var_name} = ") .
                    print_r($data, true) .
                    ($pAddPre == true ? "\n</pre>\n" : '');


        if ($return === false)
            print $vRetVal;
        else
            return $vRetVal;
    }


    /**
    * Returns with a simplified, easier-to-read version of the result of debug_backtrace() as an associative array.
    *
    * @param void
    * @return array
    */
    public static function compact_debug_backtrace()
    {
        $res = debug_backtrace();
        $ret_val = array();

        foreach($res as $res_val)
        {
            $xyz = array();
            if (isset($res_val['file'])) $xyz['file'] = $res_val['file'];
            if (isset($res_val['line'])) $xyz['line'] = $res_val['line'];
            if (isset($res_val['function'])) $xyz['function'] = $res_val['function'];
            if (isset($res_val['class'])) $xyz['class'] = $res_val['class'];
            if (isset($res_val['object']->name)) $xyz['object'] = $res_val['object']->name;

            $ret_val[] = $xyz;
        }

        return $ret_val;
    }

}



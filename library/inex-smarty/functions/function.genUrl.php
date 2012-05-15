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
 * Generate a Zend Controller URL.
 *
 * This function should be used exclusivily in all Smarty templates so
 * any changes to URL generation can be made here and only here.
 *
 * The associatve array of parameters can include:
 *      controller => index (for example)
 *      action => index (for example)
 *      var1 => value1 (for example)
 * @param array $params The attributes used when calling the function
 * @param object $smarty The Smarty object
 */
function smarty_function_genUrl( $params, &$smarty )
{
    $url = Zend_Controller_Front::getInstance()->getBaseUrl();
        
    if( isset( $params['controller'] ) )
    {
        $url .= "/{$params['controller']}";
        
        if( isset( $params['action'] ) )
        {
            $url .= "/{$params['action']}";
            
            foreach( $params as $var => $value )
            {
                if( $var != 'controller' && $var != 'action')
                    $url .= "/$var/$value";
            }
        }
    }
    
    return $url;
}


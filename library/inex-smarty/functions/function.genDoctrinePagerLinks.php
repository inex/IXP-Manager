<?php


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
 * To create the links, this Smarty function needs to use another
 * and we should have a fatal error if we cannot load it.
 */
require_once( 'function.genUrl.php' );

/**
 * Create Doctrine Pagination Links
 *
 * This function uses genUrl() to create the pagination links and so the
 * paramaters passed to that should also be included here as well as:
 *
 *      pager => the Doctrine pagination object
 *      showPageCount => show an extra row of: Page x of y
 *
 * @param array $params The attributes used when calling the function
 * @param object $smarty The Smarty object
 */
function smarty_function_genDoctrinePagerLinks( $params, &$smarty )
{
    // remove non-URL parameters from the parameters array
    $pager = $params['pager'];
    unset( $params['pager'] );

    $showPageCount = isset( $params['showPageCount'] ) && $params['showPageCount'] ? true : false;

    $baseUrl = smarty_function_genUrl( array(), $smarty );

    $links = '<table class="pagination"><tr>';

    // number of columns in the pagination table
    $numCols = 4; // always first, prev, next, last

    $params['p'] = $pager->getFirstPage();
    if( $pager->getPage() == $params['p'] )
        $links .= '<td><img src="' . $baseUrl . '/images/arrow-left-double.png" width="16" height="16" alt="|&lt;" title="Start" /></td>';
    else
        $links .= '<td><a href="' . smarty_function_genUrl( $params, $smarty ) . '"><img src="' . $baseUrl . '/images/arrow-left-double.png" width="16" height="16" alt="|&lt;" title="Start" /></a></td>';

    $params['p'] = $pager->getPreviousPage();
    if( $pager->getPage() == $params['p'] )
        $links .= '<td><img src="' . $baseUrl . '/images/arrow-left.png" width="16" height="16" alt="&lt;" title="Previous Page" /></td>';
    else
        $links .= '<td><a href="' . smarty_function_genUrl( $params, $smarty ) . '"><img src="' . $baseUrl . '/images/arrow-left.png" width="16" height="16" alt="&lt;" title="Previous Page" /></a></td>';


    $pagerRange = new Doctrine_Pager_Range_Sliding(
        array(
            'chunk' => 5 // Chunk length
        ),
        $pager
    );

    foreach( $pagerRange->rangeAroundPage() as $p )
    {
        if( $p == $pager->getPage() )
            $links .= "<td>$p</td>";
        else
        {
            $params['p'] = $p;
            $links .= '<td><a href="' . smarty_function_genUrl( $params, $smarty ) . '">' . $p . '</a></td>';
        }

        $numCols++;
    }

    $params['p'] = $pager->getNextPage();
    if( $pager->getPage() == $params['p'] )
        $links .= '<td><img src="' . $baseUrl . '/images/arrow-right.png" width="16" height="16" alt="&gt;" title="Next Page" /></td>';
    else
        $links .= '<td><a href="' . smarty_function_genUrl( $params, $smarty ) . '"><img src="' . $baseUrl . '/images/arrow-right.png" width="16" height="16" alt="&gt;|" title="Next Page" /></a></td>';

    $params['p'] = $pager->getLastPage();
    if( $pager->getPage() == $params['p'] )
        $links .= '<td><img src="' . $baseUrl . '/images/arrow-right-double.png" width="16" height="16" alt="&gt;" title="Last Page" /></td>';
    else
        $links .= '<td><a href="' . smarty_function_genUrl( $params, $smarty ) . '"><img src="' . $baseUrl . '/images/arrow-right-double.png" width="16" height="16" alt="&gt;|" title="Last Page" /></a></td>';

    $links .= '</tr>';

    if( $showPageCount )
    {
        $links .= "\n<tr><td align=\"center\" colspan=\"{$numCols}\">Page " . $pager->getPage() . " of " . $pager->getLastPage() . "</td></tr>\n";
    }

    $links .= '</table>';

    return $links;
}



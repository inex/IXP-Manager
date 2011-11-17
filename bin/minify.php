#!/usr/bin/env php
<?php

/**
 * Copyright (c) 2010 - 2011 Open Source Solutions Limited <http://www.opensolutions.ie/>
 * All rights reserved.
 * 
 * JS and CSS Minifier
 *
 * Released under the BSD License.
 * 
 * Copyright (c) 2011, Open Source Solutions Limited, Dublin, Ireland <http://www.opensolutions.ie>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted 
 * provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of 
 *    conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice, this list 
 *    of conditions and the following disclaimer in the documentation and/or other materials 
 *    provided with the distribution.
 *    
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS 
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL 
 * THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

defined( 'APPLICATION_PATH' ) || define( 'APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application' ) );

$whatToCompress = 'all';

if( in_array( 'css', $argv ) && !in_array( 'js', $argv ) )
    $whatToCompress = 'css';

if( in_array( 'js', $argv ) && !in_array( 'css', $argv ) )
    $whatToCompress = 'js';

if( in_array( $whatToCompress, array( 'all', 'js' ) ) )
{
    print "\n\nMinifying '../public/js' ";

    $files = glob( APPLICATION_PATH . '/../public/js/*.js' );
    sort( $files, SORT_STRING );

    $numFiles = sizeof( $files );
    $count = 0;

    foreach( $files as $oneFileName )
    {
        $count++;

        print '.';

        exec(   "java -jar " . APPLICATION_PATH . "/../bin/compiler.jar --compilation_level SIMPLE_OPTIMIZATIONS --warning_level QUIET" .
                " --js {$oneFileName} --js_output_file " . APPLICATION_PATH . "/../public/js-min/" . basename( $oneFileName )
        );
    }

    $mergedJs = '';

    /* MAINTAIN THIS LIST AND MAKE SURE IT IS IN SYNC WITH HEADER_COMMON_BASE.PHTML */
    $filesToMerge = array(
                        'phpjs.js',
                        'functions.js',
                        'jquery.js',
                        'jquery.datatables.js',
                        'jquery.datatables.ext.js',
                        'jquery.ui.js',
                        'jquery.ui.stars.js',
                        'jquery.colorbox.js',
                        'oss_tooltip.js',
                        'toggle_accordion.js'
                    );

    foreach( $filesToMerge as $fileName )
        $mergedJs .= file_get_contents( "../public/js-min/{$fileName}" );

    file_put_contents( '../public/js-min/javascript.js', $mergedJs );

    print ' done';
}

if( in_array( $whatToCompress, array( 'all', 'css' ) ) )
{
    // --------- WINI CSS ---------

    print "\nminifying '../public/css' ";

    $files = glob( '../public/css/*.css' );
    sort( $files, SORT_STRING );

    $numFiles = sizeof( $files );
    $count = 0;

    foreach( $files as $oneFileName )
    {
        $count++;

        print '.';

        exec( "java -jar yuicompressor.jar {$oneFileName} -o ../public/css-min/" . basename( $oneFileName ) . " -v --charset utf-8" );
    }

    $mergedCss = '';

    /* MAINTAIN THIS LIST AND MAKE SURE IT IS IN SYNC WITH HEADER_COMMON_BASE.PHTML */
    $filesToMerge = array(
                        'oss.css',
                        'jquery-ui.css',
                        'colorbox.css',
                        'oss_jquery_datatable.css',
                        'sfmenu.css',
                        'jquery.ui.stars.css',
                        'frontend.css'
                    );

    foreach( $filesToMerge as $fileName )
        $mergedCss .= file_get_contents( "../public/css-min/{$fileName}" );

    file_put_contents( '../public/css-min/stylesheet.css', $mergedCss );

    print ' done';

    // --------- CSS subdirs ---------

    $dirs = glob( '../public/css/*', GLOB_ONLYDIR );
    sort( $dirs, SORT_STRING );

    foreach( $dirs as $dirKey => $dirName )
    {
        print "\nminifying '{$dirName}' ";

        $files = glob( "{$dirName}/*.css" );
        sort( $files, SORT_STRING );

        $numFiles = sizeof( $files );
        $count = 0;

        foreach( $files as $oneFileName )
        {
            $count++;

            print '.';

            exec( "java -jar yuicompressor.jar {$oneFileName} -o " . str_replace( '/css/', '/css-min/', $dirName ) . '/' . basename( $oneFileName ) . " -v --charset utf-8" );
        }

        print ' done';
    }
}

print "\n\n";

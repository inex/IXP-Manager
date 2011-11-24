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
    print "\n\nMinifying '../public/js':\n\n";

    $files = glob( APPLICATION_PATH . '/../public/js/[0-9][0-9][0-9]-*.js' );
    sort( $files, SORT_STRING );

    $numFiles = sizeof( $files );
    $count = 0;

    foreach( $files as $oneFileName )
    {
        $count++;

        print "    [{$count}] " . basename( $oneFileName ) . " => min." . basename( $oneFileName ) . "\n";

        exec(   "java -jar " . APPLICATION_PATH . "/../bin/compiler.jar --compilation_level SIMPLE_OPTIMIZATIONS --warning_level QUIET" .
                " --js {$oneFileName} --js_output_file " . APPLICATION_PATH . "/../public/js/min." . basename( $oneFileName )
        );
    }

    $mergedJs = '';

    print "\n    Combining...";
    foreach( $files as $fileName )
        $mergedJs .= file_get_contents( "../public/js/min." . basename( $fileName) );

    file_put_contents( '../public/js/min.bundle.js', $mergedJs );

    print " done\n\n";
}

if( in_array( $whatToCompress, array( 'all', 'css' ) ) )
{

    print "\nMinifying '../public/css':\n";

    $files = glob( '../public/css/[0-9][0-9][0-9]-*.css' );
    sort( $files, SORT_STRING );

    $numFiles = sizeof( $files );
    $count = 0;

    foreach( $files as $oneFileName )
    {
        $count++;

        print "    [{$count}] " . basename( $oneFileName ) . " => min." . basename( $oneFileName ) . "\n";
        
        exec( "java -jar yuicompressor.jar {$oneFileName} -o ../public/css/min." . basename( $oneFileName ) . " -v --charset utf-8" );
    }

    $mergedCss = '';

    print "\n    Combining...";
    foreach( $files as $fileName )
        $mergedCss .= file_get_contents( "../public/css/min." . basename( $fileName ) );

    file_put_contents( '../public/css/min.bundle.css', $mergedCss );

    print ' done\n\n';
}

print "\n\n";

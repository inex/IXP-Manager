<?php

declare( strict_types = 1 );

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Exception;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use IXP\Exceptions\Utils\DotEnvParserException;
use IXP\Utils\DotEnv\DotEnvContainer;
use IXP\Utils\DotEnv\DotEnvParser;
use IXP\Utils\DotEnv\DotEnvWriter;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

/**
 * .env file configurator Controller
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SettingsController extends Controller
{
    protected array $fe_settings;
    
    
    public function __construct()
    {
        $this->fe_settings = config( 'ixp_fe_settings' );
    }
    
    /**
     * Collect rules from the fe_settings configuration file which contains
     * arrays of 'panels' for the UI.
     *
     * @return array
     */
    private function gatherRules(): array
    {
        $rules = [];
        
        foreach( $this->fe_settings[ "panels" ] as $panel ) {
            foreach( $panel[ "fields" ] as $label => $field ) {
                if( isset( $field[ "rules" ] ) && $field[ "rules" ] !== '' ) {
                    $rules[ $label ] = $field[ "rules" ];
                }
            }
        }
        
        return $rules;
    }
    
    
    /**
     * Display the form to edit an object
     */
    protected function index(): View
    {
        try {
            $this->checkIfDotEnvIsCompatible();
        } catch( Exception $e ) {
            
            AlertContainer::push( $e->getMessage(), Alert::DANGER );
            
            return view( 'settings.compatibility' )->with( [
                'exception' => $e,
            ] );
        }
        
        return view( 'settings.index' )->with( [
            'settings' => $this->fe_settings,
            'rules'    => [], //$this->gatherRules(),
        ] );
    }
    
    
    /**
     * @throws DotEnvParserException
     * @throws Exception
     */
    private function checkIfDotEnvIsCompatible()
    {
        if( !file_exists( base_path( '.env' ) ) ) {
            throw new Exception( "The .env file is missing. Please create it and try again." );
        }
        
        if( !is_writable( base_path( '.env' ) ) ) {
            throw new Exception( "The .env file is can not be written to. Please check the file permissions and try again." );
        }
        
        if( !( $env = file_get_contents( base_path( '.env' ) ) ) ) {
            throw new Exception( "The .env file is empty. Please add some settings and try again." );
        }
        
        new DotEnvParser( $env )->parse();
    }
    
    /**
     * @throws DotEnvParserException
     * @throws Exception
     */
    private function loadDotEnv(): DotEnvContainer
    {
        if( !file_exists( base_path( '.env' ) ) ) {
            throw new Exception( "The .env file is missing. Please create it and try again." );
        }
        
        if( !( $env = file_get_contents( base_path( '.env' ) ) ) ) {
            throw new Exception( "The .env file is empty. Please add some settings and try again." );
        }
        
        
        return new DotEnvContainer( new DotEnvParser( $env )->parse()->settings() );
    }
    
    /**
     * @throws Exception
     */
    private function saveDotEnv( string $dotEnv ): void
    {
        if( !file_exists( base_path( '.env' ) ) ) {
            throw new Exception( "The .env file is missing. Please create it and try again." );
        }
        
        if( !is_writable( base_path( '.env' ) ) ) {
            throw new Exception( "The .env file is can not be written to. Please check the file permissions and try again." );
        }
        
        if( !( file_put_contents( base_path( '.env' ), $dotEnv ) ) ) {
            throw new Exception( "Could not write to the .env file. Please check the file permissions and try again." );
        }
    }
    
    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update( Request $request ): RedirectResponse
    {
        $validated = $request->validate( $this->gatherRules() );
        
        try {
            // only interested in saving settings where the value has changed
            $dotenv = $this->loadDotEnv();
            
            foreach( $this->fe_settings[ "panels" ] as $panel ) {
                foreach( $panel[ "fields" ] as $fname => $fconfig ) {

                    $orig = config( $fconfig[ "config_key" ] );
                    
                    if( isset( $fconfig[ "invert" ] ) && $fconfig[ "invert" ] ) {
                        $validated[ $fname ] = $validated[ $fname ] === "1" ? "0" : "1";
                    }
                    
                    if( !isset( $validated[ $fname ] ) || $validated[ $fname ] == $orig ) {
                        continue;
                    }

                    // update dotenv container
                    if( $dotenv->isset( $fconfig[ 'dotenv_key' ] ) ) {
                        $dotenv->updateValue( $fconfig[ 'dotenv_key' ], $validated[ $fname ] );
                    } else {
                        // include blank line
                        $dotenv->set( null, null, null );
                        $dotenv->set( $fconfig[ 'dotenv_key' ], $validated[ $fname ] );
                    }
                    
                }
            }

            $this->saveDotEnv( new DotEnvWriter( $dotenv->settings() )->generateContent() );
            
        } catch( DotEnvParserException|Exception $e ) {
            AlertContainer::push( $e->getMessage(), Alert::DANGER );
            return redirect()->back();
        }
        
        AlertContainer::push( 'Settings have been successfully updated', Alert::SUCCESS );
        return redirect( route( 'settings@index') );
    }
    
}

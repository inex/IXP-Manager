<?php

namespace IXP\Console\Commands\Rir;

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Cache, Mail;

use IXP\Console\Commands\Command;

use IXP\Support\Facades\RipeRestApi;
use IXP\Tasks\Rir\Generator as RirGenerator;

/**
 * RIR Update command
 *
 * @see https://docs.ixpmanager.org/latest/features/rir-objects/
 * @author      Barry O'Donovan     <barry@opensolutions.ie>
 * @author      Yann Robin          <yann@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Rir
 * @copyright   Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class GenerateObject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'rir:generate-object
                        {object             : The RIR object template to use}
                        {--send-email       : Rather than printing to screen, sends and email for updating a RIR automatically}
                        {--update-ripe-db   : Update the RIPE database with the generated object, using the RIPE REST API}
                        {--force            : Send email/update RIPE even if the generated object matches the cached version}
                        {--to=              : The email address to send the object to (if not specified then uses IXP_API_RIR_EMAIL_TO)}
                        {--from=            : The email address from which the email is sent (if not specified, tries IXP_API_RIR_EMAIL_FROM and then defaults to IDENTITY_EMAIL)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will generate and display a RIR object (and optionally send by email/post to RIPE)';

    /**
     * Execute the console command.
     *
     * @throws
     */
    public function handle(): int
    {
        if( $this->option( "update-ripe-db" ) && $this->option( "send-email" ) ) {
            $this->error( "Cannot update RIPE database and send email at the same time." );
            return 1;
        }
        
        $gen = new RirGenerator( $this->argument ('object' ) );

        $obj = $gen->generate();
        
        $key = 'rir-object-' . $this->argument ('object' );
        $cobj = Cache::store('file' )->get( $key );
        
        if( !$this->isVerbosityQuiet() && !$this->option( "force" ) && $obj === $cobj ) {
            $this->warn( "Generated RIR object is identical to cached version, use --force to update anyway." );
        }
        
        if( $this->option( "send-email" ) && ( $this->option( "force" ) || $obj !== $cobj ) ) {
            $this->sendEmail( $key, $obj );
        } else if( $this->option( "update-ripe-db" ) && ( $this->option( "force" ) || $obj !== $cobj ) ) {
            $this->updateRipeDb( $gen->generateJson() );
        } else if( !$this->option( "send-email" ) && !$this->option( "update-ripe-db" ) ) {
            echo $obj;
        }
        
        if( $obj !== $cobj ) {
            Cache::store('file')->forever( $key, $obj );
        }
        
        return 0;
    }
    
    /**
     * Update the RIPE database with the generated object, using the RIPE REST API
     *
     * @param array $jsonData
     * @return void
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    private function updateRipeDb( array $jsonData ): void {
        
        $response = RipeRestApi::updateObject( $jsonData );
        
        $errors = false;
        
        if( $response->json( 'errormessages.errormessage') ) {
            foreach( $response->json( 'errormessages.errormessage') as $em ) {
                
                $msg = "[" . $response->status() . "] " . vsprintf( $em['text'], array_map(
                            function( $v ) { return $v['value']; }, $em['args'] ?? []
                ) );

                if( $em['severity'] === 'Warning' ) {
                    $this->warn( 'Warning ' . $msg );
                } else {
                    $this->error( 'Error ' . $msg );
                    $errors = true;
                }
            }

            if( $errors ) {
                exit( -1 );
            }
        }
        
        if( !$this->isVerbosityQuiet() ) {
            $this->info( "RIPE DB Updated" );
        }
    }
    
    /**
     * Send an email with the generated object
     * @param string $key
     * @param string $obj
     * @param string|null $cobj
     * @return void
     */
    private function sendEmail( string $key, string $obj, ?string $cobj ): void {
        if( !$this->option( "to" ) && !config( 'ixp_api.rir.email.to' )   ){
            $this->error( "Please specify the TO email address" );
            exit( -1 );
        }
        
        Mail::raw( $obj, function( $m ) {
            $m->to( $this->checkEmail( 'to', $this->option( "to" ) ?? config( 'ixp_api.rir.email.to' ) ) )
                ->from( $this->checkEmail( 'from', ( $this->option( "from" ) ?: config( 'ixp_api.rir.email.from' ) ) ?: config( 'mail.from.address' ) ) )
                ->subject( "Changes to {$this->argument ('object' )} via IXP Manager" );
        } );
        
        if( !$this->isVerbosityQuiet() ) {
            $this->info( "Email sent." );
        }
        
    }
    
    
    /**
     * Validates the provided email address
     */
    private function checkEmail( string $w, string $e ): string
    {
        if( filter_var( $e, FILTER_VALIDATE_EMAIL ) ) {
            return $e;
        }

        $this->error( "Invalid $w email address: $}" );
        exit( -1 );
    }
}
<?php

namespace IXP\Console\Commands\Rir;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Tasks\Rir\Generator as RirGenerator;

/**
 * RIR Update command
 *
 * @see https://docs.ixpmanager.org/features/rir-objects/
 * @author      Yann Robin          <yann@islandbridgenetworks.ie>
 * @author      Barry O'Donovan     <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Rir
 * @copyright   Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
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
                        {object       : The RIR object template to use}
                        {--send-email : Rather than printing to screen, sends and email for updating a RIR automatically}
                        {--force      : Send email even if it matches the cached version}
                        {--to=        : The email address to send the object to (if not specified then uses IXP_API_RIR_EMAIL_TO)}
                        {--from=      : The email address from which the email is sent (if not specified, tries IXP_API_RIR_EMAIL_FROM and then defaults to IDENTITY_EMAIL)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will generate and display a RIR object (and optionally send by email)';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle()
    {
        $gen = new RirGenerator( $this->argument ('object' ) );

        $obj = $gen->generate();

        $key = 'rir-object-' . $this->argument ('object' );
        $cobj = Cache::store('file' )->get( $key );

        if( $this->option( "send-email" ) && ( $this->option( "force" ) || $obj !== $cobj ) ) {
            if( !$this->option( "to" ) && !config( 'ixp_api.rir.email.to' )   ){
                $this->error( "Please specify the TO email address" );
                exit( -1 );
            }

            Mail::raw( $obj, function( $m ) {
                $m->to( $this->checkEmail( 'to', $this->option( "to" ) ?? config( 'ixp_api.rir.email.to' ) ) )
                    ->from( $this->checkEmail( 'from', ( $this->option( "from" ) ?? config( 'ixp_api.rir.email.from' ) ) ?? config( 'mail.from.address' ) ) )
                    ->subject( "Changes to {$this->argument ('object' )} via IXP Manager" );
            } );

            if( !$this->isVerbosityQuiet() ) {
                $this->info( "Email sent." );
            }

            if( $obj !== $cobj ) {
                Cache::store('file')->forever( $key, $obj );
            }

        } else if( !$this->option( "send-email" ) ) {
            echo $obj;
        }

        return 0;
    }

    /**
     *
     * @param string $w
     * @param string $e
     *
     * @return string|null
     */
    private function checkEmail( string $w, string $e ): ?string
    {
        if( filter_var( $e, FILTER_VALIDATE_EMAIL ) ) {
            return $e;
        }

        $this->error( "Invalid {$w} email address: {$e}" );
        exit( -1 );
    }
}
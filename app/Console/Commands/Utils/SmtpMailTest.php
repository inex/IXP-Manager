<?php

namespace IXP\Console\Commands\Utils;

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
use Illuminate\Support\Facades\Mail;

use IXP\Console\Commands\Command as IXPCommand;

use IXP\Mail\Utils\SmtpTest as SmtpTestMail;

/**
 * Class SmtpMailTest - test sending emails
 *
 * @see https://docs.ixpmanager.org/usage/email/
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Utils
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SmtpMailTest extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:smtp-mail-test {email : Email address to be sent to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to check SMTP mail settings';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument( 'email' );

        $this->info( "This utility allows you to test your SMTP settings to verify that IXP Manager can send email." );

        if( config( 'mail.default' ) !== 'smtp' ) {
            $this->error( "The mail driver ('MAIL_MAILER' in your .env file) is not set to \"smtp\". " );
            $this->error( "SMTP is the only officially supported driver in IXP Manager. " );
            return -1;
        }

        if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $this->error( "Invalid email address provided - {$email}" );
            return -2;
        }

        $this->info( "\nTesting using the following parameters:\n" );

        $this->table( [], [
            [ 'Driver', config( 'mail.default' ) ],
            [ 'Host', config( 'mail.mailers.smtp.host' ) ],
            [ 'Port', config( 'mail.mailers.smtp.port' ) ],
            [ 'Encryption', config( 'mail.mailers.smtp.encryption', '(none)' ) ],
            [ 'Username', config( 'mail.mailers.smtp.username', '(none)' ) ],
            [ 'Password', config( 'mail.mailers.smtp.password', '(none)' ) ],
            [ 'From Name', config( 'identity.name' ) ],
            [ 'From Email', config( 'identity.email' ) ],
        ] );

        $this->line( "\n\n" );

        $this->info( "Trying to send email...\n" );

        if( $this->getOutput()->isVerbose() ) {
            $mail = new SmtpTestMail( true );
        } else {
            $mail = new SmtpTestMail;
        }

        try {
            Mail::to( $email )->send( $mail );
            $this->info( "SUCCESS - email has been sent." );
        } catch( \Exception $e ) {

            $this->error( "FAILED TO SEND EMAIL!" );
            $this->line( "\n\n" );
            $this->line( "Exception thrown: " . get_class( $e ) );
            $this->line( "Error: " . $e->getMessage() );
            $this->line( "File: " . $e->getFile() );
            $this->line( "Line: " . $e->getLine() );

            $this->line( "\n" );

            if( $this->getOutput()->isVerbose() ) {
                echo $e->getTraceAsString();
            } else {
                $this->warn( "If you plan to request support from the IXP Manager team, please rerun this test with the -v (verbose) "
                    . "option and paste the complete output to an online pastebin such as https://pastebin.ibn.ie/. Please also ensure "
                    . "you have read the documentation for configuring email at https://docs.ixpmanager.org/usage/email/. Lastly, if "
                    . "you have configured a username and password, PLEASE remove these before pasting online!"
                );
            }
        }

        if( $this->getOutput()->isVerbose() ) {
            $this->line( "\n\n" . str_repeat( '=', 40 ) . "\nSMTP Dialog:\n\n" );
            $this->line( $mail->logger()->dump() );
        }
    }
}
<?php

namespace IXP\Console\Commands\Utils;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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


use D2EM;

use Entities\User as UserEntity;

use IXP\Console\Commands\Command as IXPCommand;


/**
 * Class ConvertPlaintextPasswords - convert plaintext passwords to bcrypt
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Utils
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConvertPlaintextPasswords extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:convert-plaintext-passwords {--force : Write to database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert plaintext passwords to Bcrypt.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $chunk = 0;

        /** @var UserEntity $user */
        foreach( D2EM::getRepository( UserEntity::class )->findAll() as $user ) {

            if( substr( $user->getPassword(), 0, 2 ) == '$2' ) {
                $this->error( "Skipping {$user->getId()}/{$user->getUsername()} - looks like password might not be plaintext?" );
                continue;
            }

            $user->setPassword( password_hash( $user->getPassword(), PASSWORD_BCRYPT, [ 'cost' => 10 ] ) );
            $this->line( "Setting {$user->getId()}/{$user->getUsername()} - {$user->getPassword()}" );

            if( $this->option( 'force' ) && ++$chunk % 20 == 0 ) {
                D2EM::flush();
                $this->info('Chunked 20 to database');
            }
        }

        if( $this->option( 'force' ) ) {
            D2EM::flush();
            $this->info("Chunked remainder to database (total {$chunk})");
        } else {
            $this->warn( 'Not saved to database! Use --force to save to database.' );
        }

        return 0;
    }
}

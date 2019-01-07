<?php

/**
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Database\Seeder;

/**
 * Seed the database contact groups table
 */
class ContactGroups extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contactRoles = [ 'Billing', 'Technical', 'Admin', 'Marketing' ];

        foreach( $contactRoles as $name )
        {
            $e = new \Entities\ContactGroup();
            $e->setName( $name );
            $e->setDescription( sprintf( "Contact role for %s matters", strtolower( $name ) ) );
            $e->setType( \Entities\ContactGroup::TYPE_ROLE );
            $e->setActive( true );
            $e->setLimitedTo( 0 );
            $e->setCreated( new DateTime() );
            D2EM::persist( $e );
        }

        D2EM::flush();
    }
}

<?php

namespace IXP\Console\Commands\Helpdesk;

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

use IXP\Models\{
    Contact,
    Customer,
    User
};

use IXP\Services\Helpdesk\ApiException;
 /**
  * Artisan command to add/update IXP Manager customers and contacts to a helpdesk system
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin <barry@islandbridgenetworks.ie>
  * @category   Helpdesk
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class UpdateOrganisations extends HelpdeskCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'helpdesk:update-organisations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the organisation/contacts records on the helpdesk';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** FIXME FIXME-YR update this later */
        $this->info( "Command not ready to use yet" );
        return -1;
//        if( $this->getOutput()->isVerbose() ) {
//            $this->info( "{$this->name} :: Starting..." );
//        }
//
//        $contactemails = [];
//
//        // iterate over all customers and add/update customer and its contacts
//        foreach( Customer::all() as $cust ) {
//            if( $this->getOutput()->isVeryVerbose() )
//                $this->info( "{$this->name} :: processing {$cust->name}..." );
//
//            if( $cust->typeInternal() ){
//                continue;
//            }
//
//            if( $org = $this->updateOrganisation( $cust ) ) {
//                foreach( $cust->contacts as $contact ) {
//                    // weed out duplicates:
//                    if( !in_array( $contact->email, $contactemails, false ) ){
//                        $contactemails[] = $contact->email;
//                    }
//
//                    $this->updateContact( $cust, $contact, $org );
//                }
//            }
//        }
//
//        if( $this->getOutput()->isVerbose() ) {
//            $this->info( "{$this->name} :: Finished" );
//        }
    }


    /**
     * @param Customer $cust
     *
     * @return Customer|bool
     *
     * @throws ApiException
     */
    protected function updateOrganisation( Customer $cust )
    {
        $helpdesk = $this->getHelpdesk();
        if( $org = $helpdesk->organisationFind( $cust->id ) ) {
            if( $helpdesk->organisationNeedsUpdating( $cust, $org ) ) {
                if( $helpdesk->organisationUpdate( $org->helpdesk_id, $cust ) ){
                    $this->info( "{$this->name} :: updated {$cust->name}" );
                } else {
                    $this->error( "{$this->name} :: could not update {$cust->name}" );
                    return false;
                }
            }
            return $org;
        }

        // create it:
        if( $org = $helpdesk->organisationCreate( $cust ) ) {
            $this->info( "{$this->name}} :: created {$cust->name}" );
            $this->createNetworkUsers( $cust, $org );
            return $org;
        }
        $this->error( "{$this->name} :: could not create {$cust->name}" );
        return false;
    }

    /**
     * @param Customer  $cust
     * @param Contact   $contact
     * @param $org
     * @return false|void
     * @throws ApiException
     */
    protected function updateContact( Customer $cust, Contact $contact, $org )
    {
        if( $contact->getUser() && $contact->getUser()->getPrivs() !== User::AUTH_CUSTUSER )
            return;

        if( $this->getOutput()->isVeryVerbose() ){
            $this->info( "{$this->name} :: processing {$cust->name} :: contact {$contact->name}..." );
        }

        if( $user = $this->getHelpdesk()->userFind( $contact->id ) ) {
            if( $this->getHelpdesk()->contactNeedsUpdating( $contact, $user ) ) {
                if( $this->getHelpdesk()->userUpdate( $user->helpdesk_id, $contact ) ){
                    $this->info( "{$this->name} :: updated {$contact->name}" );
                } else {
                    $this->error( "{$this->name} :: could not update {$contact->name}" );
                    return false;
                }
            }
        } else {
            try {
                // create it:
                if( $user = $this->getHelpdesk()->userCreate( $contact, $org->helpdesk_id ) ){
                    $this->info( "{$this->name}} :: created {$contact->name}" );
                } else {
                    $this->error( "{$this->name} :: could not create {$contact->name}" );
                    return false;
                }
            } catch ( ApiException $e ) {
                if( $e->userIsDuplicateEmail() ) {
                    if( $this->getOutput()->isVerbose() ){
                        $this->info( "{$this->name} :: skipping contact {$contact->name}/{$contact->id} as email is a duplicate on helpdesk..." );
                    }
                    return;
                }
                throw $e;
            }
        }
    }

    /**
     * @param Customer $cust
     * @param $org
     */
    protected function createNetworkUsers( Customer $cust, $org ): void
    {
        $noc = Contact::create([
            'name'      => $cust->name . ' - NOC',
            'email'     => $cust->nocemail,
            'mobile'    => $cust->nocphone,
        ]);

        $peering = Contact::create([
            'name'      => $cust->name . ' - Peering',
            'email'     => $cust->peeringemail,
        ]);

        try {
            $this->getHelpdesk()->userCreate( $noc, $org->helpdesk_id );
            $this->info( "{$this->name}} :: created {$noc->name}" );
        } catch( \Exception $e ) {}

        try {
            $this->getHelpdesk()->userCreate( $peering, $org->helpdesk_id );
            $this->info( "{$this->name}} :: created {$peering->name}" );
        } catch( \Exception $e ) {}
    }
}
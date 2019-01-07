<?php namespace IXP\Console\Commands\Helpdesk;

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


use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Output\OutputInterface;

use IXP\Services\Helpdesk\ApiException;

use D2EM;


 /**
  * Artisan command to add/update IXP Manager customers and contacts to a helpdesk system
  *
  * @author     Barry O'Donovan <barry@opensolutions.ie>
  * @category   Helpdesk
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class UpdateOrganisations extends HelpdeskCommand {

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
        if( $this->getOutput()->isVerbose() ) $this->info( "{$this->name} :: Starting..." );

        $contactemails = [];

        // iterate over all customers and add/update customer and its contacts
        foreach( D2EM::getRepository( 'Entities\Customer' )->findAll() as $cust ) {

            if( $this->getOutput()->isVeryVerbose() )
                $this->info( "{$this->name} :: processing {$cust->getName()}..." );

            if( $cust->isTypeInternal() )
                continue;

            if( $org = $this->updateOrganisation( $cust ) ) {

                foreach( $cust->getContacts() as $contact ) {

                    // weed out duplicates:
                    if( in_array( $contact->getEmail(), $contactemails ) )
                        continue;
                    else
                        $contactemails[] = $contact->getEmail();

                    $this->updateContact( $cust, $contact, $org );
                }
            }
        }

        if( $this->getOutput()->isVerbose() ) $this->info( "{$this->name} :: Finished" );
    }



    protected function updateOrganisation( $cust )
    {
        if( $org = $this->getHelpdesk()->organisationFind( $cust->getId() ) )
        {
            if( $this->getHelpdesk()->organisationNeedsUpdating( $cust, $org ) )
            {
                if( $this->getHelpdesk()->organisationUpdate( $org->helpdesk_id, $cust ) )
                    $this->info( "{$this->name} :: updated {$cust->getName()}" );
                else
                {
                    $this->error( "{$this->name} :: could not update {$cust->getName()}" );
                    return false;
                }
            }
            return $org;
        }
        else
        {
            // create it:
            if( $org = $this->getHelpdesk()->organisationCreate( $cust ) ) {
                $this->info( "{$this->name}} :: created {$cust->getName()}" );
                $this->createNetworkUsers( $cust, $org );
                return $org;
            }
            else
            {
                $this->error( "{$this->name} :: could not create {$cust->getName()}" );
                return false;
            }
        }
    }

    protected function updateContact( $cust, $contact, $org )
    {
        if( $contact->getUser() && $contact->getUser()->getPrivs() != \Entities\User::AUTH_CUSTUSER )
            return;

        if( $this->getOutput()->isVeryVerbose() )
            $this->info( "{$this->name} :: processing {$cust->getName()} :: contact {$contact->getName()}..." );

        if( $user = $this->getHelpdesk()->userFind( $contact->getId() ) )
        {
            if( $this->getHelpdesk()->contactNeedsUpdating( $contact, $user ) )
            {
                if( $this->getHelpdesk()->userUpdate( $user->helpdesk_id, $contact ) )
                    $this->info( "{$this->name} :: updated {$contact->getName()}" );
                else
                {
                    $this->error( "{$this->name} :: could not update {$contact->getName()}" );
                    return false;
                }
            }
        }
        else
        {
            try
            {
                // create it:
                if( $user = $this->getHelpdesk()->userCreate( $contact, $org->helpdesk_id ) )
                    $this->info( "{$this->name}} :: created {$contact->getName()}" );
                else
                {
                    $this->error( "{$this->name} :: could not create {$contact->getName()}" );
                    return false;
                }
            }
            catch ( ApiException $e )
            {
                if( $e->userIsDuplicateEmail() ) {
                    if( $this->getOutput()->isVerbose() )
                        $this->info( "{$this->name} :: skipping contact {$contact->getName()}/{$contact->getId()} as email is a duplicate on helpdesk..." );
                    return;
                }

                throw $e;
            }
        }
    }

    protected function createNetworkUsers( $cust, $org )
    {
        $noc = new \Entities\Contact;
        $noc->setName( $cust->getName() . ' - NOC' );
        $noc->setEmail( $cust->getNocemail() );
        $noc->setMobile( $cust->getNocphone() );

        $peering = new \Entities\Contact;
        $peering->setName( $cust->getName() . ' - Peering' );
        $peering->setEmail( $cust->getPeeringemail() );

        try {
            $this->getHelpdesk()->userCreate( $noc, $org->helpdesk_id );
            $this->info( "{$this->name}} :: created {$noc->getName()}" );
        } catch( \Exception $e ) {}

        try {
            $this->getHelpdesk()->userCreate( $peering, $org->helpdesk_id );
            $this->info( "{$this->name}} :: created {$peering->getName()}" );
        } catch( \Exception $e ) {}
    }

}

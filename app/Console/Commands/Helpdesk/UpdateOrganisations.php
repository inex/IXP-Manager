<?php namespace IXP\Console\Commands\Helpdesk;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Output\OutputInterface;

use D2EM;

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
    protected $description = 'Update the organisations records on the helpdesk';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if( $this->getOutput()->isVerbose() ) $this->info( "{$this->name} :: Starting..." );

        foreach( D2EM::getRepository( 'Entities\Customer' )->findAll() as $cust ) {

            if( $this->getOutput()->isVeryVerbose() ) $this->info( "{$this->name} :: processing {$cust->getName()}..." );

            if( $cust->isTypeInternal() )
                continue;

            if( $org = $this->getHelpdesk()->organisationFind( $cust->getId() ) ) {
                if( $this->getHelpdesk()->organisationNeedsUpdating( $cust, $org ) ) {
                    if( $this->getHelpdesk()->organisationUpdate( $org->helpdesk_id, $cust ) )
                        $this->info( "{$this->name} :: updated {$cust->getName()}" );
                    else
                        $this->error( "{$this->name} :: could not update {$cust->getName()}" );
                }
            } else { // create it:
                if( $this->getHelpdesk()->organisationsCreate( [ $cust ] ) )
                    $this->info( "{$this->name}} :: created {$cust->getName()}" );
                else
                    $this->error( "{$this->name} :: could not create {$cust->getName()}" );
            }

        }

        if( $this->getOutput()->isVerbose() ) $this->info( "{$this->name} :: Finished" );
    }

}

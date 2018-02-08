<?php

namespace IXP\Console\Commands\Upgrade;

use Illuminate\Console\Command;

use D2EM, DB;

use Entities\{
    ConsoleServer               as ConsoleServerEntity,
    Switcher                    as SwitcherEntity,
    ConsoleServerConnection     as ConsoleServerConnectionEntity
};

class SplitSwitchConserver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'switch:split-console-servers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will split the console servers from the switches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        if( !$this->confirm( 'Are you sure you wish to proceed? This command will split the console servers from the switches '
            . 'Generally, this command should only ever be run once when initially '
            . 'populating the new table.' ) ) {
            return 1;
        }
        $conn = D2EM::getConnection();
     
        foreach( D2EM::getRepository( SwitcherEntity::class )->findBy( [ "switchtype" => SwitcherEntity::TYPE_CONSOLESERVER ]) as $s ) {
            $conn->beginTransaction();
            $conn->transactional(function( $conn ) use ( $s ){

                try{
                    $cs = new ConsoleServerEntity;
                    D2EM::persist( $cs );
                    $cs->setName(           $s->getName()           );
                    $cs->setActive(         $s->getActive()         );
                    $cs->setHostname(       $s->getHostName()       );
                    $cs->setModel(          $s->getModel()          );
                    $cs->setNote(           $s->getNotes()          );
                    $cs->setSerialNumber(   $s->getSerialNumber()   );
                    $cs->setCabinet(        $s->getCabinet()        );
                    $cs->setVendor(         $s->getVendor()         );

                    D2EM::flush();
                    $this->info( 'The console server id:'. $cs->getName().' has been inserted into the database.' );

                    foreach( D2EM::getRepository( SwitcherEntity::class )->getConsoleServerConnections( $s->getId() ) as $csc ){
                        /** @var ConsoleServerConnectionEntity $csc */
                        $csc->setConsoleServer( $cs );
                        $csc->setSwitcher( null );
                        $this->info( 'The console server connection id:' . $csc->getId(). ' name:' . $csc->getDescription() . ' has been linked to the new console server '. $cs->getName() );
                    }
                    $switchInfo = "id:". $s->getId(). " name:" .$s->getName();
                    D2EM::remove( $s );

                    $conn->commit();
                    D2EM::flush();
                    $this->info( 'The switch '. $switchInfo . ' has been deleted from the database ' );


                } catch (Exception $e) {
                    $this->error( $e->getMessage() );
                    $conn->rollBack();
                    $conn->close();
                }
            });
            $this->info( '=========================================' );
        }
        $this->info( 'Migration completed successfully' );
    }
}

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

        // get all the entries form the macaddress table
        DB::table( 'switch' )->where( "switchtype", "2" )->orderBy( 'id' )->chunk( 100, function( $switches ) {

            foreach( $switches as $switch ) {

                /** @var SwitcherEntity $s */
                DB::beginTransaction();

                /** @var VirtualInterfaceEntity $vi */
                $s = D2EM::getRepository( SwitcherEntity::class )->find( $switch->id );
                try{
                    $cs = new ConsoleServerEntity;
                    D2EM::persist( $cs );
                    $cs->setName(           $s->getName()           );
                    $cs->setActive(         $s->getActive()         );
                    $cs->setHostname(       $s->getHostName()       );
                    $cs->setModel(          $s->getModel()          );
                    $cs->setNote(           $s->getNotes()          );
                    $cs->setSerialNumber(   $s->getSerialNumber()   );
                    $cs->setCabinet(    $s->getCabinet()            );
                    $cs->setVendor(     $s->getVendor()             );

                    D2EM::flush();

                    foreach( D2EM::getRepository( SwitcherEntity::class )->getConsoleServerConnections( $s->getId() ) as $csc ){
                        /** @var ConsoleServerConnectionEntity $csc */
                        $csc->setConsoleServer( $cs );
                        $csc->setSwitcher( null );
                    }

                    D2EM::remove( $s );

                    D2EM::flush();

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                }
            }
        });

        $this->info( 'Migration completed successfully' );
    }
}

<?php namespace IXP\Console\Commands;

use Symfony\Component\Console\Output\OutputInterface;
use IXP\Traits\Common;
use Entities\IXP;

class Command extends \Illuminate\Console\Command {

    use Common;

    /**
     * Get whatever IXP was specified on the command line
     *
     * We have not implemented multi-IXP in v4 but we're leaving the framework in
     * place as it's well constructed to allow for it.
     *
     * @param int id A specfic IXP id to load (otherwise command line option or default)
     * @return Entities\IXP
     */
    protected function ixp( $id = null ): IXP {
        // what IXP are we running on here?
        if( $this->multiIXP() ) {
            $this->error( 'Multi IXP support has not been ported to V4 due to no usage.' );
            exit -1;

            //$ixpid = $this->getParam( 'ixp', false );
            //if( !$ixpid || !( $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $ixpid ) ) )
            //    die( "ERROR: Invalid or no IXP specified.\n" );
        } else {
            return d2r( 'IXP' )->getDefault();
        }
    }

    // /**
    //  * Returns true if verbosity is at least: VERBOSITY_QUIET
    //  * @return bool
    //  */
    // protected function isVerbosityQuiet() {
    //     return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_QUIET;
    // }
    //
    // /**
    //  * Returns true if verbosity is at least: VERBOSITY_NORMAL
    //  * @return bool
    //  */
    // protected function isVerbosityNormal() {
    //     return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL;
    // }
    //
    // /**
    //  * Returns true if verbosity is at least: VERBOSITY_VERBOSE
    //  * @return bool
    //  */
    // protected function isVerbosityVerbose() {
    //     return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
    // }
    //
    // /**
    //  * Returns true if verbosity is at least: VERBOSITY_VERY_VERBOSE
    //  * @return bool
    //  */
    // protected function isVerbosityVeryVerbose() {
    //     return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
    // }
    //
    // /**
    //  * Returns true if verbosity is at least: VERBOSITY_DEBUG
    //  * @return bool
    //  */
    // protected function isVerbosityDebug() {
    //     return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG;
    // }

}

<?php namespace IXP\Console\Commands\Grapher;

use App;

use IXP\Console\Commands\Command as IXPCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


class GrapherCommand extends IXPCommand {


    /**
     * As we allow multiple graphing backends, we need to resolve
     * which one we're meant to use here.
     *
     * The order of resolution is:
     *
     * 1. As specified in the `$backend` parameter if not null
     * 2. As per the command line option --backend (if provided)
     * 3. First backend in `configs/grapher.php` `backend` element.
     *
     * @param string $backend|null
     * @return string
     */
    protected function resolveBackend( string $backend = null ): string {
        if( $backend === null ) {
            if( $this->option('backend') ) {
                $backend = $this->option('backend');
            } else if( count( config('grapher.backend') ) ) {
                $backend = config('grapher.backend')[0];
            } else {
                $this->error('No graphing backend supplied or configured (see configs/grapher.php)');
                exit(-1);
            }
        }

        if( !in_array($backend,config('grapher.backend') ) ) {
            $this->error('No graphing provider enabled (see configs/grapher.php) for ' . $backend);
            exit(-1);
        }

        return $backend;
    }

    /**
     * Return the required grapher for the specified backend
     *
     * If the backend is not specified, it is resolved via `resolveBackend()`.
     * @see IXP\Console\Commands\Grapher\GrapherCommand::resolveBackend()
     *
     * @param string|null $backend A specific backend to return. If not specified, we use command line arguments
     * @return \IXP\Contracts\Grapher
     */
    protected function getGrapher( $backend = null ) {
        return App::make( config( 'grapher.providers.' . $this->resolveBackend( $backend ) ) );
    }

}

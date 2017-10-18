<?php namespace IXP\Services;

use Illuminate\Contracts\View\Engine as EngineInterface;

use Foil\Engine as EngineFoil;

class FoilEngine implements EngineInterface
{
    /** @var PlatesEngine */
    private $engine;

    public function __construct(EngineFoil $engine)
    {
        $this->engine = $engine;
    }

    public function engine(): EngineFoil {
        return $this->engine;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function get($path, array $data = array())
    {
        return $this->engine->render($path, $data);
    }

}

<?php

namespace IXP\Exceptions;

use Exception;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

class IrrdbManage extends Exception
{
    final public function render()
    {
        AlertContainer::push( $this->getMessage(), Alert::DANGER );
        return redirect()->back();
    }
}

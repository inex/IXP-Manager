<?php

namespace IXP\Http\Controllers\Api\V4;

use D2EM;
use Illuminate\Http\Request;

use Entities\{
    Switcher
};


class SwitcherController extends Controller {

    /**
     * Get the switch port for a Switch
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $request instance of the current HTTP request
     * @return  JSON array of listPort
     */
    public function switchPort( Request $request, int $id) {
        $listPorts = D2EM::getRepository(Switcher::class)->getAllPortForASwitch($id ,$request->input('custId'), $request->input('spId'));
        return response()->json(['listPorts' => $listPorts]);
    }


}
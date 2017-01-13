<?php

namespace IXP\Http\Controllers\PatchPanel;

use D2EM;

use Illuminate\Http\Request;
use IXP\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\View\View;

use Entities\Cabinet;
use Entities\PatchPanel;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

/**
 * PatchPanel Controller
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */


class PatchPanelController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View{
        return view('patch-panel.index');
    }

    public function edit(): View {
        // Get all the cabinets
        $listCabinets = D2EM::getRepository(Cabinet::class)->findAll();
        // Get all cable types
        $listCableTypes = \Entities\PatchPanel::$CABLE_TYPES;
        // Get all connector types
        $listConnectorTypes = \Entities\PatchPanel::$CONNECTOR_TYPES;
        // array of params for the view

        $params = array('listCabinets' => $listCabinets,
                        'listCableTypes' => $listCableTypes,
                        'listConnectorTypes' => $listConnectorTypes
                );

        return view('patch-panel.edit-patch-panel')->with('params', $params);
    }

    public function add(Request $request){

        $rules = array(
            'pp-name'    => 'required|string|max:255',
            'colocation'    => 'required|string|max:255',
            'cabinets' => 'required|integer',
            'cable-types' => 'required|integer',
            'connector-types' => 'required|integer',
            'number-ports' => 'required|integer',
            'port-prefix' => 'string'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('patch-panel.edit')
                ->withErrors($validator)
                ->withInput(Input::all());
        }
        else{
            $patchPanel = new PatchPanel();
            $patchPanel->setName();
            $patchPanel->setCabinet();
            $patchPanel->setConnectorType();
            $patchPanel->setCableType();
            $patchPanel->setColoReference();
            $patchPanel->setInstallationDate();
            D2EM::persist();
            D2EM::flush();
        }






    }
}

<?php

namespace IXP\Http\Controllers\PatchPanel;

use D2EM;

use Entities\Cabinet;
use Entities\PatchPanel;

use IXP\Http\Controllers\Controller;

use Former\Facades\Former;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use Illuminate\View\View;



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
        Former::framework('TwitterBootstrap3');

    }

    /**
     * Display the patch panel list
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @return  view
     */
    public function index(): View{
        // Get all cable types
        $listCableTypes = \Entities\PatchPanel::$CABLE_TYPES;
        // Get all connector types
        $listConnectorTypes = \Entities\PatchPanel::$CONNECTOR_TYPES;

        // array of params for the view
        $params = array('listPatchPanels'       => D2EM::getRepository(PatchPanel::class)->findAll(),
                        'listCableTypes'        => $listCableTypes,
                        'listConnectorTypes'    => $listConnectorTypes
        );

        return view('patch-panel/index')->with('params', $params);
    }

    /**
     * Allow to display the form to create/edit a patch panel
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  int $id EDIT => the ID of the patch panel that we need to modify, CREATE => we don't need it = null
     * @return  view
     */
    public function edit(int $id = null): View {
        // Get all cable types
        $listCableTypes = \Entities\PatchPanel::$CABLE_TYPES;
        // Get all connector types
        $listConnectorTypes = \Entities\PatchPanel::$CONNECTOR_TYPES;
        // array of params for the view


        if($id != null){
            $patchPanel = D2EM::getRepository(PatchPanel::class)->find($id);
            Former::populate( array('pp-name'               => $patchPanel->getName(),
                                    'colocation'            => $patchPanel->getColoReference(),
                                    'cabinets'              => $patchPanel->getCabinet()->getId(),
                                    'cable-types'           => $patchPanel->getCableType(),
                                    'connector-types'       => $patchPanel->getConnectorType(),
                                    'setInstallationDate'   => $patchPanel->getInstallationDateFormated(),
                                    'port-prefix'                => $patchPanel->getPrefixPort()

            ));

            $patchPanelId = $patchPanel->getId();
            $breadCrumb = 'Edit : '.$patchPanelId. ' - '.$patchPanel->getName();
        }
        else{
            $patchPanelId = null;
            $patchPanel = null;
            $breadCrumb = 'Add';
        }

        Former::open()->rules(array(
            'pp-name'           => 'required|max:255',
            'colocation'        => 'required|max:255',
            'number-ports'      => 'required|between:0,*|integer',
            'port-prefix'       => 'max:255',
            'installation-date' => 'date'

        ));

        $params = array('listCabinets'          => D2EM::getRepository(Cabinet::class)->getForArray(),
                        'listCableTypes'        => $listCableTypes,
                        'listConnectorTypes'    => $listConnectorTypes,
                        'patchPanelId'          => $patchPanelId,
                        'patchPanel'            => $patchPanel,
                        'breadCrumb'            => $breadCrumb
                );

        return view('patch-panel/edit-patch-panel')->with('params', $params);
    }

    /**
     * Allow to create/edit a patch panel
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @return  redirect
     */
    public function add(Request $request){
        // create rules in order to check the params
        $rules = array( 'pp-name'           => 'required|string|max:255',
                        'colocation'        => 'required|string|max:255',
                        'cabinets'          => 'required|integer',
                        'cable-types'       => 'required|integer',
                        'connector-types'   => 'required|integer',
                        'number-ports'      => 'required|integer',
                        'port-prefix'       => 'string',
                        'installation-date' => 'date',
        );

        // check the rules
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // one or many rules fails we redirect to the form will the error messages
            return Redirect::to('patch-panel/edit')
                ->withErrors($validator)
                ->withInput(Input::all());
        }
        else{
            $cabinet = D2EM::getRepository(Cabinet::class)->find($request->input('cabinets'));

            if($request->input('patch-panel-id') == null){
                // create a new patch panel object
                $patchPanel = new PatchPanel();
            }
            else{
                // get the existing patch panel object for that ID
                $patchPanel = D2EM::getRepository(PatchPanel::class)->find($request->input('patch-panel-id'));
            }

            // set the datas to the object
            $patchPanel->setName($request->input('pp-name'));
            $patchPanel->setCabinet($cabinet);
            $patchPanel->setConnectorType($request->input('connector-types'));
            $patchPanel->setCableType($request->input('cable-types'));
            $patchPanel->setColoReference($request->input('colocation'));
            $patchPanel->setInstallationDate(($request->input('installation-date') == '' ? null : new \DateTime($request->input('installation-date'))));
            D2EM::persist($patchPanel);
            D2EM::flush($patchPanel);

            // create the patch panel port related to the patch panel object
            $patchPanel->createPatchPanelPort($request->input('number-ports'),$request->input('port-prefix'));

            return Redirect::to('patch-panel-port/list/patch-panel/'.$patchPanel->getId());

        }

    }

    /**
     * Display the patch panel informations
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  int $id ID of the patch panel
     * @return  view
     */
    public function view(int $id = null) {
        // Get all cable types
        $listCableTypes = \Entities\PatchPanel::$CABLE_TYPES;
        // Get all connector types
        $listConnectorTypes = \Entities\PatchPanel::$CONNECTOR_TYPES;
        // array of params for the view

        if($id != null){
            $patchPanel = D2EM::getRepository(PatchPanel::class)->find($id);
        }
        else{
            return Redirect::to('patch-panel/list');
        }

        $params = array('listCabinets'          => D2EM::getRepository(Cabinet::class)->getForArray(),
                        'listCableTypes'        => $listCableTypes,
                        'listConnectorTypes'    => $listConnectorTypes,
                        'patchPanel'            => $patchPanel
        );

        return view('patch-panel/view-patch-panel')->with('params', $params);
    }
}

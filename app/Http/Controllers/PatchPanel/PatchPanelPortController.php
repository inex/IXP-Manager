<?php

namespace IXP\Http\Controllers\PatchPanel;

use D2EM;
Use DateTime;
use Entities\Customer;
use Entities\PatchPanelPort;

use Entities\Switcher;
use Entities\SwitchPort;
use IXP\Http\Controllers\Controller;

use Former\Facades\Former;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use Illuminate\View\View;



/**
 * PatchPanelPort Controller
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */


class PatchPanelPortController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, int $id = null): View{
        // Get all states
        $listStates = \Entities\PatchPanelPort::$STATES;

        if($id != null){
            $listPatchPanelPort = D2EM::getRepository(PatchPanelPort::class)->findByPatchPanel($id);
        }
        else{
            $listPatchPanelPort = D2EM::getRepository(PatchPanelPort::class)->findAll();
        }

        $params = array('listPatchPanelPort'    => $listPatchPanelPort,
                        'listStates'            => $listStates
        );

        return view('patch-panel-port/index')->with('params', $params);
    }

    public function edit(Request $request, int $id = null) {
        if($id == null){
            return Redirect::to('patch-panel-port/list');
        }

        // Get all states
        $listStates = \Entities\PatchPanelPort::$STATES;
        // Get all customers
        $listCustomers = D2EM::getRepository(Customer::class)->getNames(true);
        // Get all switches
        $listSwitch = D2EM::getRepository(Switcher::class)->getNames(true);

        if($id != null){
            $patchPanelPort = D2EM::getRepository(PatchPanelPort::class)->find($id);
            Former::populate( array('ppp-name'              => $patchPanelPort->getName(),
                                    'patch-panel'           => $patchPanelPort->getPatchPanel()->getName(),
                                    'switch'                => $patchPanelPort->getSwitchId(),
                                    'customer'              => $patchPanelPort->getCustomerId(),
                                    'state'                 => $patchPanelPort->getState(),
                                    'note'                  => $patchPanelPort->getNotes(),
                                    'assigned-at'           => $patchPanelPort->getAssignedAtFormated(),
                                    'connected-at'          => $patchPanelPort->getConnectedAtFormated(),
                                    'ceased-requested-at'   => $patchPanelPort->getCeaseRequestedAtFormated(),
                                    'ceased-at'                => $patchPanelPort->getCeasedAtFormated(),
                                    'last-state-change-at'  => $patchPanelPort->getLastStateChangeFormated(),
            ));



        }
        else{
            return Redirect::to('patch-panel-port/list');
        }

        $params = array(
            'listStates'        => $listStates,
            'listCustomers'     => $listCustomers,
            'listSwitch'        => $listSwitch,
            'patchPanelPort'    => $patchPanelPort,

        );

        return view('patch-panel-port/edit')->with('params', $params);
    }

    public function add(Request $request, int $id){
        if($id == null){
            return Redirect::to('patch-panel-port/list');
        }

        $rules = array( 'ppp-name'              => 'required|string|max:255',
                        'patch-panel'           => 'required',
                        'state'                 => 'required|integer',
                        'assignedAt'            => 'date',
                        'connectedAt'           => 'date',
                        'ceasedRequestedAt'     => 'date',
                        'ceasedAt'              => 'date',
                        'lastStateChangeAt'     => 'date',
        );


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('patch-panel-port/edit/'.$id)
                ->withErrors($validator)
                ->withInput(Input::all());
        }
        else{
            $patchPanelPort = D2EM::getRepository(PatchPanelPort::class)->find($id);

            if($request->input('switch-port')){
                $switchPort = D2EM::getRepository(SwitchPort::class)->find($request->input('switch-port'));

                $patchPanelPort->setSwitchPort($switchPort);
            }

            if($request->input('customer')){
                $patchPanelPort->setCustomer(D2EM::getRepository(Customer::class)->find($request->input('customer')));
            }

            if($patchPanelPort->getState() != $request->input('state')){
                $patchPanelPort->setState($request->input('state'));
                $patchPanelPort->setLastStateChange(new \DateTime(date('Y-m-d')));
            }

            $patchPanelPort->setNotes(($request->input('note') == '' ? null : $request->input('note')));

            $patchPanelPort->setAssignedAt(($request->input('assigned-at') == '' ? null : new \DateTime($request->input('assigned-at'))));
            $patchPanelPort->setConnectedAt(($request->input('connected-at') == '' ? null : new \DateTime($request->input('connected-at'))));

            $patchPanelPort->setCeaseRequestedAt(($request->input('ceased-requested-at') == '' ? null : new \DateTime($request->input('ceased-requested-at'))));
            $patchPanelPort->setCeasedAt(($request->input('ceased-at') == '' ? null : new \DateTime($request->input('ceased-at'))));

            $patchPanelPort->setInternalUse($request->input('internal-use'));
            $patchPanelPort->setChargeable($request->input('chargeable'));

            D2EM::persist($patchPanelPort);
            D2EM::flush($patchPanelPort);

            return Redirect::to('patch-panel-port/list');

        }

    }

    public function getSwitchPort(Request $request){
        $idSwitch = $request->input('switchId');
        $switch = D2EM::getRepository(Switcher::class)->find($idSwitch);
        dd($switch);
    }


    public function view(Request $request, int $id = null): View
    {
        $listStates = \Entities\PatchPanelPort::$STATES;
        if($id != null){
            $patchPanelPort = D2EM::getRepository(PatchPanelPort::class)->find($id);
        }
        else{
            return Redirect::to('patch-panel-port/list');
        }

        $params = array('listStates'        => $listStates,
                        'patchPanelPort'    => $patchPanelPort
        );

        return view('patch-panel-port/view')->with('params', $params);
    }
}

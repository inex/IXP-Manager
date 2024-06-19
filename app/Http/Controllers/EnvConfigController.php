<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, Former, Hash, Redirect, Route, Str;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use IXP\Services\DotEnvWriter;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Models\User;

/**
 * .env file configurator Controller
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class EnvConfigController extends Controller
{
    protected DotEnvWriter $envWriter;
    protected Former $former;
    protected array $panelConfig;

    /**
     * The minimum privileges required to access this controller.
     *
     * If you set this to less than the superuser, you need to manage privileges and access
     * within your own implementation yourself.
     *
     * @var int
     */
    public static $minimum_privilege = User::AUTH_SUPERUSER;

    public function __construct(Former $former, DotEnvWriter $envWriter) {
        $this->middleware('auth');
        $this->envWriter = $envWriter;
        $this->former = $former;
        $this->panelConfig = include(config_path('ixp_fe_config.php'));
    }

    /**
     * Collect rules from the panelconfig
     *
     * @param array $panels
     *
     * @return array
     */
    protected function gatherRules($panels): array
    {
        $rules = [];
        foreach($panels as $panel) {
            foreach($panel["fields"] as $label => $field) {
                if(isset($field["rules"]) && $field["rules"] !== '') {
                    $rules[$label] = $field["rules"];
                }
            }
        }
        return $rules;
    }

    /**
     * Replace substitution in value
     *
     * @param string|null $value
     * @param array $attributes
     *
     * @return string
     */
    protected function patternReplace(string|null $value,array $attributes): string
    {
        // check value: if it is points to another value grab that and replace it
        preg_match_all('/\${([\w\.]+)}/',$value,$matches);
        if (count($matches) > 0) {
            $search = $matches[0];
            $replace = [];
            foreach($matches[1] as $key) {
                $replace[] = $attributes[$key]["value"] ?: '';
            }
            $value = str_replace($search,$replace,$value);
        }
        return $value;
    }

    /**
     * Display the form to update the .env
     */
    protected function createForm()
    {
        $envConfig = new $this->envWriter();
        $envValues = $envConfig->getVariables();

        $form = $this->former::open()->method('POST')
            ->id('envForm')
            ->action(route('env_config@update'))
            ->customInputWidthClass( 'col-8' )
            ->customLabelWidthClass( 'col-4' )
            ->actionButtonsCustomClass( "grey-box")
            ->rules($this->gatherRules($this->panelConfig["panels"]));

        $form .= '<ul class="tabNavMenu" id="envFormTabs">';
        $first = true;
        $tabContents = [];
        foreach($this->panelConfig["panels"] as $panel => $content) {
            $form .= '<li>'
                .'<button class="tabButton'.($first ? ' active' : '').'" id="'.$panel.'-tab" data-target="#'.$panel.'-content" type="button">'.$content["title"].'</button></li>';

            $tab = '<div class="tabPanel'.($first ? ' active' : '').'" id="'.$panel.'-content" role="tabpanel" aria-labelledby="'.$panel.'-content">';

            if(isset($content["description"]) && $content["description"] !== "") {
                $tab .= '<p class="description">'.$content["description"].'</p>';
            }

            if(isset($content["fields"]) && count($content["fields"]) > 0) {
                foreach($content["fields"] as $field => $param) {
                    $title = $param["name"];
                    if(isset($param["docs_url"]) && $param["docs_url"]) {
                        $title .= '<a href="'.$param["docs_url"].'" target="_blank"><i class="ml-2 fa fa-external-link"></i></a>';
                    }

                    $value = null;
                    if(isset($envValues[$param['dotenv_key']])) {
                        $value = $envValues[$param['dotenv_key']]["value"];
                    }
                    //info($field.': '.var_export($value,1));

                    switch($param["type"]) {
                        case 'radio':
                            $input = Former::checkbox($field)->label($title)->check( $value === 'true' );
                            break;
                        case 'select':
                            if($param["options"]["type"] === 'array') {
                                $options = $param["options"]["list"];
                            } else {
                                $options = $this->getSelectOptions($param["options"]["list"]);
                            }

                            $input = Former::select($field)->label($title)->options($options,$value)->placeholder('Select an Option')->addClass( 'chzn-select' );
                            break;
                        case 'textarea':
                            $value = $this->patternReplace($value,$envValues);

                            $input = Former::textarea($field)->label($title)->value($value);
                            break;
                        default: // text
                            $value = $this->patternReplace($value,$envValues);

                            $input = Former::text($field)->label($title)->value($value);
                    }

                    $tab .= '<div class="inputWrapper">'.$input;
                    if(isset($param["help"]) && $param["help"] !== '') {
                        $tab .= '<div class="small"><i class="fa fa-info-circle tw-text-blue-600"></i> '.$param["help"].'</div>';
                    }
                    $tab .= '</div>';
                }
            }
            $tab .= '</div>';
            $tabContents[] = $tab;
            $first = false;
        }


        $form .= '</ul><div class="tabContent" id="envFormTabContents">';
        $form .= implode('',$tabContents).'</div>';
        $form .= $this->former::actions(
            Former::primary_button( 'Save Changes' )->id('updateButton')->class( "mb-2 mb-sm-0" )
        );
        $form .= $this->former::close();
        return $form;
    }


    /**
     * Display the form to edit an object
     */
    protected function index()
    {
        AlertContainer::push("test alert",Alert::SUCCESS);

        return view( 'env-config.index' )->with( [
            'form' => $this->createForm(),
        ] );
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     *
     * @return array
     *
     * @throws
     */
    public function update( Request $request ): array
    {
        $changes = $request->all();

        $envConfig = new $this->envWriter();
        foreach($this->panelConfig["panels"] as $panel) {
            foreach($panel["fields"] as $label => $field) {
                switch($field["type"]) {
                    case 'radio':
                        $value = $changes[$label] === '1' ? "true" : "false";
                        $envConfig->set($field["dotenv_key"],$value);
                        break;
                    default:
                        if(!isset($changes[$label]) || $changes[$label] === NULL || $changes[$label] === '') {
                            $envConfig->disable($field["dotenv_key"]);
                            $value = '-';
                        } else {
                            $envConfig->set($field["dotenv_key"],$changes[$label]);
                            $value = $changes[$label];
                        }
                }

            }
        }
        $envConfig->write();

        return ["status" => "success", "message" => "Modification done"];
    }

}

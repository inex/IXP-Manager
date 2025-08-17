<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Former;

use IXP\Services\DotEnvWriter;
use Illuminate\Http\Request;

/**
 * .env file configurator Controller
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SettingsController extends Controller
{
    protected array $fe_settings;


    public function __construct(
        protected Former $former,
        protected DotEnvWriter $envWriter
    ) {
        $this->fe_settings = config( 'ixp_fe_settings');
    }

    /**
     * Collect rules from the fe_settings configuration file which contains
     * arrays of 'panels' for the UI.
     *
     * @param array $panels
     * @return array
     */
    private function gatherRules(array $panels): array
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
     * @return string|null
     */
    private function patternReplace(string|null $value,array $attributes): string|null
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
    private function createForm(): string
    {
        $envValues = $this->envWriter->getVariables();

        $form = $this->former::open()->method('POST')
            ->id('envForm')
            ->action(route('settings@update'))
            ->customInputWidthClass( 'col-8' )
            ->customLabelWidthClass( 'col-4' )
            ->actionButtonsCustomClass( "grey-box")
            ->rules($this->gatherRules($this->fe_settings["panels"]));

        $form .= '<ul class="tabNavMenu" id="envFormTabs">';
        $first = true;
        $tabContents = [];

        foreach($this->fe_settings["panels"] as $panel => $content) {
            $form .= '<li>'
                .'<button class="tabButton'.($first ? ' active' : '').'" id="'.$panel.'-tab" data-target="#'.$panel.'-content" type="button">'.$content["title"].'</button></li>';

            $tab = '<div class="tabPanel'.($first ? ' active' : '').'" id="'.$panel.'-content" role="tabpanel" aria-labelledby="'.$panel.'-content">';

            if(isset($content["description"]) && $content["description"] !== "") {
                $tab .= '<div class="alert alert-info" role="alert"><div class="d-flex align-items-center"><div class="text-center"><i class="fa fa-question-circle fa-2x"></i></div><div class="col-sm-12">'.$content["description"].'</div></div></div>';
            }

            if(isset($content["fields"]) && count($content["fields"])) {

                foreach($content["fields"] as $field => $param) {
                    $title = $param["name"];
                    if(isset($param["docs_url"]) && $param["docs_url"]) {
                        $title .= '<a href="'.$param["docs_url"].'" target="_blank"><i class="ml-2 fa fa-external-link"></i></a>';
                    }

                    // value comes from config, not .env. Config includes defaults not covered by .env and so
                    // using .env could overwrite defaults.
                    $value = config( $param['config_key'] );

                    switch($param["type"]) {

                        case 'radio':
                            if( isset( $param["invert"] ) && $param["invert"] ) {
                                $value = !$value;
                            }
                            $input = Former::checkbox($field)->label($title)->check( $value );
                            break;

                        case 'select':
                            if($param["options"]["type"] === 'array') {
                                $options = $param["options"]["list"];
                            } else if($param["options"]["type"] === 'countries') {
                                $options = $this->getCountriesSelection();;
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
    protected function index(): \Illuminate\Contracts\View\View
    {
        return view( 'settings.index' )->with( [
            'form' => $this->createForm(),
        ] );
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     *
     * @return string[]
     *
     * @throws
     *
     * @psalm-return array{status: 'success', message: 'Modification done'}
     */
    public function update( Request $request ): array
    {
        $changes = $request->all();

        foreach($this->fe_settings["panels"] as $panel) {
            foreach($panel["fields"] as $label => $field) {
                switch($field["type"]) {
                    case 'radio':
                        $value = $changes[ $label ] === '1';
                        if( isset( $field["invert"] ) && $field["invert"] ) {
                            $value = !$value;
                        }

                        $this->envWriter->set($field["dotenv_key"],$value ? "true" : "false");
                        break;
                    default:
                        if(!isset($changes[$label]) || $changes[$label] === NULL || $changes[$label] === '') {
                            $this->envWriter->disable($field["dotenv_key"]);
                        } else {
                            $this->envWriter->set($field["dotenv_key"],$changes[$label]);
                        }
                }

            }
        }
        $this->envWriter->write();

        return ["status" => "success", "message" => "Modification done"];
    }

}

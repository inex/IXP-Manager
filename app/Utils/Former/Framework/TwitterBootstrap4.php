<?php namespace IXP\Utils\Former\Framework;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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


use Former\Framework\TwitterBootstrap4 as FormerTwitterBootstrap4;
use IXP\Utils\Former\Framework\Action as FormerAction;
use Former\Traits\Field;
use HtmlObject\Element;

/**
 * Overrides some methods of Former's default framework
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class TwitterBootstrap4 extends FormerTwitterBootstrap4  {

    /**
     * Create a new TwitterBootstrap instance
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct( $app )
    {
        parent::__construct($app);
    }


    /**
     * Wrap a field with potential additional tags
     *
     * @param  Field $field
     *
     * @return Element A wrapped field
     */
    public function wrapField($field)
    {
        $width = isset( $this->app['former.form']->getAttributes()['custom-input-width-class'] )
            ? $this->app['former.form']->getAttributes()['custom-input-width-class'] : $this->fieldWidth;

        if ($this->app['former.form']->isOfType('horizontal')) {
                return Element::create('div', $field)->addClass($width);
        }

        return $field;
    }

    /**
     * Add label classes
     *
     * @return string[] An array of attributes with the label class
     */
    public function getLabelClasses()
    {
        $width = isset( $this->app['former.form']->getAttributes()['custom-label-width-class'] )
            ? $this->app['former.form']->getAttributes()['custom-label-width-class'] : $this->labelWidth;

        if ($this->app['former.form']->isOfType('horizontal')) {
            return array('control-label', $width);
        } elseif ($this->app['former.form']->isOfType('inline')) {
            return array('sr-only');
        } else {
            return array('control-label');
        }
    }

    /**
     * Render an help text
     *
     * @param string $text
     * @param array  $attributes
     *
     * @return Element
     */
    public function createBlockHelp($text, $attributes = array())
    {
        return Element::create('small', $text, $attributes)->addClass('form-text text-muted former-help-text');
    }

    /**
     * Wrap an item to be prepended or appended to the current field
     *
     * @return Element A wrapped item
     */
    public function placeAround($item)
    {
        // Render object
        if (is_object($item) and method_exists($item, '__toString')) {
            $item = $item->__toString();
        }

        // Get class to use
        $class = (strpos($item, '<button') !== false) ? 'append' : 'addon';

        return Element::create('span', $item)->addClass('input-group-'.$class);
    }

    /**
     * Add group classes
     *
     * @return string A list of group classes
     */
    public function getGroupClasses()
    {
        if ($this->app['former.form']->isOfType('horizontal')) {
            $classes = 'form-group row ';
        } else {
            $classes = 'form-group ';
        }

        if( isset( $this->app['former.form']->getAttributes()['inputs-position'] ) ){
            $classes .= $this->app['former.form']->getAttributes()['inputs-position'];
        }

        return $classes;
    }


    /**
     * Add actions block class
     *
     * @return string|null
     */
    public function getActionClasses()
    {
        if ($this->app['former.form']->isOfType('horizontal') || $this->app['former.form']->isOfType('inline')) {
            $classes = 'form-group';

            if( isset( $this->app['former.form']->getAttributes()['inputs-positions'] ) ){
                $classes .= $this->app['former.form']->getAttributes()['inputs-positions'];
            }

            if( isset( $this->app['former.form']->getAttributes()['action-buttons-custom-class'] ) &&  $this->app['former.form']->getAttributes()['action-buttons-custom-class'] == "grey-box" ){
                $classes .= " col-sm-12";
            } else{
                $classes .= " row";
            }

            return $classes;
        }

        return null;
    }


    /**
     * Wrap actions block with potential additional tags
     *
     * @param  Actions $actions
     *
     * @return string A wrapped actions block
     */
    public function wrapActions($actions)
    {

        // For horizontal forms, we wrap the actions in a div
        if ($this->app['former.form']->isOfType('horizontal')) {

            $class = isset( $this->app['former.form']->getAttributes()['action-buttons-custom-class'] ) ? $this->app['former.form']->getAttributes()['action-buttons-custom-class'] : "";

            if( $class == "grey-box" ){
                $element = Element::create('div', $actions)->addClass( "bg-light p-4 mt-4 shadow-sm text-center col-lg-12" );
            } else{
                $element = Element::create('div', $actions)->addClass(array($this->fieldOffset, $this->fieldWidth , $class ));
            }

            return $element;
        }

        return $actions;
    }
}

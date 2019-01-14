<?php namespace IXP\Utils\Former\Framework;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
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
        $width = isset( $this->app['former.form']->getAttributes()['custom-width-class'] )
            ? $this->app['former.form']->getAttributes()['custom-width-class'] : $this->fieldWidth;

        if ($this->app['former.form']->isOfType('horizontal')) {
                return Element::create('div', $field)->addClass($width);
        }

        return $field;
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

}

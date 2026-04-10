<?php

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Utils\Whois;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

/**
 * Attribute containing a whois server configuration name, allowing specific Whois server
 * to be resolved from configuration.
 *
 * @author     Thomas Kerin       <thomas@islandbridgenetworks.ie>
 * @category   Whois
 * @package    IXP\Utils\Whois
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class WhoisHost implements ContextualAttribute
{
    /**
     * @param string $name The name of the whois config to load
     */
    public function __construct(public string $name) {}

    /**
     * @param WhoisHost     $attribute containing whois config name
     * @param Container     $container for dependency injection
     * @return Whois
     * @throws \IXP\Exceptions\Utils\Whois\WhoisException
     */
    public static function resolve(self $attribute, Container $container): Whois
    {
        return $container->make(WhoisResolver::class)->get($attribute->name);
    }
}
<?php
declare(strict_types=1);

namespace IXP\Http\Middleware\Services;

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

use App;
use Auth;
use Closure;
use D2EM;
use Route;

use Illuminate\Http\Request;

use Entities\{
    Router as RouterEntity,
    User as UserEntity
};
use IXP\Services\Grapher as LookingGlassService;
use IXP\Exceptions\Utils\RouterException;

// use IXP\Exceptions\Services\Grapher\{BadBackendException,CannotHandleRequestException};

/**
 * LookingGlass -> MIDDLEWARE
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   LookingGlass
 * @package    IXP\Services\LookingGlass
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LookingGlass
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next )
    {
        if( Route::currentRouteName() == 'lg::index' ) {
            return $next($request);
        }

        // get the router object
        try {
            /** @var RouterEntity $router */
            $router = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => $request->handle ] );
            if( !$router->hasApi() ) {
                throw new RouterException('No API available');
            }
        } catch( RouterException $e ) {
            abort( 404, $e->getMessage() );
        }

        // let's authorise for access (this throws an exception)
        $this->authorise($router);

        // get the appropriate looking glass service
        // (throws an exception if no appropriate Looking Glass handler)
        $lg = App::make('IXP\Services\LookingGlass')->forRouter( $router );

        $request->attributes->add(['lg' => $lg]);

        return $next($request);
    }


    /**
     * This function controls access to a router for a looking glass
     *
     * @param RouterEntity $router
     * @return bool
     */
    private function authorise( RouterEntity $router ): bool {
        if( $router->authorise( Auth::check() ? Auth::user()->getPrivs() : UserEntity::AUTH_PUBLIC ) ) {
            return true;
        }

        abort( 401, "Insufficent permissions to access this looking glass" );
    }


}

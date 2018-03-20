<?php

namespace IXP\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;

use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Checks if reseller mode is enabled.
     *
     * To enable reseller mode set the env variable IXP_RESELLER_ENABLED
     *
     * @see http://docs.ixpmanager.org/features/reseller/
     *
     * @return bool
     */
    protected function resellerMode(): bool
    {
        return boolval( config( 'ixp.reseller.enabled', false ) );
    }

    /**
     * Checks if multi IXP mode is enabled.
     *
     * To enable multi IXP mode set the env variable IXP_MULTIIXP_ENABLED
     *
     * NB: this functionality is deprecated in IXP Manager v4.0 and will be
     * removed piecemeal.
     *
     * @see https://github.com/inex/IXP-Manager/wiki/Multi-IXP-Functionality
     *
     * @return bool
     */
    protected function multiIXP(): bool
    {
        return boolval( config( 'ixp.multiixp.enabled', false ) );
    }

    /**
     * Checks if as112 is activated in the UI.
     *
     * To disable as112 in the UI set the env variable IXP_AS112_UI_ACTIVE
     *
     * @see http://docs.ixpmanager.org/features/as112/
     *
     * @return bool
     */
    protected function as112UiActive(): bool
    {
        return boolval( config( 'ixp.as112.ui_active', false ) );
    }

    /**
     * Checks if logo management is enabled
     *
     * To enable logos in the UI set IXP_FE_FRONTEND_DISABLED_LOGO=false in .env
     *
     * @return bool
     */
    protected function logoManagementEnabled()
    {
        return !boolval( config( 'ixp_fe.frontend.disabled.logo' ) );
    }

}

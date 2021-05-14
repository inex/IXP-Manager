<?php

namespace IXP\Http\Controllers\PatchPanel\Port;

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

use Former, Mail, Redirect;

use Barryvdh\DomPDF\PDF;

use IXP\Models\PatchPanelPort;

use Illuminate\Http\{
    RedirectResponse,
};

use Illuminate\View\View;

use IXP\Exceptions\Mailable as MailableException;

use IXP\Http\Requests\{
    EmailPatchPanelPort as EmailPatchPanelPortRequest,
};

use IXP\Mail\PatchPanelPort\Email;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Email Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\PatchPanel\Port
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class EmailController extends Common
{
    /**
     * Display and fill the form to send an email to the customer
     *
     * @param  PatchPanelPort   $ppp    patch panel port
     * @param  int              $type   Email type to send
     *
     * @return  view
     */
    public function email( PatchPanelPort $ppp, int $type ): View
    {
        $mailable = $this->setupEmailRoutes( $ppp, $type );

        return view( 'patch-panel-port/email-form' )->with([
            'ppp'                           => $ppp,
            'emailType'                     => $type,
            'body'                          => $mailable->getBody()
        ]);
    }

    /**
     * Send an email to the customer (connected, ceased, info, loa PDF)
     *
     * @param EmailPatchPanelPortRequest    $r
     * @param PatchPanelPort                $ppp   patch panel port id
     * @param int                           $type Email type to send
     *
     * @return RedirectResponse|View
     */
    public function send( EmailPatchPanelPortRequest $r, PatchPanelPort $ppp, int $type ): RedirectResponse|View
    {
        $mailable = $this->setupEmailRoutes( $ppp, $type );

        $mailable->prepareFromRequest( $r );
        $mailable->prepareBody( $r );

        try {
            $mailable->checkIfSendable();
        } catch( MailableException $e ) {
            AlertContainer::push( $e->getMessage(), Alert::DANGER );

            return view( 'patch-panel-port/email-form' )->with([
                'ppp'                           => $ppp,
                'emailType'                     => $type,
                'mailable'                      => $mailable
            ]);
        }

        if( $type === PatchPanelPort::EMAIL_LOA || $r->loa ) {
            /** @var PDF $pdf */
            [ $pdf, $pdfname ] = $this->createLoAPDF( $ppp );
            $mailable->attachData( $pdf->output(), $pdfname, [
                'mime'    => 'application/pdf'
            ]);
        }

        // should we also attach public files?
        if( in_array( $type, [ PatchPanelPort::EMAIL_CEASE, PatchPanelPort::EMAIL_INFO ], true ) ) {
            foreach( $ppp->patchPanelPortFilesPublic as $file ) {
                $mailable->attach( storage_path() . '/files/' . $file->path(), [
                    'as'            => $file->name,
                    'mime'          => $file->type
                ]);
            }
        }

        Mail::send( $mailable );

        AlertContainer::push( "Email sent.", Alert::SUCCESS );

        return Redirect::to( route( 'patch-panel-port@list-for-patch-panel', [ "pp" => $ppp->patch_panel_id ] ) );
    }

    /**
     * Setup / validation for composing and sending emails
     *
     * @param PatchPanelPort    $ppp
     * @param int               $type Email type to send
     * @param Email|null        $mailable
     *
     * @return  Email
     */
    private function setupEmailRoutes( PatchPanelPort $ppp, int $type, Email $mailable = null ): Email
    {
        if( !array_key_exists( $type, PatchPanelPort::$EMAIL_CLASSES ) ) {
            abort(404, 'Email type not found');
        }

        if( !$mailable ) {
            $mailable = new PatchPanelPort::$EMAIL_CLASSES[ $type ]( $ppp );
        }

        Former::populate([
            'email_to'       => implode( ',', $mailable->getRecipientEmails('to') ),
            'email_subject'  => $mailable->getSubject(),
            'email_bcc'      => implode( ',', $mailable->getRecipientEmails('bcc') )
        ]);

        return $mailable;
    }
}
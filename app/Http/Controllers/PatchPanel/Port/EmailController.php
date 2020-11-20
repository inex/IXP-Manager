<?php

namespace IXP\Http\Controllers\PatchPanel\Port;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use D2EM, Former, Mail, Redirect;

use Entities\{
    PatchPanelPort              as PatchPanelPortEntity,
    PatchPanelPortFile          as PatchPanelPortFileEntity,
};

use Illuminate\Http\{
    RedirectResponse,
};

use Illuminate\View\View;

use IXP\Exceptions\Mailable as MailableException;
use IXP\Http\Controllers\Controller;

use IXP\Http\Requests\{
    EmailPatchPanelPort as EmailPatchPanelPortRequest,
};

use IXP\Mail\PatchPanelPort\Email;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Repositories\PatchPanelPort as PatchPanelPortRepository;

/**
 * Email Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel/Port
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class EmailController extends Controller
{

    /**
     * Setup / validation for composing and sending emails
     *
     * @param  int $type Email type to send
     * @param  Email $mailable
     * @return  Email
     */
    private function setupEmailRoutes( int $type, Email $mailable = null ): Email
    {
        /** @var PatchPanelPortRepository $pppRepository */
        $pppRepository = D2EM::getRepository( PatchPanelPortEntity::class );
        if( !( $emailClass = $pppRepository->resolveEmailClass( $type ) ) ) {
            abort(404, 'Email type not found');
        }

        if( !$mailable ) {
            $mailable = new $emailClass($this->getPPP());
        }

        Former::populate([
            'email_to'       => implode( ',', $mailable->getRecipientEmails('to') ),
            'email_subject'  => $mailable->getSubject(),
            'email_bcc'      => implode( ',', $mailable->getRecipientEmails('bcc') )
        ]);

        return $mailable;
    }

    /**
     * Display and fill the form to send an email to the customer
     *
     * @param  int $id patch panel port id
     * @param  int $type Email type to send
     * @return  view
     */
    public function email( int $id, int $type ): View
    {
        $mailable = $this->setupEmailRoutes( $type );

        return view( 'patch-panel-port/email-form' )->with([
            'ppp'                           => $this->getPPP($id),
            'emailType'                     => $type,
            'body'                          => $mailable->getBody()
        ]);
    }

    /**
     * Send an email to the customer (connected, ceased, info, loa PDF)
     *
     * @param EmailPatchPanelPortRequest $request
     * @param int $id   patch panel port id
     * @param int  $type Email type to send
     *
     * @return RedirectResponse|View
     */
    public function sendEmail( EmailPatchPanelPortRequest $request, int $id, int $type )
    {
        $mailable = $this->setupEmailRoutes( $type );

        $mailable->prepareFromRequest( $request );
        $mailable->prepareBody( $request );

        try {
            $mailable->checkIfSendable();
        } catch( MailableException $e ) {
            AlertContainer::push( $e->getMessage(), Alert::DANGER );

            return view( 'patch-panel-port/email-form' )->with([
                'ppp'                           => $this->getPPP($id),
                'emailType'                     => $type,
                'mailable'                      => $mailable
            ]);
        }

        if( $type == PatchPanelPortEntity::EMAIL_LOA || $request->input( 'loa' ) ) {
            /** @var \Barryvdh\DomPDF\PDF $pdf */
            [ $pdf, $pdfname ] = $this->createLoAPDF( $this->getPPP() );
            $mailable->attachData( $pdf->output(), $pdfname, [
                'mime'    => 'application/pdf'
            ]);
        }

        // should we also attach public files?
        if( in_array( $type, [ PatchPanelPortEntity::EMAIL_CEASE, PatchPanelPortEntity::EMAIL_INFO ] ) ) {
            foreach( $this->getPPP()->getPatchPanelPortPublicFiles() as $pppf ) {
                /** @var PatchPanelPortFileEntity $pppf */
                $mailable->attach( storage_path() . '/files/' . $pppf->getPath(), [
                    'as'            => $pppf->getName(),
                    'mime'          => $pppf->getType()
                ]);
            }
        }

        Mail::send( $mailable );

        AlertContainer::push( "Email sent.", Alert::SUCCESS );

        return Redirect::to( route( 'patch-panel-port@list-for-patch-panel', [ "pp" => $this->getPPP()->getPatchPanel()->getId() ] ) );
    }

}
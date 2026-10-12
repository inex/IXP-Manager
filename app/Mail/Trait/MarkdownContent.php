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

namespace IXP\Mail\Trait;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Markdown;

/**
 * This trait provides a content method which Laravel will use if available.
 * It will render the provided $userMarkdown using the mail::message layouts
 * into text and HTML for email.
 */
trait MarkdownContent
{
    /**
     * Markdown content to send (derived from templates, but subject to changes)
     */
    public string $userMarkdown;

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $markdown = app(Markdown::class);

        $this->withSymfonyMessage( function ( \Symfony\Component\Mime\Email $message ) use ( $markdown ) {
            $message->text( $markdown->renderText( 'mail::message', [
                'slot' => $this->userMarkdown,
            ] )->toHtml() );
        } );

        return new Content(
            htmlString: $markdown->render( 'mail::message', [
                'slot' => $this->userMarkdown,
            ] )->toHtml()
        );
    }
}
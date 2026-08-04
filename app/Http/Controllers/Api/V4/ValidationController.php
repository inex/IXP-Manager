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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace IXP\Http\Controllers\Api\V4;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use IXP\Contracts\Validation\ValidationRunner;
use IXP\Utils\Validation\CallToActionLink;

class ValidationController
{

    private function getJobKey( string $jobId ): string
    {
        return "validation:job:$jobId";
    }

    public function apiResults(string $id): JsonResponse
    {
        if ( !( $job = Cache::store('file')->get( $this->getJobKey( $id ) ) ) ) {
            return response()->json( [], 404 );
        }
        $complete = array_all( $job['backends'] , fn(ValidationRunner $backend) => $backend->isComplete() || $backend->isTimedOut() );

        $prioritySortedBackends = collect($job['backends'])
            ->sortBy(fn(ValidationRunner $backend) => $backend->getValidator()->getPriority())
            ->all();
        $validations = [];

        /** @var ValidationRunner[] $prioritySortedBackends */
        foreach ($prioritySortedBackends as $backend) {
            // This loop processes complete (successful + failed), and timed out
            $softwareArray = [];
            $resultsArray = [];

            foreach ($backend->getSoftware() as $software) {
                $softwareArray[] = [
                    'name'    => $software->software,
                    'version' => $software->version
                ];
            }

            foreach ($backend->getResults() as $result) {
                $resultsArray[] = [
                    'message'          => $result->message,
                    'type'             => $result->type,
                    'additional_info'  => $result->additionalInfo,
                    'docs_url'         => $result->docsUrl,
                    'settings_url'     => $result->settingsUrl,
                    'call_to_action'   => $result->callToAction instanceof CallToActionLink ? [
                        'url'   => $result->callToAction->url,
                        'text'  => $result->callToAction->text,
                    ] : null,
                ];
            }

            if ( ( $failureInfo = $backend->getFailureInfo() ) ) {
                $failure = [
                    'exception' => $failureInfo->class,
                    'message'   => $failureInfo->message,
                    'file'      => $failureInfo->file,
                    'line'      => $failureInfo->line,
                ];
            } else {
                $failure = null;
            }

            $validations[] = [
                'name'         => $backend->getValidator()->getName(),
                'description'  => $backend->getValidator()->getDescription(),
                'priority'     => $backend->getValidator()->getPriority(),
                'is_complete'  => $backend->isComplete(),
                'is_failed'    => $backend->isFailed(),
                'is_timedout'  => $backend->isTimedOut(),
                'software'     => $softwareArray,
                'results'      => $resultsArray,
                'failure'      => $failure,
            ];
        }

        return response()->json( [
            'started'     => $job['started'],
            'finished'    => $job['finished'],
            'complete'    => $complete,
            'validations' => $validations,
        ] );
    }
}
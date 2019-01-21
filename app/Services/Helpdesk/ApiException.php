<?php namespace IXP\Services\Helpdesk;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Exception;

class ApiException extends Exception {

    /**
     * Error details from Zendesk
     * @object
     *
     * Example:
     *
     *     object(stdClass)#6753 (3) {
     *       ["error"]=>
      *      string(13) "RecordInvalid"
     *       ["description"]=>
     *       string(24) "Record validation errors"
     *       ["details"]=>
     *       object(stdClass)#6752 (1) {
     *         ["email"]=>
     *         array(1) {
     *           [0]=>
     *           object(stdClass)#6912 (2) {
     *             ["description"]=>
     *             string(62) "Email: ccc@example.com is already being used by another user"
     *             ["error"]=>
     *             string(14) "DuplicateValue"
     *           }
     *         }
     *       }
     *     }
     *
     */
    protected $errorDetails;

    /**
     * Set the error details
     *
     * @param object $ed
     */
    public function setErrorDetails( $ed ) {
        $this->errorDetails = $ed;
    }

    /**
     * Get the error details
     * @return object
     */
    public function getErrorDetails() {
        return $this->errorDetails;
    }


    /**
     * Zendesk uses email addresses as a unique key.
     *
     * This test lets us check for that error in a helpdesk agnostic way.
     *
     * @return boolean If the error / exception relates to a duplicate email address
     */
    public function userIsDuplicateEmail() {
        if( $this->errorDetails && $this->errorDetails->error == 'RecordInvalid' && isset( $this->errorDetails->details->email ) ) {
            foreach( $this->errorDetails->details->email as $ed ) {
                if( $ed->error == 'DuplicateValue' )
                    return true;
            }
        }
        return false;
    }
}

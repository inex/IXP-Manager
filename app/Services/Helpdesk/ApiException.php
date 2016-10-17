<?php namespace IXP\Services\Helpdesk;

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

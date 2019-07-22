<?php

namespace IXP\Rules;

use Illuminate\Contracts\Validation\Rule;

class IdnValidate implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $foo = idn_to_ascii ( $value, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46 );
        return ( filter_var( $foo, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) ) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not valid.';
    }
}

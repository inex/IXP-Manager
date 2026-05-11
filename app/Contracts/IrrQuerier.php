<?php

namespace IXP\Contracts;


use IXP\Exceptions\GeneralException as Exception;

/**
 * Contract for IRR query utilities
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @author Thomas Kerin    <thomas@islandbridgenetworks.ie>
 */
interface IrrQuerier
{
    /**
     * Get the IRRDB prefix list (based on route[6]: objects) for a given AS
     * number / macro and protocol.
     *
     * Returns an array of prefixes (or empty array).
     *
     * @param string $asmacro As number (of the form as1234) or AS macro
     * @param int $proto The IP protocol - 4 or 6.
     *
     * @return array The array of prefixes (or empty array).
     *
     * @throws Exception On a JSON decoding error
     *
     * @psalm-return list{0?: mixed,...}
     */
    public function getPrefixList( string $asmacro, int $proto = 4 ): array;

    /**
     * Get the IRRDB ASN list (based on route[6]: objects) for a given AS
     * number / macro and protocol.
     *
     * Returns an array of ASNs that may appear in any as path for the
     * route paths (or empty array).
     *
     * @param string $asmacro As number (of the form as1234) or AS macro
     * @param int $proto The IP protocol - 4 or 6.
     *
     * @return array The array of prefixes (or empty array).
     *
     * @psalm-return list<mixed>
     */
    public function getAsnList( string $asmacro, int $proto = 4 ): array;

    /**
     * The whois server to query
     *
     * @param string $whois The whois server to query
     *
     * @return static For fluent interfaces
     */
    public function setWhois( string $whois ): static;

    /**
     * The whois server sources
     *
     * @param string $sources The whois server sources
     *
     * @return static For fluent interfaces
     */
    public function setSources( string $sources ): static;

}
<?php

namespace Entities;

/**
 * Sessions
 */
class Session
{
    /**
     * @var int|null
     */
    private $user_id;

    /**
     * @var string|null
     */
    private $ip_address;

    /**
     * @var string|null
     */
    private $user_agent;

    /**
     * @var string
     */
    private $payload;

    /**
     * @var int
     */
    private $last_activity;

    /**
     * @var string
     */
    private $id;
    
}

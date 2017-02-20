<?php

namespace Entities;

/**
 * PatchPanelPortFile
 */
class PatchPanelPortFile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \DateTime
     */
    private $uploaded_at;

    /**
     * @var string
     */
    private $uploaded_by;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var string
     */
    private $storage_location;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\PatchPanelPort
     */
    private $patchPanelPort;


}


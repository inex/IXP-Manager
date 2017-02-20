<?php

namespace Entities;

/**
 * PatchPanelPortHistoryFile
 */
class PatchPanelPortHistoryFile
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
     * @var \Entities\PatchPanelPortHistory
     */
    private $patchPanelPortHistory;


}


<?php

namespace Entities;

/**
 * SflowReceiver
 */
class SflowReceiver
{
/**
 * @var string
 */
private $dst_ip;

/**
 * @var integer
 */
private $dst_port;

/**
 * @var integer
 */
private $id;

/**
 * @var \Entities\VirtualInterface
 */
private $VirtualInterface;


/**
 * Set dstIp
 *
 * @param string $dstIp
 *
 * @return SflowReceiver
 */
public function setDstIp($dstIp)
{
$this->dst_ip = $dstIp;

return $this;
}

/**
 * Get dstIp
 *
 * @return string
 */
public function getDstIp()
{
return $this->dst_ip;
}

/**
 * Set dstPort
 *
 * @param integer $dstPort
 *
 * @return SflowReceiver
 */
public function setDstPort($dstPort)
{
$this->dst_port = $dstPort;

return $this;
}

/**
 * Get dstPort
 *
 * @return integer
 */
public function getDstPort()
{
return $this->dst_port;
}

/**
 * Get id
 *
 * @return integer
 */
public function getId()
{
return $this->id;
}

/**
 * Set virtualInterface
 *
 * @param \Entities\VirtualInterface $virtualInterface
 *
 * @return SflowReceiver
 */
public function setVirtualInterface(\Entities\VirtualInterface $virtualInterface = null)
{
$this->VirtualInterface = $virtualInterface;

return $this;
}

/**
 * Get virtualInterface
 *
 * @return \Entities\VirtualInterface
 */
public function getVirtualInterface()
{
return $this->VirtualInterface;
}


}

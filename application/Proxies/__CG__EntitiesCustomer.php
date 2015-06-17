<?php

namespace Proxies\__CG__\Entities;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Customer extends \Entities\Customer implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function setType($type)
    {
        $this->__load();
        return parent::setType($type);
    }

    public function getType()
    {
        $this->__load();
        return parent::getType();
    }

    public function setShortname($shortname)
    {
        $this->__load();
        return parent::setShortname($shortname);
    }

    public function getShortname()
    {
        $this->__load();
        return parent::getShortname();
    }

    public function setAutsys($autsys)
    {
        $this->__load();
        return parent::setAutsys($autsys);
    }

    public function getAutsys()
    {
        $this->__load();
        return parent::getAutsys();
    }

    public function setMaxprefixes($maxprefixes)
    {
        $this->__load();
        return parent::setMaxprefixes($maxprefixes);
    }

    public function getMaxprefixes()
    {
        $this->__load();
        return parent::getMaxprefixes();
    }

    public function setPeeringemail($peeringemail)
    {
        $this->__load();
        return parent::setPeeringemail($peeringemail);
    }

    public function getPeeringemail()
    {
        $this->__load();
        return parent::getPeeringemail();
    }

    public function setNocphone($nocphone)
    {
        $this->__load();
        return parent::setNocphone($nocphone);
    }

    public function getNocphone()
    {
        $this->__load();
        return parent::getNocphone();
    }

    public function setNocfax($nocfax)
    {
        $this->__load();
        return parent::setNocfax($nocfax);
    }

    public function getNocfax()
    {
        $this->__load();
        return parent::getNocfax();
    }

    public function setNocemail($nocemail)
    {
        $this->__load();
        return parent::setNocemail($nocemail);
    }

    public function getNocemail()
    {
        $this->__load();
        return parent::getNocemail();
    }

    public function setNochours($nochours)
    {
        $this->__load();
        return parent::setNochours($nochours);
    }

    public function getNochours()
    {
        $this->__load();
        return parent::getNochours();
    }

    public function setNocwww($nocwww)
    {
        $this->__load();
        return parent::setNocwww($nocwww);
    }

    public function getNocwww()
    {
        $this->__load();
        return parent::getNocwww();
    }

    public function setPeeringmacro($peeringmacro)
    {
        $this->__load();
        return parent::setPeeringmacro($peeringmacro);
    }

    public function getPeeringmacro()
    {
        $this->__load();
        return parent::getPeeringmacro();
    }

    public function setPeeringmacrov6($peeringmacrov6)
    {
        $this->__load();
        return parent::setPeeringmacrov6($peeringmacrov6);
    }

    public function getPeeringmacrov6()
    {
        $this->__load();
        return parent::getPeeringmacrov6();
    }

    public function setPeeringpolicy($peeringpolicy)
    {
        $this->__load();
        return parent::setPeeringpolicy($peeringpolicy);
    }

    public function getPeeringpolicy()
    {
        $this->__load();
        return parent::getPeeringpolicy();
    }

    public function setCorpwww($corpwww)
    {
        $this->__load();
        return parent::setCorpwww($corpwww);
    }

    public function getCorpwww()
    {
        $this->__load();
        return parent::getCorpwww();
    }

    public function setDatejoin($datejoin)
    {
        $this->__load();
        return parent::setDatejoin($datejoin);
    }

    public function getDatejoin()
    {
        $this->__load();
        return parent::getDatejoin();
    }

    public function setDateleave($dateleave)
    {
        $this->__load();
        return parent::setDateleave($dateleave);
    }

    public function getDateleave()
    {
        $this->__load();
        return parent::getDateleave();
    }

    public function setStatus($status)
    {
        $this->__load();
        return parent::setStatus($status);
    }

    public function getStatus()
    {
        $this->__load();
        return parent::getStatus();
    }

    public function setActivepeeringmatrix($activepeeringmatrix)
    {
        $this->__load();
        return parent::setActivepeeringmatrix($activepeeringmatrix);
    }

    public function getActivepeeringmatrix()
    {
        $this->__load();
        return parent::getActivepeeringmatrix();
    }

    public function setLastupdated($lastupdated)
    {
        $this->__load();
        return parent::setLastupdated($lastupdated);
    }

    public function getLastupdated()
    {
        $this->__load();
        return parent::getLastupdated();
    }

    public function setLastupdatedby($lastupdatedby)
    {
        $this->__load();
        return parent::setLastupdatedby($lastupdatedby);
    }

    public function getLastupdatedby()
    {
        $this->__load();
        return parent::getLastupdatedby();
    }

    public function setCreator($creator)
    {
        $this->__load();
        return parent::setCreator($creator);
    }

    public function getCreator()
    {
        $this->__load();
        return parent::getCreator();
    }

    public function setCreated($created)
    {
        $this->__load();
        return parent::setCreated($created);
    }

    public function getCreated()
    {
        $this->__load();
        return parent::getCreated();
    }

    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function addVirtualInterface(\Entities\VirtualInterface $virtualInterfaces)
    {
        $this->__load();
        return parent::addVirtualInterface($virtualInterfaces);
    }

    public function removeVirtualInterface(\Entities\VirtualInterface $virtualInterfaces)
    {
        $this->__load();
        return parent::removeVirtualInterface($virtualInterfaces);
    }

    public function getVirtualInterfaces()
    {
        $this->__load();
        return parent::getVirtualInterfaces();
    }

    public function addContact(\Entities\Contact $contacts)
    {
        $this->__load();
        return parent::addContact($contacts);
    }

    public function removeContact(\Entities\Contact $contacts)
    {
        $this->__load();
        return parent::removeContact($contacts);
    }

    public function getContacts()
    {
        $this->__load();
        return parent::getContacts();
    }

    public function addConsoleServerConnection(\Entities\ConsoleServerConnection $consoleServerConnections)
    {
        $this->__load();
        return parent::addConsoleServerConnection($consoleServerConnections);
    }

    public function removeConsoleServerConnection(\Entities\ConsoleServerConnection $consoleServerConnections)
    {
        $this->__load();
        return parent::removeConsoleServerConnection($consoleServerConnections);
    }

    public function getConsoleServerConnections()
    {
        $this->__load();
        return parent::getConsoleServerConnections();
    }

    public function addCustomerEquipment(\Entities\CustomerEquipment $customerEquipment)
    {
        $this->__load();
        return parent::addCustomerEquipment($customerEquipment);
    }

    public function removeCustomerEquipment(\Entities\CustomerEquipment $customerEquipment)
    {
        $this->__load();
        return parent::removeCustomerEquipment($customerEquipment);
    }

    public function getCustomerEquipment()
    {
        $this->__load();
        return parent::getCustomerEquipment();
    }

    public function addPeer(\Entities\PeeringManager $peers)
    {
        $this->__load();
        return parent::addPeer($peers);
    }

    public function removePeer(\Entities\PeeringManager $peers)
    {
        $this->__load();
        return parent::removePeer($peers);
    }

    public function getPeers()
    {
        $this->__load();
        return parent::getPeers();
    }

    public function addPeersWith(\Entities\PeeringManager $peersWith)
    {
        $this->__load();
        return parent::addPeersWith($peersWith);
    }

    public function removePeersWith(\Entities\PeeringManager $peersWith)
    {
        $this->__load();
        return parent::removePeersWith($peersWith);
    }

    public function getPeersWith()
    {
        $this->__load();
        return parent::getPeersWith();
    }

    public function addXCust(\Entities\PeeringMatrix $xCusts)
    {
        $this->__load();
        return parent::addXCust($xCusts);
    }

    public function removeXCust(\Entities\PeeringMatrix $xCusts)
    {
        $this->__load();
        return parent::removeXCust($xCusts);
    }

    public function getXCusts()
    {
        $this->__load();
        return parent::getXCusts();
    }

    public function addYCust(\Entities\PeeringMatrix $yCusts)
    {
        $this->__load();
        return parent::addYCust($yCusts);
    }

    public function removeYCust(\Entities\PeeringMatrix $yCusts)
    {
        $this->__load();
        return parent::removeYCust($yCusts);
    }

    public function getYCusts()
    {
        $this->__load();
        return parent::getYCusts();
    }

    public function addUser(\Entities\User $users)
    {
        $this->__load();
        return parent::addUser($users);
    }

    public function removeUser(\Entities\User $users)
    {
        $this->__load();
        return parent::removeUser($users);
    }

    public function getUsers()
    {
        $this->__load();
        return parent::getUsers();
    }

    public function addTraffic95th(\Entities\Traffic95th $traffic95ths)
    {
        $this->__load();
        return parent::addTraffic95th($traffic95ths);
    }

    public function removeTraffic95th(\Entities\Traffic95th $traffic95ths)
    {
        $this->__load();
        return parent::removeTraffic95th($traffic95ths);
    }

    public function getTraffic95ths()
    {
        $this->__load();
        return parent::getTraffic95ths();
    }

    public function addTraffic95thMonthly(\Entities\Traffic95thMonthly $traffic95thMonthlys)
    {
        $this->__load();
        return parent::addTraffic95thMonthly($traffic95thMonthlys);
    }

    public function removeTraffic95thMonthly(\Entities\Traffic95thMonthly $traffic95thMonthlys)
    {
        $this->__load();
        return parent::removeTraffic95thMonthly($traffic95thMonthlys);
    }

    public function getTraffic95thMonthlys()
    {
        $this->__load();
        return parent::getTraffic95thMonthlys();
    }

    public function addTrafficDailie(\Entities\TrafficDaily $trafficDailies)
    {
        $this->__load();
        return parent::addTrafficDailie($trafficDailies);
    }

    public function removeTrafficDailie(\Entities\TrafficDaily $trafficDailies)
    {
        $this->__load();
        return parent::removeTrafficDailie($trafficDailies);
    }

    public function getTrafficDailies()
    {
        $this->__load();
        return parent::getTrafficDailies();
    }

    public function setNoc24hphone($noc24hphone)
    {
        $this->__load();
        return parent::setNoc24hphone($noc24hphone);
    }

    public function getNoc24hphone()
    {
        $this->__load();
        return parent::getNoc24hphone();
    }

    public function addSecEvent(\Entities\SecEvent $secEvents)
    {
        $this->__load();
        return parent::addSecEvent($secEvents);
    }

    public function removeSecEvent(\Entities\SecEvent $secEvents)
    {
        $this->__load();
        return parent::removeSecEvent($secEvents);
    }

    public function getSecEvents()
    {
        $this->__load();
        return parent::getSecEvents();
    }

    public function hasLeft()
    {
        $this->__load();
        return parent::hasLeft();
    }

    public function getAdminUsers()
    {
        $this->__load();
        return parent::getAdminUsers();
    }

    public function isTypeFull()
    {
        $this->__load();
        return parent::isTypeFull();
    }

    public function isTypeAssociate()
    {
        $this->__load();
        return parent::isTypeAssociate();
    }

    public function isTypeInternal()
    {
        $this->__load();
        return parent::isTypeInternal();
    }

    public function isTypeProBono()
    {
        $this->__load();
        return parent::isTypeProBono();
    }

    public function hasPrivateVLANs()
    {
        $this->__load();
        return parent::hasPrivateVLANs();
    }

    public function getPrivateVlanDetails()
    {
        $this->__load();
        return parent::getPrivateVlanDetails();
    }

    public function addRSPrefixes(\Entities\RSPrefix $rSPrefixes)
    {
        $this->__load();
        return parent::addRSPrefixes($rSPrefixes);
    }

    public function removeRSPrefixes(\Entities\RSPrefix $rSPrefixes)
    {
        $this->__load();
        return parent::removeRSPrefixes($rSPrefixes);
    }

    public function getRSPrefixes()
    {
        $this->__load();
        return parent::getRSPrefixes();
    }

    public function isRouteServerClient($proto = 4)
    {
        $this->__load();
        return parent::isRouteServerClient($proto);
    }

    public function isIPvXEnabled($proto = 4)
    {
        $this->__load();
        return parent::isIPvXEnabled($proto);
    }

    public function isIrrdbFiltered()
    {
        $this->__load();
        return parent::isIrrdbFiltered();
    }

    public function isAS112Client()
    {
        $this->__load();
        return parent::isAS112Client();
    }

    public function setIRRDB(\Entities\IRRDBConfig $iRRDB = NULL)
    {
        $this->__load();
        return parent::setIRRDB($iRRDB);
    }

    public function getIRRDB()
    {
        $this->__load();
        return parent::getIRRDB();
    }

    public function setPeeringDb($peeringDb)
    {
        $this->__load();
        return parent::setPeeringDb($peeringDb);
    }

    public function getPeeringDb()
    {
        $this->__load();
        return parent::getPeeringDb();
    }

    public function addNote(\Entities\CustomerNote $notes)
    {
        $this->__load();
        return parent::addNote($notes);
    }

    public function removeNote(\Entities\CustomerNote $notes)
    {
        $this->__load();
        return parent::removeNote($notes);
    }

    public function getNotes()
    {
        $this->__load();
        return parent::getNotes();
    }

    public function setPeeringmacrov6($peeringmacrov6)
    {
        $this->__load();
        return parent::setPeeringmacrov6($peeringmacrov6);
    }

    public function getPeeringmacrov6()
    {
        $this->__load();
        return parent::getPeeringmacrov6();
    }

    public function setRegistrationDetails(\Entities\CompanyRegisteredDetail $registrationDetails)
    {
        $this->__load();
        return parent::setRegistrationDetails($registrationDetails);
    }

    public function getRegistrationDetails()
    {
        $this->__load();
        return parent::getRegistrationDetails();
    }

    public function setBillingDetails(\Entities\CompanyBillingDetail $billingDetails)
    {
        $this->__load();
        return parent::setBillingDetails($billingDetails);
    }

    public function getBillingDetails()
    {
        $this->__load();
        return parent::getBillingDetails();
    }

    public function setAbbreviatedName($abbreviatedName)
    {
        $this->__load();
        return parent::setAbbreviatedName($abbreviatedName);
    }

    public function getAbbreviatedName()
    {
        $this->__load();
        return parent::getAbbreviatedName();
    }

    public function setMD5Support($mD5Support)
    {
        $this->__load();
        return parent::setMD5Support($mD5Support);
    }

    public function getMD5Support()
    {
        $this->__load();
        return parent::getMD5Support();
    }

    public function setIsReseller($isReseller)
    {
        $this->__load();
        return parent::setIsReseller($isReseller);
    }

    public function getIsReseller()
    {
        $this->__load();
        return parent::getIsReseller();
    }

    public function isReseller()
    {
        $this->__load();
        return parent::isReseller();
    }

    public function isResoldCustomer()
    {
        $this->__load();
        return parent::isResoldCustomer();
    }

    public function addResoldCustomer(\Entities\Customer $resoldCustomers)
    {
        $this->__load();
        return parent::addResoldCustomer($resoldCustomers);
    }

    public function removeResoldCustomer(\Entities\Customer $resoldCustomers)
    {
        $this->__load();
        return parent::removeResoldCustomer($resoldCustomers);
    }

    public function getResoldCustomers()
    {
        $this->__load();
        return parent::getResoldCustomers();
    }

    public function setReseller(\Entities\Customer $reseller = NULL)
    {
        $this->__load();
        return parent::setReseller($reseller);
    }

    public function getReseller()
    {
        $this->__load();
        return parent::getReseller();
    }

    public function addIXP(\Entities\IXP $iXPs)
    {
        $this->__load();
        return parent::addIXP($iXPs);
    }

    public function removeIXP(\Entities\IXP $iXPs)
    {
        $this->__load();
        return parent::removeIXP($iXPs);
    }

    public function getIXPs()
    {
        $this->__load();
        return parent::getIXPs();
    }

    public function addIrrdbPrefixes(\Entities\IrrdbPrefix $irrdbPrefixes)
    {
        $this->__load();
        return parent::addIrrdbPrefixes($irrdbPrefixes);
    }

    public function removeIrrdbPrefixes(\Entities\IrrdbPrefix $irrdbPrefixes)
    {
        $this->__load();
        return parent::removeIrrdbPrefixes($irrdbPrefixes);
    }

    public function getIrrdbPrefixes($proto = false)
    {
        $this->__load();
        return parent::getIrrdbPrefixes($proto);
    }

    public function resolveAsMacro($protocol = 4, $asnPrefix = '')
    {
        $this->__load();
        return parent::resolveAsMacro($protocol, $asnPrefix);
    }

    public function addIrrdbPrefixe(\Entities\IrrdbPrefix $irrdbPrefixes)
    {
        $this->__load();
        return parent::addIrrdbPrefixe($irrdbPrefixes);
    }

    public function removeIrrdbPrefixe(\Entities\IrrdbPrefix $irrdbPrefixes)
    {
        $this->__load();
        return parent::removeIrrdbPrefixe($irrdbPrefixes);
    }

    public function addRSPrefixe(\Entities\RSPrefix $rSPrefixes)
    {
        $this->__load();
        return parent::addRSPrefixe($rSPrefixes);
    }

    public function removeRSPrefixe(\Entities\RSPrefix $rSPrefixes)
    {
        $this->__load();
        return parent::removeRSPrefixe($rSPrefixes);
    }

    public function addIrrdbASN(\Entities\IrrdbAsn $irrdbASNs)
    {
        $this->__load();
        return parent::addIrrdbASN($irrdbASNs);
    }

    public function removeIrrdbASN(\Entities\IrrdbAsn $irrdbASNs)
    {
        $this->__load();
        return parent::removeIrrdbASN($irrdbASNs);
    }

    public function getIrrdbASNs()
    {
        $this->__load();
        return parent::getIrrdbASNs();
    }

    public function statusIsNormal()
    {
        $this->__load();
        return parent::statusIsNormal();
    }

    public function statusIsNotConnected()
    {
        $this->__load();
        return parent::statusIsNotConnected();
    }

    public function statusIsSuspended()
    {
        $this->__load();
        return parent::statusIsSuspended();
    }

    public function isUniqueMonitorIndex($i)
    {
        $this->__load();
        return parent::isUniqueMonitorIndex($i);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'name', 'type', 'shortname', 'abbreviatedName', 'autsys', 'maxprefixes', 'peeringemail', 'nocphone', 'noc24hphone', 'nocfax', 'nocemail', 'nochours', 'nocwww', 'peeringmacro', 'peeringmacrov6', 'peeringpolicy', 'corpwww', 'datejoin', 'dateleave', 'status', 'activepeeringmatrix', 'peeringDb', 'lastupdated', 'lastupdatedby', 'creator', 'created', 'MD5Support', 'isReseller', 'id', 'IrrdbASNs', 'IrrdbPrefixes', 'Notes', 'VirtualInterfaces', 'ResoldCustomers', 'Contacts', 'ConsoleServerConnections', 'CustomerEquipment', 'Peers', 'PeersWith', 'XCusts', 'YCusts', 'RSPrefixes', 'Users', 'Traffic95ths', 'Traffic95thMonthlys', 'TrafficDailies', 'SecEvents', 'IRRDB', 'RegistrationDetails', 'BillingDetails', 'Reseller', 'IXPs');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}

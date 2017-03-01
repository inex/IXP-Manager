<?php

namespace Proxies\__CG__\Entities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class PatchPanelPort extends \Entities\PatchPanelPort implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'name', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'state', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'notes', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'assigned_at', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'connected_at', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'cease_requested_at', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'ceased_at', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'last_state_change', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'internal_use', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'chargeable', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'id', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'switchPort', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'patchPanelPortHistory', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'patchPanel', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'customer'];
        }

        return ['__isInitialized__', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'name', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'state', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'notes', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'assigned_at', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'connected_at', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'cease_requested_at', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'ceased_at', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'last_state_change', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'internal_use', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'chargeable', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'id', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'switchPort', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'patchPanelPortHistory', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'patchPanel', '' . "\0" . 'Entities\\PatchPanelPort' . "\0" . 'customer'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (PatchPanelPort $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$name]);

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', []);

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setState($state)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setState', [$state]);

        return parent::setState($state);
    }

    /**
     * {@inheritDoc}
     */
    public function getState()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getState', []);

        return parent::getState();
    }

    /**
     * {@inheritDoc}
     */
    public function setNotes($notes)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNotes', [$notes]);

        return parent::setNotes($notes);
    }

    /**
     * {@inheritDoc}
     */
    public function getNotes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNotes', []);

        return parent::getNotes();
    }

    /**
     * {@inheritDoc}
     */
    public function setAssignedAt($assignedAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAssignedAt', [$assignedAt]);

        return parent::setAssignedAt($assignedAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getAssignedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAssignedAt', []);

        return parent::getAssignedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setConnectedAt($connectedAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setConnectedAt', [$connectedAt]);

        return parent::setConnectedAt($connectedAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getConnectedAt', []);

        return parent::getConnectedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setCeaseRequestedAt($ceaseRequestedAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCeaseRequestedAt', [$ceaseRequestedAt]);

        return parent::setCeaseRequestedAt($ceaseRequestedAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getCeaseRequestedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCeaseRequestedAt', []);

        return parent::getCeaseRequestedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setCeasedAt($ceasedAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCeasedAt', [$ceasedAt]);

        return parent::setCeasedAt($ceasedAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getCeasedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCeasedAt', []);

        return parent::getCeasedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setLastStateChange($lastStateChange)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastStateChange', [$lastStateChange]);

        return parent::setLastStateChange($lastStateChange);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastStateChange()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLastStateChange', []);

        return parent::getLastStateChange();
    }

    /**
     * {@inheritDoc}
     */
    public function setInternalUse($internalUse)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setInternalUse', [$internalUse]);

        return parent::setInternalUse($internalUse);
    }

    /**
     * {@inheritDoc}
     */
    public function getInternalUse()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getInternalUse', []);

        return parent::getInternalUse();
    }

    /**
     * {@inheritDoc}
     */
    public function setChargeable($chargeable)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setChargeable', [$chargeable]);

        return parent::setChargeable($chargeable);
    }

    /**
     * {@inheritDoc}
     */
    public function getChargeable()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getChargeable', []);

        return parent::getChargeable();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setSwitchPort(\Entities\SwitchPort $switchPort = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSwitchPort', [$switchPort]);

        return parent::setSwitchPort($switchPort);
    }

    /**
     * {@inheritDoc}
     */
    public function getSwitchPort()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSwitchPort', []);

        return parent::getSwitchPort();
    }

    /**
     * {@inheritDoc}
     */
    public function addPatchPanelPortHistory(\Entities\PatchPanelPortHistory $patchPanelPortHistory)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addPatchPanelPortHistory', [$patchPanelPortHistory]);

        return parent::addPatchPanelPortHistory($patchPanelPortHistory);
    }

    /**
     * {@inheritDoc}
     */
    public function removePatchPanelPortHistory(\Entities\PatchPanelPortHistory $patchPanelPortHistory)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removePatchPanelPortHistory', [$patchPanelPortHistory]);

        return parent::removePatchPanelPortHistory($patchPanelPortHistory);
    }

    /**
     * {@inheritDoc}
     */
    public function getPatchPanelPortHistory()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPatchPanelPortHistory', []);

        return parent::getPatchPanelPortHistory();
    }

    /**
     * {@inheritDoc}
     */
    public function setPatchPanel(\Entities\PatchPanel $patchPanel = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPatchPanel', [$patchPanel]);

        return parent::setPatchPanel($patchPanel);
    }

    /**
     * {@inheritDoc}
     */
    public function getPatchPanel()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPatchPanel', []);

        return parent::getPatchPanel();
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomer(\Entities\Customer $customer = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCustomer', [$customer]);

        return parent::setCustomer($customer);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomer()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCustomer', []);

        return parent::getCustomer();
    }

}

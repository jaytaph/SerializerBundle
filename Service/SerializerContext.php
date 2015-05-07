<?php

namespace Noxlogic\SerializerBundle\Service;

class SerializerContext
{
    protected $version = null;
    protected $groups = array('DEFAULT');
    protected $inCollection = false;

    public static function create()
    {
        return new self();
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
        $this->groups = array_map(function ($group) { return strtoupper($group); }, $this->groups);
    }

    /**
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param null $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @param $version
     *
     * @return bool
     */
    public function sinceVersion($version)
    {
        return version_compare($this->version, $version, '>=');
    }

    /**
     * @param $version
     *
     * @return bool
     */
    public function untilVersion($version)
    {
        return version_compare($this->version, $version, '<');
    }

    /**
     * @param $group
     *
     * @return bool
     */
    public function hasGroup($group)
    {
        return in_array(strtoupper($group), $this->groups);
    }

    /**
     * @return bool
     */
    public function isInCollection()
    {
        return $this->inCollection;
    }

    /**
     * @param bool $inCollection
     */
    public function setInCollection($inCollection)
    {
        $this->inCollection = $inCollection;
    }
}

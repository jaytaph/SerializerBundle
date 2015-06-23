<?php

namespace Noxlogic\SerializerBundle\Service;

class SerializerContext
{
    protected $version = null;
    protected $groups = array();

    /**
     * Fluent interface creator.
     *
     * @return SerializerContext
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Returns all the groups configured.
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Sets an array of groups.
     *
     * @param array $groups
     *
     * @return $this
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
        $this->groups = array_map(function ($group) { return strtoupper($group); }, $this->groups);

        return $this;
    }

    /**
     * @param $group
     *
     * @return $this
     */
    public function addGroup($group)
    {
        $this->groups[] = strtoupper($group);

        return $this;
    }

    /**
     * Return the current version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the current version.
     *
     * @param null $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Checks if the given version is higher or equal than the current defined.
     *
     * @param $version
     *
     * @return bool
     */
    public function sinceVersion($version)
    {
        if ($this->version == null) {
            return true;
        }

        return version_compare($this->version, $version, '>=');
    }

    /**
     * Checks if the given version is lower than the current defined.
     *
     * @param $version
     *
     * @return bool
     */
    public function untilVersion($version)
    {
        if ($this->version == null) {
            return true;
        }

        return version_compare($this->version, $version, '<');
    }

    /**
     * return true when the given group is current defined.
     *
     * @param $group
     *
     * @return bool
     */
    public function hasGroup($group)
    {
        return in_array(strtoupper($group), array_merge(array('DEFAULT'), $this->groups));
    }
}

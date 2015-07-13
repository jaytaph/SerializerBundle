<?php

namespace Noxlogic\SerializerBundle\Service;

class SerializerContext
{
    protected $version = null;
    protected $groups = array();
    protected $maximum_depth = 10;
    protected $current_depth = 0;
    protected $embedded_stack = array();
    protected $recursive = false;

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
     * @param $group
     */
    public function removeGroup($group)
    {
        $group = strtoupper($group);
        if (isset($this->groups[$group])) {
            unset($this->groups[$group]);
        }
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


    /**
     * @return int
     */
    public function getCurrentDepth()
    {
        return $this->current_depth;
    }

    /**
     * @param int $current_depth
     *
     * @return $this
     */
    public function setCurrentDepth($current_depth)
    {
        $this->current_depth = $current_depth;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaximumDepth()
    {
        return $this->maximum_depth;
    }

    /**
     * @param int $maximum_depth
     *
     * @return $this
     */
    public function setMaximumDepth($maximum_depth)
    {
        $this->maximum_depth = $maximum_depth;

        return $this;
    }

    public function push($id) {
        array_push($this->embedded_stack, $id);
    }
    public function pop() {
        return array_pop($this->embedded_stack);
    }

    public function has($id) {
        return in_array($id, $this->embedded_stack);
    }

    public function canRecurse() {
        return $this->recursive;
    }

    public function setRecursive($recursive) {
        $this->recursive = $recursive;
    }

}

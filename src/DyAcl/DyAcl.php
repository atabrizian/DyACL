<?php
/**
 * Author: Arash Tabriziyan <a.tabriziyan@gmail.com>
 * Github User: ghost098
 *
 * Date: 9/6/13
 *
 * Copyright (C) 2013 Arash Tabriziyan
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace DyAcl;

/**
 * Class DyAcl
 * This is a simple but handy ACL
 *
 * @package DyAcl
 * @author: Arash Tabriziyan <a.tabriziyan@gmail.com>
 */
class DyAcl
{
    /**
     * Privilege type: allow
     */
    const ALLOW = 'allow';

    /**
     * Privilege type: deny
     */
    const DENY = 'deny';

    const ACTION_ALL = 'all';
    const ACTION_CREATE = 'create';
    const ACTION_READ = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    private $allPossibleActions = array(
        self::ACTION_CREATE,
        self::ACTION_READ,
        self::ACTION_UPDATE,
        self::ACTION_DELETE
    );

    /**
     * A collection of current user's roles
     *
     * @var array null
     */
    private $roles = null;

    /**
     * A collection of resources
     * A resource can be anything that we need to manage our users' access
     * to such as controller/method, file, directory, etc
     * Access to all resources that are not known by class are denied by default
     *
     * @var array null
     */
    private $resources = null;

    /**
     * A Multidimensional array that includes all rules about current user
     *
     * @var array null
     */
    private $rules = null;

    private $rulesByRole = null;

    /**
     * Checks whether the resource is currently registered or not
     *
     * @param string $resource A resource which can be anything that we need to
     * manage our users access to such as controller/method, file, directory, etc
     *
     * @return bool true if the resource is found and false on failure
     */
    public function hasResource($resource)
    {
        return isset($this->resources[$resource]) ? true : false;
    }

    /**
     * Adds a resource to the collection
     *
     * @param string $resource A resource which can be anything that we need to
     * manage our users access to such as controller/method, file, directory, etc
     *
     * @return bool true on success, false on failure
     */
    public function addResource($resource)
    {
        if (!$this->hasResource($resource)) {
            $this->resources[] = $resource;
        }
    }

    public function addResources($resources)
    {
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    /**
     * Allow access to specific resource on all or specific action
     *
     * @param string $resource A resource which can be anything that we need to
     * manage our users access to such as controller/method, file, directory, etc
     * @param string $action Action will be set to 'all' by default but other
     * possible actions are Create, Read, Update, Delete and any other action that
     * you mention!
     */
    public function allow($resource, $action = null)
    {
        $this->setRule($resource, $action, self::ALLOW);
    }

    public function forceAllow($resource, $action = null)
    {
        $this->setForceRule($resource, $action, self::ALLOW);
    }

    /**
     * Deny access to specific resource on all or specific action
     *
     * @param string $resource A resource which can be anything that we need to
     * manage our users access to such as controller/method, file, directory, etc
     * @param string $action Action will be set to 'all' by default but other
     * possible actions are Create, Read, Update, Delete and any other action that
     * you mention!
     */
//    public function deny($resource, $action = null)
//    {
//        $this->setRule($resource, $action, self::DENY);
//    }

    public function forceDeny($resource, $action = null)
    {
        $this->setForceRule($resource, $action, self::DENY);
    }

    /**
     * Assign a new role to current user
     *
     * @param int|string $role The role of the user
     * @return bool true on success and false on repeated role
     */
    public function setRole($role)
    {
        if (!isset($this->role[$role])) {
            $this->roles[] = $role;
            return true;
        }
        return false;
    }

    /**
     * Assign new roles to current user
     *
     * @param array $roles An array of current users roles
     * @return bool
     */
    public function setRoles($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                $this->setRole($role);
            }
        }
    }

    /**
     * Check the role exists or not
     *
     * @param string $role A specific role
     * @return bool
     */
    public function hasRole($role)
    {
        return (isset($this->roles[$role])) ? true : false;
    }

    /**
     * Returns the current users roles
     *
     * @return array|null
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set an access rule
     *
     * @param string $resource A resource which can be anything that we need to
     * manage our users access to such as controller/method, file, directory, etc
     * @param string $privilege ALLOW and DENY are only possible values
     * @param string $action Action will be set to 'all' by default but other
     * possible actions are Create, Read, Update, Delete and any other action that
     */
    public function setRule($resource, $privilege, $action = self::ACTION_ALL)
    {
        if (!$this->hasResource($resource)) {
            $this->addResource($resource);
        }

        if ($action == self::ACTION_ALL) {
            foreach ($this->allPossibleActions as $action) {
                if (!isset($this->rules[$resource][$action])) {
                    $this->rules[$resource][$action] = $privilege;
                } else {
                    $this->rules[$resource][$action] = $this->permissionAnd(
                        $this->rules[$resource][$action],
                        $privilege
                    );
                }
            }
        } else {
            if (!isset($this->rules[$resource][$action])) {
                $this->rules[$resource][$action] = $privilege;
            } else {
                $this->rules[$resource][$action] = $this->permissionAnd($this->rules[$resource][$action], $privilege);
            }
        }

    }

    public function setForceRule($resource, $privilege, $action = self::ACTION_ALL)
    {
        if (!$this->hasResource($resource)) {
            $this->addResource($resource);
        }

        if ($action == self::ACTION_ALL) {
            foreach ($this->allPossibleActions as $action) {
                $this->rules[$resource][$action] = $privilege;
            }
        } else {
            $this->rules[$resource][$action] = $privilege;
        }
    }

    /**
     * Set batch rules
     *
     * @param array $rules An array of rules which should include resource, privilige and
     * action for each rule
     *
     * @return bool
     */
    public function setRules($rules)
    {
        if (is_array($rules)) {
            foreach ($rules as $rule) {
                $this->setRule($rule['resource'], $rule['privilege'], $rule['action']);
            }
            return true;
        }
        return false;
    }

    public function setForceRules($rules)
    {
        if (is_array($rules)) {
            foreach ($rules as $rule) {
                $this->setForceRule($rule['resource'], $rule['privilege'], $rule['action']);
            }
            return true;
        }
        return false;
    }

    /**
     * Get all rules that are set
     *
     * @return array|null an array of rules or null if nothing is set.
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Whether the user is allowed to access the resource or not
     *
     * @param string $resource A resource which can be anything that we need to
     * manage our users access to such as controller/method, file, directory, etc
     * @param string $action Action will be set to 'all' by default but other
     * possible actions are Create, Read, Update, Delete and any other action that
     * @return bool true if allowed and false if denied
     */
    public function isAllowed($resource, $action = self::ACTION_ALL)
    {
        if($action == self::ACTION_ALL) {
            foreach($this->allPossibleActions as $action) {
                if($this->rules[$resource][$action] !== self::ALLOW) {
                    return false;
                }
            }
            return true;
        }
        else {
            return ((isset($this->rules[$resource][$action]) and $this->rules[$resource][$action] === 'allow') ? true : false);
        }

    }

    /**
     * A utility function that ORs two different privilege which can be allow or deny
     * If two different rules are available for a user the generous one is effective!
     * For example if one rule allows a resource and another rule denies that
     * specific resource the result is 'allow'
     *
     * @param string $A allow or deny
     * @param string $B allow or deny
     * @return string allow or deny
     */
    private function permissionAnd($A, $B)
    {
        $X = ($A === self::ALLOW) ? 1 : 0;
        $Y = ($B === self::ALLOW) ? 1 : 0;

        return (($X & $Y) == 1) ? 'allow' : 'deny';
    }
}
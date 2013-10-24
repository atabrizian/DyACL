<?php
/**
 * Author: Arash Tabriziyan <a.tabriziyan@gmail.com>
 * Github User: ghost098
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

use PDO;

class DyAclPDO extends DyAcl
{
    const DB_USER_ROLES = 'users_roles';
    const DB_ROLES_RESOURCES = 'roles_resources';
    const DB_RESOURCES = 'resources';
    const DB_ROLES = 'roles';

    private $pdo;

    public function __construct($dsn, $username, $password)//, $options = null)
    {
        $this->pdo = new PDO($dsn, $username, $password);//, $options);
    }

    public function prepareAcl($user_id)
    {
        $this->flush();
        $ph = $this->pdo->prepare("SELECT `role_id` FROM `".self::DB_USER_ROLES."` WHERE `user_id` = :user_id;");
        if($ph->execute(array(':user_id' => $user_id))) {
            $roles = $ph->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_COLUMN, 'role_id');
            $ph->closeCursor();

            if($roles !== false) {
                $this->setRoles($roles);

                foreach($roles as $role) {
                    $ph = $this->pdo->prepare("SELECT * FROM `".self::DB_ROLES_RESOURCES."` WHERE `role_id` = :role_id");

                    if($ph->execute(array(':role_id'=> $role))) {
                        $rules = $ph->fetchAll(PDO::FETCH_ASSOC);
                        $ph->closeCursor();

                        if($rules !== false) {
                            $this->setRules($rules);
                        }
                        else {
                            throw new \Exception("Rule selection failed!");
                        }
                    }
                    else {
                        throw new \Exception("Rule selection failed!");
                    }
                }

                return true;
            }
            else {
                throw new \Exception("Role selection failed!");
            }
        }
        else {
            throw new \Exception("Role selection failed!");
        }
    }

    public function addResourceToDB($resourceName)
    {
        $ph = $this->pdo->prepare("SELECT count(*) as `cnt` FROM `".self::DB_RESOURCES."` WHERE `name` = :name;");
        if($ph->execute(array(':name' => $resourceName))) {
            $cnt = $ph->fetch(PDO::FETCH_ASSOC);
            $ph->closeCursor();

            if($cnt['cnt'] == 0) {
                $ph = $this->pdo->prepare("Insert Into `".self::DB_RESOURCES."` (name) VALUES (:name)");
                if($ph->execute(array(':name' => $resourceName))) {
                    $this->addResource($resourceName);
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                throw new \Exception("Resource already exists!");
            }
        }
        else {
            throw new \Exception("Resource selection failed!");
        }
    }

    public function addUserRoleToDB($userId, $roleId)
    {
        $ph = $this->pdo->prepare("SELECT count(*) as `cnt` FROM `".self::DB_USER_ROLES."` WHERE `user_id` = :user_id and `role_id` = :role_id;");
        if($ph->execute(array(':user_id' => $userId, ':role_id' => $roleId))) {
            $cnt = $ph->fetch(PDO::FETCH_ASSOC | PDO::FETCH_COLUMN);
            $ph->closeCursor();

            if($cnt['cnt'] == 0) {
                $ph = $this->pdo->prepare("Insert Into `".self::DB_RESOURCES."` (user_id, role_id) VALUES (:user_id, :role_id)");
                if($ph->execute(array(':user_id' => $userId, ':role_id' => $roleId))) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                throw new \Exception("Role already exists!");
            }
        }
        else {
            throw new \Exception("Role selection failed!");
        }
    }

    public function addRuleToDB($roleId, $resourceId, $action, $privilege = DyAcl::ALLOW)
    {
        $ph = $this->pdo->prepare("SELECT count(*) as `cnt` FROM `".self::DB_ROLES_RESOURCES."` WHERE `role_id` = :role_id and `resource` = :resource and `action` = :action and `privilege` = :privilege;");
        if($ph->execute(array(':role_id' => $roleId, ':resource' => $resourceId, ':action' => $action, ':privilege' => $privilege))) {
            $cnt = $ph->fetch(PDO::FETCH_ASSOC | PDO::FETCH_COLUMN);
            $ph->closeCursor();

            if($cnt['cnt'] == 0) {
                $ph = $this->pdo->prepare("Insert Into `".self::DB_ROLES_RESOURCES."` (role_id, resource, action, privilege) VALUES (:role_id, :resource, :action, :privilege)");
                if($ph->execute(array(':role_id' => $roleId, ':resource' => $resourceId, ':action' => $action, ':privilege'=> $privilege))) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                throw new \Exception("Rule already exists!");
            }
        }
        else {
            throw new \Exception("Rule selection failed!");
        }
    }

    public function removeResourceFromDB($name)
    {
        $ph = $this->pdo->prepare("DELETE FROM `".self::DB_USER_ROLES."` WHERE `name` = :name");
        if($ph->execute(array(':name' => $name))) {
            return true;
        }
        else {
            return false;
        }
    }

    public function removeUserRoleFromDB($userId, $roleId)
    {
        $ph = $this->pdo->prepare("DELETE FROM `".self::DB_USER_ROLES."` WHERE `user_id` = :user_id AND `role_id` = :role_id");
        if($ph->execute(array(':user_id' => $userId, ':role_id' => $roleId))) {
            return true;
        }
        else {
            return false;
        }
    }

    public function removeRuleFromDB($roleId, $resource, $action, $privilege)
    {
        $ph = $this->pdo->prepare("DELETE FROM `".self::DB_ROLES_RESOURCES."` WHERE `role_id` = :role_id AND `resource` = :resource AND `action` = :action AND `privilege` = :privilege");
        if($ph->execute(array(':role_id' => $roleId, ':resource' => $resource, ':action' => $action, ':privilege' => $privilege))) {
            return true;
        }
        else {
            return false;
        }
    }
}
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
    private $pdo;

    public function __construct($dsn, $username, $password)//, $options = null)
    {
        $this->pdo = new PDO($dsn, $username, $password);//, $options);
    }

    public function prepareAcl($user_id)
    {
        $this->flush();
        $ph = $this->pdo->prepare("SELECT `role_id` FROM `users_roles` WHERE `user_id` = :user_id;");
        if($ph->execute(array(':user_id' => $user_id))) {
            $roles = $ph->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_COLUMN, 'role_id');
            $ph->closeCursor();

            if($roles !== false) {
                $this->setRoles($roles);

                foreach($roles as $role) {
                    $ph = $this->pdo->prepare("SELECT * FROM `roles_resources` WHERE `role_id` = :role_id");

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

    public function addResourceToDB()
    {

    }

    public function addUserRoleToDB()
    {

    }

    public function addRuleToDB()
    {

    }

    public function removeResourceFromDB()
    {

    }

    public function removeUserRoleFromDB()
    {

    }

    public function removeRuleFromDB()
    {

    }

    public function updateRuleInDB()
    {

    }
}
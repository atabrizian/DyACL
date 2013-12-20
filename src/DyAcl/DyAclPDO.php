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

/**
 * Class DyAclPDO
 *
 * @package DyAcl
 */
class DyAclPDO extends DyAclToDb
{
    /**
     * @var \PDO PDO object
     */
    private $pdo;

    /**
     * Constructor
     *
     */
    public function __construct($pdo, $configFile = null)
    {
        parent::__construct($configFile);
        $this->pdo = $pdo;
    }

    /**
     * Loads acl rules from database according to user_id
     *
     * @param int $userId User's id
     *
     * @throws \Exception
     * @return bool
     */
    public function prepareAcl($userId)
    {
        $this->flush();
        $ph = $this->pdo->prepare(
            "SELECT `" . $this->usersRolesFkToRoles
            . "` FROM `" . $this->usersRolesTblName
            . "` WHERE `" . $this->usersRolesFkToUsers . "` = :user_id;"
        );

        if ($ph->execute(array(':user_id' => $userId))) {
            $roles = $ph->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_COLUMN);
            $ph->closeCursor();

            if ($roles) {
                $this->setRoles($roles);

                $ph = $this->pdo->prepare(
                    "SELECT `" . $this->rulesFkToResources . "` as `resource`, `"
                    . $this->rulesPrivilegeField . "` as `privilege`, `"
                    . $this->rulesActionField . "` as `action` FROM `"
                    . $this->rulesTblName . "` WHERE `"
                    . $this->rulesFkToRoles . "` IN ('" . implode(
                        "', '",
                        $roles
                    ) . "')"
                );

                if ($ph->execute()) {
                    $rules = $ph->fetchAll(PDO::FETCH_ASSOC);
                    $ph->closeCursor();

                    if ($rules) {
                        $this->setRules($rules);
                    }
                    // eles no rule has been defined for this user's roles!!!
                } else {
                    //if this happen it means your users_roles table is not in
                    //correct format or your config is wrong
                    throw new DyAclException("Something wrong with database or Config!");
                }

                return true;
            }
            // else this user has no roles so everything is denied
        } else {
            //if this happen it means your users_roles table is not in
            //correct format or your config is wrong
            throw new DyAclException("Something wrong with database or Config!");
        }
    }
}

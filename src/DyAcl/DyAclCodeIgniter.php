<?php

namespace DyAcl;


/**
 * Class CodeIgniter
 *
 * @package DyAcl
 */
class DyAclCodeIgniter extends DyAclToDb
{
    /**
     * @var mixed An instance of CodeIgniter
     */
    private $CI;
    /**
     * @var istance of CI's db
     */
    private $db;

    public function __construct($ci)
    {
        $this->CI = $ci;
        $this->db = $this->CI->db;
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

        $this->db
            ->select($this->usersRolesFkToRoles)
            ->from($this->usersRolesTblName)
            ->where(array($this->usersRolesFkToUsers => $userId));

        $roles = $this->db->get();
        if ($roles->num_rows() > 0) {
            $this->setRoles($roles);


            $this->db
                ->select(
                    array(
                        "`" . $this->rulesFkToResources . "` as `resource`",
                        "`" . $this->rulesPrivilegeField . "` as `privilege`",
                        "`" . $this->rulesActionField . "` as `action`"
                    )
                )->from($this->rulesTblName)
                ->where("`" . $this->rulesFkToRoles . "` IN ('" . implode("', '", $roles) . "')");

            $rules = $this->db->get();
            if ($rules->num_rows > 0) {
                $this->setRules($rules);
            } else {
                throw new \Exception("Rule selection failed!");
            }

            return true;
        } else {
            throw new \Exception("Role selection failed!");
        }
    }
}

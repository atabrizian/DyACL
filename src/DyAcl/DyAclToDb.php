<?php
namespace DyAcl;

use DOMDocument;
use DOMXPath;

/**
 * Class dbFuncs
 *
 * @package DyAcl
 */
abstract class DyAclToDb extends DyAcl
{
    //Db Configuration will be loaded to these properties

    protected $usersRolesTblName;
    protected $usersRolesFkToUsers;
    protected $usersRolesFkToRoles;
    protected $rulesTblName;
    protected $rulesFkToRoles;
    protected $rulesFkToResources;
    protected $rulesPrivilegeField;
    protected $rulesActionField;

    protected $requiredDbConfigs = array(
        'usersRolesTblName',
        'usersRolesFkToUsers',
        'usersRolesFkToRoles',
        'rulesTblName',
        'rulesFkToRoles',
        'rulesFkToResources',
        'rulesPrivilegeField',
        'rulesActionField'
    );

    public function __construct($configFile = null)
    {
        parent::__construct();

        $this->setDbMap($configFile);
    }

    /**
     * load db config from xml file
     *
     * @param string $configFile Path to database config file which should be in
     *                           xml format
     *
     * @throws DyAclException
     * @return mixed
     */
    public function setDbMap($configFile)
    {
        if (!is_readable($configFile)) {
            throw new DyAclException("Config file is not accessible");
        }

        $dom = new DOMDocument();
        if (!$dom->load($configFile)) {
            throw new DyAclException("Config file can not be loaded.");
        }

        if ($dom->schemaValidate(__DIR__ . "/../../config/dbConfig.xsd")) {
            try {
                $xpath = new DOMXPath($dom);
                $list = $xpath->query("//configuration/part[@name='dyacl']/config");
                $configs = [];
                for ($i = 0; $i < $list->length; $i++) {
                    $configs[$list->item($i)->attributes->getNamedItem('name')->textContent]
                        = $list->item($i)->textContent;
                }

                foreach ($this->requiredDbConfigs as $configKey) {
                    if (!in_array($configKey, array_keys($configs))) {
                        throw new DyAclException("Invalid Key in database configuration" .
                        " file.");
                    }
                    $this->{$configKey} = $configs[$configKey];
                }
            } catch (\Exception $e) {
                throw new DyAclException($e->getMessage(), $e->getCode());
            }
        } else {
            throw new DyAclException("Config file is not in correct format. " .
            "Please check dbConfig.xsd for correct format.");
        }
    }

    /**
     * Prepare DyAcl using rules loaded from database
     *
     * @param int $userId User's id
     *
     * @return mixed
     */
    public abstract function prepareAcl($userId);
}

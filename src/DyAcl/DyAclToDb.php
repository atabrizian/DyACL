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
    public function setDbMap($configFile = null)
    {
        if (is_null($configFile)) {
            $configFile = __DIR__ . "/../../config/dbConfig.xml";
        }

        if (!is_readable($configFile)) {
            throw new DyAclException("Config file is not accessible");
        }

        $dom = new DOMDocument();
        try {
            $dom->load($configFile);
        } catch (\Exception $e) {
            throw new DyAclException($e->getMessage(), $e->getCode());
        }


        try {
            if ($dom->schemaValidate(__DIR__ . "/../../config/dbConfig.xsd")) {
                $xpath = new DOMXPath($dom);
                $list = $xpath->query("//configuration/part[@name='dyacl']/config");
                $configs = [];
                for ($i = 0; $i < $list->length; $i++) {
                    $configs[$list->item($i)->attributes->getNamedItem('name')->textContent]
                        = $list->item($i)->textContent;
                }

                foreach ($this->requiredDbConfigs as $configKey) {
                    if (!in_array($configKey, array_keys($configs))) {
                        throw new DyAclException($configKey . " was not set in config file.");
                    }
                    $this->{$configKey} = $configs[$configKey];
                }
            }
            //if validation fails it throughs an exception that will be caught and rethrown
        } catch (\Exception $e) {
            throw new DyAclException($e->getMessage(), $e->getCode());
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

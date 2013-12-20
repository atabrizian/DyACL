<?php
namespace DyAcl;

/**
 * Class DyAclFactory
 *
 * @package DyAcl
 */
class DyAclFactory
{
    /**
     * An instance of DyAcl class
     *
     * @return DyAcl
     */
    public static function newAcl()
    {
        return new DyAcl();
    }

    /**
     * An instance of DyAclPDO class
     *
     * @param \PDO $pdo A pdo instance to work with
     *
     * @param null|string $configFile Path to xml config file
     *
     * @return DyAclPDO
     */
    public static function newDyAclPDO($pdo, $configFile = null)
    {
        return new DyAclPDO($pdo, $configFile);
    }
}

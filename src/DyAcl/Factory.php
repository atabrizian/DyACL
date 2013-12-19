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
     * @return DyAclPDO
     */
    public static function newDyAclPDO($pdo)
    {
        return new DyAclPDO($pdo);
    }

    /**
     * An instance of DyAclCodeIgniter
     *
     * @param mixed $CI your codeigniters CI::get_instance()
     *
     * @return DyAclCodeIgniter
     */
    public static function newDyAclCI($CI)
    {
        return new DyAclCodeIgniter($CI);
    }
}

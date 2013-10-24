<?php
/**
 * Date: 10/11/13
 * Time: 2:35 AM
 */
namespace DyAcl;

use DyAcl\DyAcl;

/**
 * These are tests regarding DyAcl class
 *
 * Class DyACLTest
 * @package DyAcl
 */
class DyAclTest extends \PHPUnit_Framework_TestCase {

    private $sampleHost = "localhost";
    private $sampleDbName = "DyACL";
    private $sampleUsername = "testUsername";
    private $samplePassword = "testPassword";

    /**
     * This test is supposed to set rules oith "setRule" command and test isAllowed
     */
    public function testIsAllowedDyACL()
    {
        $dyacl = new DyAcl();

//        $dyacl->setRole('user');//it's better to use user_id and here it'- not needed

        $dyacl->setRule('public', DyAcl::ALLOW);//it's better to use resource_id
        $dyacl->setRule('user_can_only_read', DyAcl::ALLOW, DyAcl::ACTION_READ);
        $dyacl->setRule('user_can_not_delete', DyAcl::ALLOW, DyAcl::ACTION_CREATE);
        $dyacl->setRule('user_can_not_delete', DyAcl::ALLOW, DyAcl::ACTION_READ);
        $dyacl->setRule('user_can_not_delete', DyAcl::ALLOW, DyAcl::ACTION_UPDATE);

        $this->assertTrue($dyacl->isAllowed('public', DyAcl::ACTION_CREATE));
        $this->assertTrue($dyacl->isAllowed('public', DyAcl::ACTION_READ));
        $this->assertTrue($dyacl->isAllowed('public', DyAcl::ACTION_UPDATE));
        $this->assertTrue($dyacl->isAllowed('public', DyAcl::ACTION_DELETE));
        $this->assertTrue($dyacl->isAllowed('public', DyAcl::ACTION_ALL));

        $this->assertFalse($dyacl->isAllowed('user_can_only_read', DyAcl::ACTION_CREATE));
        $this->assertTrue($dyacl->isAllowed('user_can_only_read', DyAcl::ACTION_READ));
        $this->assertFalse($dyacl->isAllowed('user_can_only_read', DyAcl::ACTION_UPDATE));
        $this->assertFalse($dyacl->isAllowed('user_can_only_read', DyAcl::ACTION_DELETE));
        $this->assertFalse($dyacl->isAllowed('user_can_only_read', DyAcl::ACTION_ALL));

        $this->assertTrue($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_CREATE));
        $this->assertTrue($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_READ));
        $this->assertTrue($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_UPDATE));
        $this->assertFalse($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_DELETE));
        $this->assertFalse($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_ALL));

        $this->assertFalse($dyacl->isAllowed('secret', DyAcl::ACTION_CREATE));
        $this->assertFalse($dyacl->isAllowed('secret', DyAcl::ACTION_READ));
        $this->assertFalse($dyacl->isAllowed('secret', DyAcl::ACTION_UPDATE));
        $this->assertFalse($dyacl->isAllowed('secret', DyAcl::ACTION_DELETE));
        $this->assertFalse($dyacl->isAllowed('secret', DyAcl::ACTION_ALL));
    }

    public function testForceDeny()
    {
        $dyacl = new DyAcl();

//        $dyacl->setRole('user');//it's better to use user_id and here it's not needed

        $dyacl->allow('public');//it's better to use resource_id
        $dyacl->allow('user_can_not_delete');
        $dyacl->deny('user_can_not_delete', DyAcl::ACTION_DELETE);

        $this->assertTrue($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_DELETE));

        $dyacl->forceDeny('user_can_not_delete', DyAcl::ACTION_DELETE);
        $this->assertFalse($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_DELETE));
    }

    public function testAddResource()
    {
        $dyacl = new DyAclPDO("mysql:host={$this->sampleHost};dbname={$this->sampleDbName};", $this->sampleUsername, $this->samplePassword);
        $this->assertTrue($dyacl->addResourceToDB('newResource'));
    }

    public function testAddUserRoleToDB()
    {
        $dyacl = new DyAclPDO("mysql:host={$this->sampleHost};dbname={$this->sampleDbName};", $this->sampleUsername, $this->samplePassword);
        $this->assertTrue($dyacl->addUserRoleToDB(4, 4));
    }

    public function testAddRuleToDB()
    {
        $dyacl = new DyAclPDO("mysql:host={$this->sampleHost};dbname={$this->sampleDbName};", $this->sampleUsername, $this->samplePassword);
        $this->assertTrue($dyacl->addRuleToDB(2, 5, DyAcl::ACTION_DELETE));
    }

    public function testRemoveResourceFromDB()
    {
        $dyacl = new DyAclPDO("mysql:host={$this->sampleHost};dbname={$this->sampleDbName};", $this->sampleUsername, $this->samplePassword);
        $this->assertTrue($dyacl->removeResourceFromDB(5));
    }

    public function testRemoveUserRoleFromDB()
    {
        $dyacl = new DyAclPDO("mysql:host={$this->sampleHost};dbname={$this->sampleDbName};", $this->sampleUsername, $this->samplePassword);
        $this->assertTrue($dyacl->removeUserRoleFromDB(4, 4));
    }

    public function testRemoveRuleFromDB()
    {
        $dyacl = new DyAclPDO("mysql:host={$this->sampleHost};dbname={$this->sampleDbName};", $this->sampleUsername, $this->samplePassword);
        $this->assertTrue($dyacl->removeRuleFromDB(2, 5, $dyacl::ACTION_DELETE, $dyacl::ALLOW));
    }
}
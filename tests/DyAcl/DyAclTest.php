<?php
/**
 * Date: 10/11/13
 * Time: 2:35 AM
 */
namespace DyAcl;

use DyAcl\DyAclFactory;

/**
 * These are tests regarding DyAcl class
 *
 * Class DyACLTest
 *
 * @package DyAcl
 */
class DyAclTest extends \PHPUnit_Framework_TestCase
{

    private $sampleHost = "localhost";
    private $sampleDbName = "DyACL";
    private $sampleUsername = "testUsername";
    private $samplePassword = "testPassword";

    /**
     * @var \PDO
     */
    private $pdo;

    public function setUp()
    {
        $this->pdo = new \PDO("mysql:host={$this->sampleHost};dbname={$this->sampleDbName};", $this->sampleUsername, $this->samplePassword);
    }

    /**
     * This test is supposed to set rules with "setRule" command and test isAllowed
     */
    public function testIsAllowedDyACL()
    {
        $dyacl = DyAclFactory::newAcl();

        $dyacl->setRole('user');

        $dyacl->setRule('public', DyAcl::ALLOW);
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

        $dyacl->setRule('secret', $dyacl::DENY);
        $this->assertFalse($dyacl->isAllowed('secret'));
        $dyacl->setRule('secret', $dyacl::ALLOW);
        $this->assertTrue($dyacl->isAllowed('secret'));
    }

    public function testForceDenyAndForceAllow()
    {
        $dyacl = DyAclFactory::newAcl();

//        $dyacl->setRole('user');//it's better to use user_id and here it's not needed

        $dyacl->allow('public'); //it's better to use resource_id
        $dyacl->allow('user_can_not_delete');
        $dyacl->deny('user_can_not_delete', DyAcl::ACTION_DELETE);

        $this->assertTrue($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_DELETE));

        $dyacl->forceDeny('user_can_not_delete', DyAcl::ACTION_DELETE);
        $this->assertFalse($dyacl->isAllowed('user_can_not_delete', DyAcl::ACTION_DELETE));
        $dyacl->forceDeny('public');
        $this->assertFalse($dyacl->isAllowed('public'));
    }

    public function testDyAclPdo()
    {
        $dyAcl = DyAclFactory::newDyAclPDO($this->pdo);

        $sampleUserId = 1;
        $dyAcl->prepareAcl($sampleUserId);

        $this->assertEquals(2, $dyAcl->getRoles()[0]);
        $this->assertEquals(
            array(
                'create' => 'allow',
                'read' => 'allow',
                'update' => 'allow',
                'delete' => 'allow'
            ),
            $dyAcl->getRules()['public']
        );
        $this->assertFalse($dyAcl->isAllowed('secret'));
        $this->assertTrue($dyAcl->isAllowed('public'));
        $this->assertFalse($dyAcl->isAllowed('user_can_only_read'));
        $this->assertTrue($dyAcl->isAllowed('user_can_only_read', DyAclPDO::ACTION_READ));
        $this->assertFalse($dyAcl->isAllowed('user_can_not_delete'));
        $this->assertTrue($dyAcl->isAllowed('user_can_not_delete', DyAclPDO::ACTION_READ));
        $this->assertFalse($dyAcl->isAllowed('user_can_not_delete', DyAclPDO::ACTION_DELETE));

        $sampleUserId = 3;//not exist
        $dyAcl->prepareAcl($sampleUserId);
    }

    /**
     * These funcs are tested indirectly because they are used in other functions but
     * for the sake of code coverage i have to include them here
     */
    public function testUnnecessary()
    {
        $dyacl = DyAclFactory::newAcl();
        $dyacl->addResource('x');
        $dyacl->addResources(array('y', 'z'));
        $this->assertTrue($dyacl->hasResource('x'));
        $this->assertTrue($dyacl->hasResource('y'));
        $dyacl->allow('x');
        $this->assertTrue($dyacl->isAllowed('x'), $dyacl::ACTION_CREATE);
        $dyacl->setForceRules(
            array(
                array(
                    'resource' => 'y',
                    'action' => $dyacl::ACTION_ALL,
                    'privilege' => $dyacl::DENY
                ),
                array(
                    'resource' => 'x',
                    'action' => $dyacl::ACTION_CREATE,
                    'privilege' => $dyacl::DENY
                )
            )
        );
        $this->assertFalse($dyacl->isAllowed('x', $dyacl::ACTION_CREATE));
        $dyacl->setForceRule('newResource', $dyacl::DENY);
    }

    /**
     * test deleteRole
     */
    public function testDeleteRole()
    {
        $dyacl = DyAclFactory::newAcl();
        $dyacl->setRole('user');
        $dyacl->setRoles(array('moderator', 'admin'));

        $this->assertTrue($dyacl->hasRole('admin'));
        $dyacl->deleteRole('admin');
        $this->assertFalse($dyacl->hasRole('admin'));
    }

    /**
     * @expectedException \DyAcl\DyAclException
     * @expectedExceptionMessage Something wrong with database or Config!
     */
    public function testOuterWrongConfigException()
    {
        $dyacl = DyAclFactory::newDyAclPDO($this->pdo, __DIR__."/../wrong-configs/dbConfig-wrong-field-name-to-test.xml");
        $dyacl->prepareAcl(1);
    }

    /**
     * @expectedException \DyAcl\DyAclException
     * @expectedExceptionMessage Something wrong with database or Config!
     */
    public function testInnerWrongConfigException()
    {
        $dyacl = DyAclFactory::newDyAclPDO($this->pdo, __DIR__."/../wrong-configs/dbConfig-wrong-field-name-to-test2.xml");
        $dyacl->prepareAcl(1);
    }

    /**
     * @expectedException \DyAcl\DyAclException
     * @expectedExceptionMessage Config file is not accessible
     */
    public function testConfigFileNotAccessibleException()
    {
        $dyacl = DyAclFactory::newDyAclPDO($this->pdo, "/path/to/not-exist-config.xml");
        $dyacl->prepareAcl(1);
    }

    /**
     * test Exception on other dom load problems
     *
     * @expectedException \DyAcl\DyAclException
     */
    public function testConfigFileCanNotBeLoadedException()
    {
        $dyacl = DyAclFactory::newDyAclPDO($this->pdo, __DIR__."/../wrong-configs/dbConfig-wrong-field-name-to-test3.xml");
        $dyacl->prepareAcl(1);
    }

    /**
     * test Exception on other dom load problems
     *
     * @expectedException \DyAcl\DyAclException
     * @expectedExceptionMessage  usersRolesTblName was not set in config file.
     */
    public function testConfigKeyNotProvidedException()
    {
        $dyacl = DyAclFactory::newDyAclPDO($this->pdo, __DIR__."/../wrong-configs/dbConfig-wrong-field-name-to-test4.xml");
        $dyacl->prepareAcl(1);
    }

    /**
     * test Exception on other dom load problems
     *
     * @expectedException \DyAcl\DyAclException
     */
    public function testConfigOtherPossiblePorblemsException()
    {
        $dyacl = DyAclFactory::newDyAclPDO($this->pdo, __DIR__."/../wrong-configs/dbConfig-wrong-field-name-to-test5.xml");
        $dyacl->prepareAcl(1);
    }
}
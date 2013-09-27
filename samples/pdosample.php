<?php
/**
 * Attention!
 *
 * Before running this sample file you need to execute "DyACL.sql" and "sample.sql"
 * on your sample database.
 *
 * Remeber that you can execute this sql files on a database that you want to run
 * your tests on or you can uncomment 2 lines in 'DyACL.sql' that create a new
 * database named 'DyACL' and then execute 'sample.sql' which imports some sample
 * data for our test.
 *
 * You should change database connection information according to your own database connection
 * info
 *
 * Before running the test i want to explain the sample data - which are provided to you
 * in 'sample.sql'.
 *
 * There are 2 different users in 'users' table:
 * user by id 1
 * admin by id 2
 *
 * There are 2 different roles in 'roles' table:
 * admin by id 1
 * user by id 2
 *
 * There are 3 different records in 'users_roles' table:
 * row 1: user_id:1 role_id:2
 * Means: user named 'user' is a member of role 'user'
 *
 * row 2: user_id:2 role_id:1
 * Means: user named 'admin' is a member of role 'user'
 *
 * row 3: user_id:2 role_id:2
 * Means: user named 'admin' is a member of role 'admin'
 *
 * Finally, to put it simple, users can access whatever is defined for role 'user' but
 * members of role 'admin' has access to resources specific to admins and also
 * resources that users have access to.
 *
 * There are 4 different resources in 'resources' table. I named these resources according
 * to access rules so it makes understanding of our rules easier:
 *
 * public
 * secret
 * user_can_only_read
 * user_can_not_delete
 *
 * There are 6 different rules in table 'roles_resources'. I'll explain them row by row:
 * Remember, anything which we want to cotrol access to is called 'resource' for example
 * a controller, a method in a controller, a directory, a file , etc
 *
 * row 1: id:1 role_id:2 reource_name:public action:all status:allow
 * It means: 'User' role can do any available action on the resource 'public'
 *
 * row 2: id:2 role_id:2 reource_name:secret action:all status:deny
 * It means: 'User' role is not allowed to access resource 'secret'
 *
 * row 3: id:3 role_id:2 reource_name:user_can_only_read action:read status:allow
 * It means: 'User' role can just read(view) the resource 'user_can_only_read'
 *
 * row 4: id:4 role_id:2 reource_name:user_can_not_delete action:all status:allow
 * row 5: id:5 role_id:2 reource_name:user_can_not_delete action:delete status:deny
 * These two mean: 'User' role can do any action on the resource 'user_can_not_delete'
 * except deleting it or whatever the action 'delete' may mean - i mean soft delete!
 *
 * row 6: id:6 role_id:1 reource_name:secret action:all status:allow
 * It means: 'admin' role can do any available action on the resource 'secret'
 */
namespace DyAcl;

require_once '../vendor/autoload.php'; //this is for composer autoload

$sampleHost = "localhost";
$sampleDbName = "DyACL";
$sampleUsername = "testUsername";
$samplePassword = "testPassword";

$dyAcl = new DyAclPDO("mysql:host={$sampleHost};dbname={$sampleDbName};", $sampleUsername, $samplePassword);

/**
 * First of all test User.
 * User is just a member of role 'User'
 */

echo "Checking ACL for user 'user':<br>";
$sampleUserId = 1;
$dyAcl->prepareAcl($sampleUserId);

echo "His roles are:<br>";
echo '<pre>';print_r($dyAcl->getRoles());echo '</pre>';

echo '<br><hr><br>';

echo "Associated Rules are:<br>";
echo '<pre>';print_r($dyAcl->getRules());echo '</pre>';

echo '<br><hr><br>';

echo "Checking 'secret':<br>";
if ($dyAcl->isAllowed('secret')) {
    echo "Yes, 'all' action is allowed.<br>";
} else {
    echo "No, 'all' action is not allowed.<br>";
}

echo '<br><hr><br>';

echo "Checking 'public':<br>";
if ($dyAcl->isAllowed('public')) {
    echo "Yes, 'all' action is allowed.<br>";
} else {
    echo "No, 'all' action is not allowed.<br>";
}

echo '<br><hr><br>';

echo "Checking 'user_can_only_read':<br>";

if ($dyAcl->isAllowed('user_can_only_read')) {
    echo "Yes, 'all' action is allowed.<br>";
} else {
    echo "No, 'all' action is not allowed.<br>";
}

if ($dyAcl->isAllowed('user_can_only_read', DyAclPDO::ACTION_READ)) {
    echo "Yes, read is allowed.<br>";
} else {
    echo "No, read is not allowed.<br>";
}

echo '<br><hr><br>';

echo "Checking 'user_can_not_delete':<br>";

if ($dyAcl->isAllowed('user_can_not_delete')) {
    echo "Yes, 'all' action is allowed.<br>";
} else {
    echo "No, 'all' action is not allowed.<br>";
}

if ($dyAcl->isAllowed('user_can_not_delete', DyAclPDO::ACTION_READ)) {
    echo "Yes, read is allowed.<br>";
} else {
    echo "No, read is not allowed.<br>";
}

if ($dyAcl->isAllowed('user_can_not_delete', DyAclPDO::ACTION_DELETE)) {
    echo "Yes, delete is allowed.<br>";
} else {
    echo "No, delete is not allowed.<br>";
}

echo '<br><hr><br>';


echo "Checking ACL for user 'admin':<br>";
$sampleUserId = 2;
$dyAcl->prepareAcl($sampleUserId);

echo "His roles are:<br>";
echo '<pre>';print_r($dyAcl->getRoles());echo '</pre>';

echo '<br><hr><br>';

echo "Associated Rules are:<br>";
echo '<pre>';print_r($dyAcl->getRules());echo '</pre>';

echo '<br><hr><br>';

echo "Checking 'secret':<br>";
if ($dyAcl->isAllowed('secret')) {
    echo "Yes, 'all' action is allowed.<br>";
} else {
    echo "No, 'all' action is not allowed.<br>";
}

echo '<br><hr><br>';

echo "Checking 'public':<br>";
if ($dyAcl->isAllowed('public')) {
    echo "Yes, 'all' action is allowed.<br>";
} else {
    echo "No, 'all' action is not allowed.<br>";
}

echo '<br><hr><br>';

echo "Checking 'user_can_only_read':<br>";

if ($dyAcl->isAllowed('user_can_only_read')) {
    echo "Yes, 'all' action is allowed.<br>";
} else {
    echo "No, 'all' action is not allowed.<br>";
}

if ($dyAcl->isAllowed('user_can_only_read', DyAclPDO::ACTION_READ)) {
    echo "Yes, read is allowed.<br>";
} else {
    echo "No, read is not allowed.<br>";
}

echo '<br><hr><br>';

echo "Checking 'user_can_not_delete':<br>";

if ($dyAcl->isAllowed('user_can_not_delete')) {
    echo "Yes, 'all' action is allowed.<br>";
} else {
    echo "No, 'all' action is not allowed.<br>";
}

if ($dyAcl->isAllowed('user_can_not_delete', DyAclPDO::ACTION_READ)) {
    echo "Yes, read is allowed.<br>";
} else {
    echo "No, read is not allowed.<br>";
}

if ($dyAcl->isAllowed('user_can_not_delete', DyAclPDO::ACTION_DELETE)) {
    echo "Yes, delete is allowed.<br>";
} else {
    echo "No, delete is not allowed.<br>";
}

echo '<br><hr><br>';
<?php

namespace DyAcl;

use DyAcl\DyAclPDO;
use PDO;

require_once '../vendor/autoload.php'; //this is for composer autoload

$sampleHost = "localhost";
$sampleDbName = "DyACL";
$sampleUsername = "testUsername";
$samplePassword = "testPassword";

$pdo = new PDO("mysql:host={$sampleHost};dbname={$sampleDbName};", $sampleUsername, $samplePassword);

$dyAcl = new DyAclPDO($pdo, realpath("../config/dbConfig.xml"));

$sampleUserId = 1;
$dyAcl->prepareAcl($sampleUserId);

echo "Checking ACL for user 'user':<br>";


echo "His roles are:<br>";
echo '<pre>';
print_r($dyAcl->getRoles());
echo '</pre>';

echo '<br><hr><br>';

echo "Associated Rules are:<br>";
echo '<pre>';
print_r($dyAcl->getRules());
echo '</pre>';

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
echo '<pre>';
print_r($dyAcl->getRoles());
echo '</pre>';

echo '<br><hr><br>';

echo "Associated Rules are:<br>";
echo '<pre>';
print_r($dyAcl->getRules());
echo '</pre>';

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
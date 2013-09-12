# DyACL
=====

This is a really simple implementation of ACL.

### DEFINITIONS:

**Visitor**: Somebody who visits a page in your website.

**User**: A visitor who is a member of your website.

**Role**: A role of a visitor in your website. Roles are known as groups of users.

**Resource**: What ever stuff in your website that a permission can be applied to.
It can be a controller, method in your controller, special function, a file, a directory,
and etc.

**Action**: An action is the way a visitor accesses a part of your site it can include Create,Read (View), Update , Delete or anything else.

**Rule**: A rule about who can access what and how.

I've put a sample sql file named "DyACL.sql" in this repository to show you how to arrange
these thing in your database.

**Remember** that this library does need just an array of anything so you're not forced to use
database. You can put your roles, resources, etc in a file or an array and give that to the
library. Rules also can be loaded from database or even created on fly.

### HOW IT WORKS:

1. Library should be loaded whether by including it into the file or with help from
composer.
2. Find out who is visiting your website. Each and every user even a guest should have an
ID that will help you differenciate one from another.
3. Every user has at least one role and as many roles you desire is possible. Roles are
different according to each site. It can be as simple as "Administrator, manager, user" or
it can be as complex as roles are defined by administrator on fly. But it makes no
difference because you store these in "roles" table and when you find out what is your
users ID it's as easy as a simple database query. Selected the roles from database define
theme for library by "setRoles" function:

```
$roles = array(); // here assign what you selected from "roles" table

$dyacl = new DyACL();
$dyacl->setRoles($roles);
```

if there is an special role that you desire to be added on fly add it by "setRole" function.
For example you have a role named "admin" and you want to assign it to this user:

```
$dyacl->setRole("admin");
```

4. When you have a list of current user's roles you can select these roles' "Rules" from
it's table. After that define these rules for the library by "setRules" function.

```
$rules = array();// here assign the rules you selected from database.

$dyacl->setRules($rules);
```

The library will define every resource that is mentioned by rules for itself so you do
not need to define any resources. Access to a resource that is unknown for the library
would be denied by default. In case you need to define any rule on fly go on and use
"setRule" function. Each "rule" consists of a "resource" name, a "privilege", for which
the possible values are 'allow' and 'deny', and finally an "action" which is by default
"all" which means user is allowed to do whatever is possible or you can assign an
action such as "view",
For example you want to deny any access to a folder named "secrets":

```
$dyacl->setRule("secrets", DyACL::DENY);
```

or you want to allow all access but deleting:

```
$dyacl->setRule("secrets", DyACL::ALLOW);
$dyacl->setRule("secrets", DyACL::DENY, "delete");
```

or you want to deny all but viewing:

```
$dyacl->setRule("secrets", DyACL::DENY);
$dyacl->setRule("secrets", DyACL::ALLOW, "view");
```

Remeber that DyACL::DENY is equal to "deny" and DyACL::ALLOW is equal to "allow".

5. Finally checking whether user has access to a resource or not is possible by "isAllowed"
function:
For example after all this you want to check whether allow access to the folder "secrets"
or not:

```
if ($dyacl->isAllowed("secrets") {
    echo "Yes, you are allowed";
}
else {
    echo "Access Denied!";
}
```

OR maybe you want to check whether the user is allowed to delete the folder "secrets":

```
if ($dyacl->isAllowed("secrets", "delete") {
    echo "Yes, you are allowed";
}
else {
    echo "Access Denied!";
}
```

### A COMPLETE SAMPLE:

    coming soon...

### FAQ:

-Q: What happens if a "role" denies access to a resource but another one allows it?

-A: The result will be "allow".


-Q: Do I need to define each and every resource?!

-A: NO. There may be lots of resources defined in your database. Loading all that can
use lots of memory or may be impossible for large projects. You just load specific rules
that are related to current user and the library will handle all the rest.
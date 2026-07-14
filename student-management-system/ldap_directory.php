<?php
/*
 * Week 13 – Simulated LDAP Directory
 * A real deployment would query OpenLDAP through PHP's ldap_bind().
 * Here we SIMULATE the directory as a hierarchical structure that is
 * completely SEPARATE from the application database — the key LDAP
 * idea: authentication data lives in a central directory service,
 * not inside each application's own tables.
 *
 * Directory tree simulated:
 *   dc=university,dc=ac,dc=ke
 *     ou=staff    -> administrators
 *     ou=students -> students
 */

$LDAP_DIRECTORY = [
    [
        "dn"       => "uid=admin1,ou=staff,dc=university,dc=ac,dc=ke",
        "uid"      => "admin1",
        "cn"       => "System Administrator",
        "mail"     => "admin1@university.ac.ke",
        // password: Admin@2026 (stored hashed, as a real directory would)
        "userPassword" => '$2b$10$0h.qHswZpJk432F7r4I1ge136wx2jaV/qkaOzdyEFxq4uMIBeSiN6',
        "role"     => "admin",
    ],
    [
        "dn"       => "uid=stud1,ou=students,dc=university,dc=ac,dc=ke",
        "uid"      => "stud1",
        "cn"       => "Test Student",
        "mail"     => "stud1@students.university.ac.ke",
        // password: Student@2026
        "userPassword" => '$2b$10$fgyHVPayxRADaeOSOmK/yOQhB88c345qUafsXhHCnWSo2fuspPmIO',
        "role"     => "student",
    ],
];

/**
 * Simulates ldap_bind(): searches the directory for the uid and
 * verifies the password. Returns the entry on success, false otherwise.
 */
function ldap_simulated_bind($uid, $password) {
    global $LDAP_DIRECTORY;
    foreach ($LDAP_DIRECTORY as $entry) {
        if ($entry["uid"] === $uid && password_verify($password, $entry["userPassword"])) {
            return $entry;
        }
    }
    return false;
}
?>

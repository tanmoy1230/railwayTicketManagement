<?php

$db_user = "root";
$db_pass = "";
$db_name = "railway ticket management";

$db = new mysqli("localhost", $db_user, $db_pass, $db_name);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>

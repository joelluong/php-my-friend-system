<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoelLuong
 * Date: 7/10/2018
 * Time: 7:05 PM
 */
session_start(); // start the session
$_SESSION = array(); // unset all session variables
session_destroy(); // destroy all data associated with the session
header("location: index.php"); // redirect to number.php
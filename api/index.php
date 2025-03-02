<?php
require_once "core\Router.php";
require_once "config\Database.php";


$router = new Router();
$router->handleRequest();
?>
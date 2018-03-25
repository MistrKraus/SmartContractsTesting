<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 05.11.2017
 * Time: 15:49
 */

$GLOBALS['isLocal'] = ($_SERVER["HTTP_HOST"] == "localhost");
//echo $isLocal;
//exit();
session_start();

mb_internal_encoding("UTF-8");

//spl_autoload_extensions(".php");
//spl_autoload_register();

function autoloadFunkce($class) {
    // Končí název třídy řetězcem "Controller" ?
    if (preg_match('/Controller$/', $class))
        require("controller/" . $class . ".php");
    else
        require("model/" . $class . ".php");
}

spl_autoload_register("autoloadFunkce");

//$_SESSION['zprava'] = "Ahoj";

Db::connect("localhost", "root", "", "voc_practise");     // VOCABULARY

$redirect = new RedirectController();
$redirect->process(array($_SERVER['REQUEST_URI']));

$redirect->buildView();
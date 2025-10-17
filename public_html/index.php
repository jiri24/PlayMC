<?php

session_start();

require_once '../system/System.php';

ini_set('date.timezone', 'Europe/Berlin');
$system = new System();

//** un-comment the following line to show the debug console
//$system->getTemplate()->debugging = true;
// Zavolání kontroleru
$calledController = $system->getURL()->getArg(0);
$finded = false;
$controllers = $system->getDB()->query("SELECT * FROM controller");
foreach ($controllers as $controller) {
    if (mb_strtolower($calledController) == mb_strtolower($controller['name'])) {
        $calledController = $controller['name'];
        $finded = true;
        break;
    }
}
if (!$finded) {
    $calledController = "Info";
}
require_once '../controllers/' . $calledController . '.php';
$controller = new $calledController($system);

// Nastavení přihlášení
$system->checkLogin();
// Vytiskni stránku
$system->getTemplate()->printOutput();

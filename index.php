<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'vendor/autoload.php';

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('CLASSDIR', ROOT . DS . 'app');
define('CONTROLLERSDIR', CLASSDIR . DS . 'Controller');
define('MODELSDIR', CLASSDIR . DS . 'Model');
define('VIEWSDIR', CLASSDIR . DS . 'Views');
define('TEMPLATESDIR', VIEWSDIR . DS . 'templates');

require CLASSDIR . DS . 'Router.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$r = new Router();
$r->manageRequest();

?>

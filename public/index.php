<?php
header("Access-Control-Allow-Origin: http://localhost:3000");

define('HTTP_METHOD',$_SERVER['REQUEST_METHOD']);
DEFINE('BASE_PATH',realpath(__DIR__.'/../'));
DEFINE('DS', DIRECTORY_SEPARATOR);

/*
|--------------------------------------------------------------------------
| CORS WHITELIST
|--------------------------------------------------------------------------
| RETURN HEADER FOR ALLOWED ORIGINS.
| FONT FORGET FALSE AS 2nd ARG
*/
if (isset($_SERVER['HTTP_ORIGIN'])) header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers:  X-Authorization, authorization, version, {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");


/*
|--------------------------------------------------------------------------
| Suppress PREFLIGHT REQUESTS
|--------------------------------------------------------------------------
| OPTION REQUESTS ARE SENT BY BROWSER TO PING YOUR WEBSITE.
| SO WE DONT NEED TO ACTUALLY PROCESS THIS REQUEST.
| JUST RETURN AN EMPTY 200 SUCCESS HEADER IS ENOUGH
*/
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
    http_response_code(200);
    die();
}

require __DIR__ . '/../vendor/autoload.php';




// define( 'REDBEAN_MODEL_PREFIX', "SimpleIn\\Models\\");
class_alias('\RedBeanPHP\R','\R');
// use RedBeanPHP\Facade as R;
R::setup('sqlite://'.BASE_PATH.'production.sqlite');

use Luracast\Restler\Restler;
use Luracast\Restler\Defaults;
use Luracast\Restler\Format\JsonFormat;


require __DIR__ . '/../src/User.php';
Defaults::$userIdentifierClass = 'Simplein\User';
Defaults::$accessControlFunction = 'AccessControl::verifyAccess';

Defaults::$apiVendor = "Simplein";
Defaults::$useUrlBasedVersioning = true;
JsonFormat::$numbersAsNumbers = true;

$r = new Restler();

$r->addAuthenticationClass('AccessControl');
$r->addAPIClass('Home','/home');
$r->setSupportedFormats('JsonFormat', 'XmlFormat');
$r->setSupportedFormats('YamlFormat', 'YamlFormat');
$r->setSupportedFormats('CsvFormat', 'CsvFormat');
$r->setSupportedFormats('JsonFormat', 'XmlFormat');
//$r->addAuthenticationClass('Auth');
$r->addAPIClass('Explorer');

$files = glob(BASE_PATH.'/src/endpoints/*.php', GLOB_BRACE);
foreach($files as $file) {
  $name = explode('/', $file);
  $name = array_pop($name);
  $name = explode('.php', $name);
  $name = array_shift($name);
  $r->addAPIClass(ucfirst($name),'/'.strtolower($name));
}

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application set up, we can simply let it handle the
| request and response
|
*/
Restler::addListener('onRespond', function () {
    header('X-Powered-By: Simpleinformatics');
});

$r->handle();

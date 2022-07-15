<?php
define("BASEDIR", dirname(__DIR__));

require_once BASEDIR . "/vendor/autoload.php";

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->setBasePath("/api/v1");

$app->addBodyParsingMiddleware();

$app->post("/compile", [ \PhpDocGen\Http\Controllers\ApiController::class, "compile"]);

$app->run();
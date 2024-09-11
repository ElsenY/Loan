<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->add(function (Request $request, RequestHandler $handler) { 
    $response = $handler->handle($request);
    return $response->withHeader('Content-Type','application/json');
});
$app->addErrorMiddleware(true,true,true)->getDefaultErrorHandler()->forceContentType('application/json');


$routes = require __DIR__ . '/../src/App/routes.php';
$routes($app);


$app->run();
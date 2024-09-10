<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\LoanController;
use App\Middleware\LoanMiddleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->add(function (Request $request, RequestHandler $handler) { 
    $response = $handler->handle($request);
    return $response->withHeader('Content-Type','application/json');
});
$app->addErrorMiddleware(true,true,true)->getDefaultErrorHandler()->forceContentType('application/json');

$app->post('/api/loan',LoanController::class . ':insert')->add(LoanMiddleware::class . ':validateInsert');

$app->run();
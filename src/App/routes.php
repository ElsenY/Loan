<?php

use App\Controllers\LoanController;
use Slim\App;
use App\Middleware\LoanMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get("/", function (Request $request, Response $response): Response { 
        $response->getBody()->write("hello world");   
        return $response;
    });

    $app->post('/api/loan',LoanController::class . ':insert')->add(LoanMiddleware::class . ':validateInsert');
};
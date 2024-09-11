<?php

use App\Controllers\LoanController;
use Slim\App;
use App\Middleware\LoanMiddleware;

return function (App $app) {
    $app->post('/api/loan',LoanController::class . ':insert')->add(LoanMiddleware::class . ':validateInsert');
};
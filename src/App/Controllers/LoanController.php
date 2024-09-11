<?php

namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoanController {

    public function __construct(){
      
    }

    public function insert(Request $request, Response $response): Response{
        $body = $request->getParsedBody();

        $data = 'name: '.$body['name'] . PHP_EOL
        . 'loan_amount:' .$body['loan_amount'] . PHP_EOL
        . 'loan_purpose: ' . $body['loan_purpose'] . PHP_EOL
        . 'date_of_birth: ' . $body['date_of_birth'] . PHP_EOL
        . 'sex: ' . $body['sex'] . PHP_EOL
        . 'ktp_number: ' . $body['ktp_number'] . PHP_EOL
        . 'loan_period: ' . $body['loan_period'] . PHP_EOL;
        
        $file_loc = $_ENV["DATA_STORE_NAME"] ?? 'loan-datass.txt';
        $inserted = file_put_contents($file_loc,$data);

        if ($inserted){ 
            $response->withStatus(200);
            $respBody = json_encode([
                'message' => "successfully insert the data!",
            ]);
            $response->getBody()->write($respBody);
        } else {
            $response->withStatus(500);
            $respBody = json_encode([
                'message' => "there's a problem when inserting the data!",
            ]);
            $response->getBody()->write($respBody);
        }

        return $response;
    }
}
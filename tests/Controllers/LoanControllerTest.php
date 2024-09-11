<?php

namespace Tests\Controllers;

use App\Controllers\LoanController;
use PHPUnit\Framework\TestCase;
require_once __DIR__ ."/../../src/App/Controllers/LoanController.php";
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class LoanControllerTest extends TestCase {
    private $filename = 'loan-data-test.txt';
    public function testInsertLoan() {
        $loanController = new LoanController();

        $payload = [
            "name" =>  "Add your name in the body",
            "loan_amount" => 10000,
            "loan_purpose" => "vacation",
            "date_of_birth" => 301099,
            "sex" => "male",
            "ktp_number" =>  1231233010992312,
            "loan_period" => 100
        ];

        $_ENV["DATA_STORE_NAME"] = $this->filename;
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', '/api/loan')
                                ->withParsedBody($payload)
                                ->withHeader('Content-Type', 'application/json');
        
        $response = new Response();
        $returnResp = $loanController->insert($request,$response);

        $this->assertFileExists($this->filename);
        $this->assertEquals(200,$returnResp->getStatusCode());
    }

    protected function setUp(): void
    {
        // Ensure the file does not exist before the test
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
    
    protected function tearDown(): void
    {
        // Clean up the file after the test
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
}
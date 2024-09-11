<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
require_once __DIR__ ."/../../src/App/Controllers/LoanController.php";
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use App\Middleware\LoanMiddleware;
use ReflectionClass;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\Attributes\DataProvider;


class LoanMiddlewareTest extends TestCase {

    #[DataProvider('requestDataProvider')]
    public function testMiddlewareValidateInsert($payload,$expectedDataType,$expectedLength) {

        $loanMiddleware = new LoanMiddleware();
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', '/api/loan')
                                ->withParsedBody($payload)
                                ->withHeader('Content-Type', 'application/json');
                                
        $handlerMock = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface{
                $response = new Response(200);
                $payload = json_encode([
                    'error' => 'all success',
                    'message' => 'test-message',
                ]);
                $response->getBody()->write($payload);
                return $response;
            }
        };

        $returnResp = $loanMiddleware->validateInsert($request,$handlerMock);

        // assert type is response
        $this->assertInstanceOf($expectedDataType,$returnResp);

        // asserting if there's any error field or success
        if ($returnResp instanceof Response) {
            $message = (string)$returnResp->getBody();
            $data = json_decode($message,true);

            $this->assertEquals($expectedLength,$data['error']);
        }
    }

    public static function requestDataProvider()
    {
        $payload = [
            'name' =>  'Add your name in the body',
            'loan_amount' => 10000,
            'loan_purpose' => 'vacation',
            'date_of_birth' => 301099,
            'sex' => 'male',
            'ktp_number' =>  1231233010992312,
            'loan_period' => 100
        ];

        return [
            [$payload, Response::class, 'all success'],
            [[...$payload,'loan_amount'=>100000], Response::class, 'Invalid Field Format'],
        ];
    }

    public function testMiddleWareConstructor() {
        $loanMiddleware = new LoanMiddleware();

        $this->assertNotEmpty($this->getProperty($loanMiddleware,'nameValidator'));
        $this->assertNotEmpty($this->getProperty($loanMiddleware,'loanAmountValidator'));
        $this->assertNotEmpty($this->getProperty($loanMiddleware,'loanPeriodValidator'));
        $this->assertNotEmpty($this->getProperty($loanMiddleware,'loanPurposeValidator'));
        $this->assertNotEmpty($this->getProperty($loanMiddleware,'dateOfBirthValidator'));
        $this->assertNotEmpty($this->getProperty($loanMiddleware,'sexValidator'));
    }

    public function getProperty($object, $property)
    {
        $reflectedClass = new ReflectionClass($object);
        $reflection = $reflectedClass->getProperty($property);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }
}
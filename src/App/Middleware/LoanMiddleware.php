<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Respect\Validation\Validator as v;

class LoanMiddleware{

    private $nameValidator;
    private $ktpValidator;
    private $loanAmountValidator;
    private $loanPeriodValidator;
    private $loanPurposeValidator;
    private $dateOfBirthValidator;
    private $sexValidator;
    public function __construct() {
        $this->nameValidator = v::stringType()->notEmpty()->contains(' ')->callback(function($val) {
            return count(explode(' ', trim($val))) >= 2;
        });
        $this->loanAmountValidator = v::number()->notEmpty()->between(1000,10000);
        $this->loanPurposeValidator = v::stringType()->notEmpty()
        ->containsAny(['vacation','renovation', 'electronics', 'wedding', 'rent', 'car', 'investment']);
        $this->dateOfBirthValidator = v::date('dmy')->notEmpty();
        $this->sexValidator = v::stringType()->notEmpty()->in(['male','female']);
        $this->loanPeriodValidator = v::number()->notEmpty();
    }

    public function validateInsert(Request $request, RequestHandler $handler): Response {
        $errormessage = '';

        $body = $request->getParsedBody();
        $valid = $this->nameValidator->validate($body['name'] ?? null);
        $validField = true;

        if (!$valid) {
            $validField = false;
            $errormessage = 'invalid name format! please input the name with at least 2 names (first name & last name) separated by space' . PHP_EOL;
        }

        $valid = $this->loanAmountValidator->validate($body['loan_amount'] ?? null);

        if (!$valid) {
            $validField = false;
            $errormessage .= 'invalid loan_amount format! loan_amount must be between 1000 and 10000';
        }

        $valid = $this->loanPurposeValidator->validate($body['loan_purpose'] ?? null);

        if (!$valid) {
            $validField = false;
            $errormessage .= 'invalid loan_purpose format! loan_purpose should contains at least one of this word [vacation, renovation, electronics, wedding, rent, car, investment]' . PHP_EOL;
        }

        $dob = $body['date_of_birth'] ?? null;
        $valid = $this->dateOfBirthValidator->validate($dob);

        if (!$valid) {
            $validField = false;
            $errormessage .= 'invalid date_of_birth format! date_of_birth must be a number and follow ddmmyy format' . PHP_EOL;
        }

        $sex = $body['sex'] ?? null;
        $valid = $this->sexValidator->validate($sex);

        if (!$valid) {
            $validField = false;
            $errormessage .= 'invalid sex format! sex must be male or female' . PHP_EOL;
        }

        if (strcasecmp("sex","male")) {
            $this->ktpValidator = v::regex("/^\d{6}$dob\d{4}$/");
        } else {
            $date = substr($dob,0,2)+40;
            $newDob = $date + substr($dob,2,4);
            $this->ktpValidator = v::regex("/^\d{6}$newDob\d{4}$/");
        }

        $valid = $this->ktpValidator->validate($body["ktp_number"] ?? null);

        if (!$valid) {
            $validField = false;
            $errormessage .= 'invalid ktp_number format! ktp_number must be a number and follow XXXXXXDDMMYYXXXX format' . PHP_EOL;
        }

        $valid = $this->loanPeriodValidator->validate($body['loan_period'] ?? null);

        if (!$valid) {
            $validField = false;
            $errormessage .= 'invalid loan_period format! loan period must be a number' . PHP_EOL;
        }

        $response = $handler->handle($request);
        if (!$validField) {
            $response = $response->withStatus(400);

            $payload = json_encode([
                'error' => 'Invalid Field Format',
                'message' => $errormessage,
            ]);

            $response->getBody()->write($payload);

            return $response;
        }

        return $handler->handle($request);
    }
}
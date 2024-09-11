# Loan API

Below describe how to run the application and the unit test

# Prerequisites

1. PHP installed
2. composer installed

# How to serve the app

1. make sure you are in the project directory, then run `composer install`
2. go to 'public' folder and run 'php -S localhost:[PORT] eg : `php -S localhost:8888`
3. the application is served locally

additionally can try to run `curl localhost:[PORT]` in your terminal, if you get "hello world" as the response means the app is running properly

# How to use the api

1. the loan api route is at /api/loan, and should be called with POST method
2. the body need these attributes
   - name
   - loan_amount
   - loan_purpose
   - date_of_birth
   - sex
   - ktp_number
   - loan_period
3. upon successful call, there will be a text file created in public folder with the name 'loan-data.txt' containing the data

# How to run the unit test

1. run `composer install` if you haven't
2. make sure you are in the same directory as `tests` folder
3. run `vendor/bin/phpunit tests/` to run the unit test

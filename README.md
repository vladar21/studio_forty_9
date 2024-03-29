# studio_forty_9

Studio Forty 9's test task is a Symfony bundle designed to efficiently manage product inventory. It provides functionality for importing stock data from CSV files, storing it in a MySQL database, and presenting it through a user-friendly interface. It also includes functionality to notify users via email when a product is out of stock.

## Assignments

1. **Symfony Bundle Creation**: Create a Symfony bundle to handle product inventory. It should be installable in a vanilla Symfony app using MySQL as the database.

2. **CSV Data Import**: Create a command to read stock data from the provided CSV file. The directory location should be configurable and should be relative to the web root.

3. **Doctrine Entity**: Create a doctrine entity for the stock data.

4. **Database Interaction**: Save the stock data into the database - perform an update when the stock and location values already exist.

5. **Controller Action**: Create a controller action to present the stock data (styling not important).

6. **Data Saving**: Create a controller action to accept posted stock data and save it into the database.

7. **Out of Stock Detection**: When processing stock data, determine when a stock item is going out of stock (so value changing from a positive value to 0).

8. **Message Triggering**: Trigger a message using the messenger component when an item goes out of stock.

9. **Email Notification**: Create a message handler to send an email notification to a configurable email address. The email should contain text describing that the SKU is out of stock at a particular location.

10. **Testing**: Create unit/functional tests where appropriate.

## Installation

To install the bundle, clone this repository to your local machine:

```bash
git clone https://github.com/vladar21/studio_forty_9.git
```

## Usage

1. Install dependencies:
```bash
composer install
```

2. Run database migrations:
```bash
php bin/console doctrine:migrations:migrate
```

3. Use the provided console command to load stock data from a CSV file:
```bash
php bin/console app:load-stock-data public/master20240306015003.csv
```

4. Access the stock data via the provided controller actions.

## Testing

Unit and functional tests have been provided to ensure the reliability and correctness of the bundle's functionality. 
To run the tests, execute the following command:
```bash
./bin/phpunit
```

The tests cover various aspects of the bundle, including:

``StockController``: Tests for controller actions responsible for presenting stock data and accepting posted stock data.

``StockService``: Tests for the service responsible for handling stock data changes, including saving data into the database and triggering messages.

``StockOutMessageHandler``: Tests for the message handler responsible for sending email notifications when an item goes out of stock.

``LoadStockDataCommand``: Tests for the console command responsible for loading stock data from a CSV file.

The tests ensure that all features of the bundle work as expected and help maintain the quality of the codebase.

## License

This project is licensed under the MIT License.
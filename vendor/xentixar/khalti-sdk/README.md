# KhaltiSdk PHP Library

The `KhaltiSdk` is a PHP library for integrating the Khalti payment gateway into your application. This library simplifies the process of initializing payments, validating transactions, and more.

## Installation

To use this library, you'll need to include it in your project. You can install it via Composer. If you don't have Composer installed, you can download it from [getcomposer.org](https://getcomposer.org/).

### Step-by-step Installation

1. **Install Composer**:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   mv composer.phar /usr/local/bin/composer
   ```

2. **Add the package to your project**:
   ```bash
   composer require xentixar/khalti-sdk
   ```

3. **Include Composer's autoloader** in your script:
   ```php
   require_once __DIR__ . '/vendor/autoload.php';
   ```

## Usage

### Setting Up the Khalti Instance

Create a new instance of the `Khalti` class and set your secret key using the `setSecretKey` method. The secret key is required for authorization when making API requests.

### Example Code

```php
<?php

use Xentixar\KhaltiSdk\Khalti;

require_once __DIR__ . "/vendor/autoload.php";

$khalti = new Khalti();

// Set your Khalti secret key
$khalti->setSecretKey('test_secret_key_1392a63451d740c59806685acd57730a');

// Configure the payment details
$khalti->config(
    'http://localhost:8000/decode.php',  // Return URL
    'http://localhost:8000',            // Website URL
    10,                                 // Amount in rupees
    '124',                              // Purchase Order ID
    'test',                             // Purchase Order Name
    [                                   // Customer Information
        "name" => "Khalti Bahadur",
        "email" => "example@gmail.com",
        "phone" => "9800000000"
    ]
);

// Initiate the payment
$khalti->init();
?>
```

### Configuration Details

The `config` method allows you to specify several parameters:

- **`$return_url`**: The URL where the user will be redirected after payment.
- **`$website_url`**: Your website's URL.
- **`$amount`**: The payment amount in rupees.
- **`$purchase_order_id`**: A unique identifier for the purchase order.
- **`$purchase_order_name`**: The name of the purchase order.
- **`$customer_info`**: An optional array containing customer details (`name`, `email`, `phone`).
- **`$amount_breakdown`**: Optional breakdown of the amount (e.g., tax, shipping).
- **`$product_details`**: Optional product details.

### Initiating a Payment

The `init` method sends a request to the Khalti API to initiate the payment process. It handles the request server-side, and if the response contains a `payment_url`, it redirects the user to the Khalti payment page.

**Note**: For production, set the `$production` flag to `true` in the `init` method.

```php
$khalti->init(true); // Set to true for production
```

## Validating a Transaction

To validate a transaction, use the `validate` method. This method sends a request to the Khalti API to check the status of a transaction.

```php
$response = $khalti->validate('transaction_code_here');

if ($response) {
    // Handle the response
    echo $response;
} else {
    echo "Error: Invalid response from Khalti API.";
}
```

**Note**: For production, set the `$production` flag to `true` in the `validate` method.

```php
$response = $khalti->validate('transaction_code_here', true); // Set to true for production
```

### Decoding the Response

To decode the response data from Khalti:

```php
$data = $khalti->decode();
if ($data) {
    // Process the data
    print_r($data);
} else {
    echo "Error: No data to decode.";
}
```

## Error Handling

The `init` method includes basic error handling for cURL requests. You may want to add additional error handling based on your application's requirements.

## Contribution

Contributions are welcome! Please submit a pull request or create an issue to report bugs or request features.

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Additional Resources

- [Khalti API Documentation](https://docs.khalti.com/)
- [Official Khalti Website](https://khalti.com/)
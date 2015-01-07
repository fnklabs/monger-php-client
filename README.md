# Monger php client

Read more about project at [Monger](https://monger.fnklabs.com)

##Create Client instance
```php
$logger       = new Logger("test");
$curlAdapter  = new CurlAdapter($logger),
$mongerClient = new MongerClient(
                                "https://monger.fnklabs.com", 
                                "USER_TOKEN", 
                                "USER_ACCESS_TOKEN_WITH_WRITE_SCOPE", 
                                "YOUR_APPLICATION_NAME",
                                "YOUR_APPLICATION_VERSION",
                                $curlAdapter,
                                $logger
               );
```

##Register new customer 
```php
$mongerClient->newCustomer(
            "CUSTOMER_INTERNAL_ID",
            "CUSTOMER_INITIALS",
            "CUSTOMER_EMAIL",
            "CUSTOMER_PHONE_NUMBER",
            CUSTOMER_GENDER,
            "CUSTOMER_COUNTRY",
            "CUSTOMER_CITY",
            "CUSTOMER_AGE",
            new \DateTime(),
            ["TAG_1", "TAG_2"]
        );
```

##Register customer's new  activity
```php
$mongerClient->newActivity("CUSTOMER_INTERNAL_ID", "ACTIVITY_NAME", new \DateTime());

```

##Register customer's new payment
```php
$mongerClient->newPayment("CUSTOMER_INTERNAL_ID", "PAYMENT_INTERNAL_ID", PURCHASE_AMOUNT, new \DateTime());
```

##Additional info
* You can find example at MongerClientTest
* To retrieve access to Monger Service visit page [Monger](https://monger.fnklabs.com)


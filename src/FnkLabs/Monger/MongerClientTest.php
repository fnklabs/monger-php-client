<?php

namespace FnkLabs\Monger;

use Monolog\Logger;

class MongerClientTest extends \PHPUnit_Framework_TestCase
{

    const CUSTOMER_INTERNAL_ID = "1";
    const PAYMENT_INTERNAL_ID = "1";

    const PURCHASE_AMOUNT = 100;

    public function test()
    {
        $logger       = new Logger("test");
        $mongerClient = new MongerClient(
            "https://monger.fnklabs.com",
            "USER_TOKEN",
            "USER_ACCESS_TOKEN_WITH_WRITE_SCOPE",
            "YOUR_APPLICATION_NAME",
            "YOUR_APPLICATION_VERSION",
            new CurlAdapter($logger),
            $logger
        );

        $mongerClient->newActivity(self::CUSTOMER_INTERNAL_ID, "registration", new \DateTime());
        $mongerClient->newCustomer(
            self::CUSTOMER_INTERNAL_ID,
            "Test User",
            "test@example.com",
            "1234567890",
            true,
            "COUNTRY",
            "CITY",
            "22",
            new \DateTime(),
            ["Google", "BANNER_1"]
        );
        $mongerClient->newPayment(self::CUSTOMER_INTERNAL_ID, self::PAYMENT_INTERNAL_ID, self::PURCHASE_AMOUNT, new \DateTime());
    }
}

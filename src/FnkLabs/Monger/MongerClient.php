<?php

namespace FnkLabs\Monger;

use Monolog\Logger;

/**
 * Class MongerClient
 * @package FnkLabs\Monger
 */
class MongerClient
{
    private $serviceAddress;
    private $userToken;
    private $accessToken;
    private $httpClient;
    private $logger;
    private $applicationName;
    private $applicationVersion;

    /**
     *
     * @param string     $serviceAddress     Monger Service Http API address
     * @param string     $userToken          Monger API User token
     * @param string     $accessToken        Monger API Access token
     * @param string     $applicationName    Current application name
     * @param string     $applicationVersion Current application version
     * @param HttpClient $httpClient         Http client implementation
     * @param Logger     $logger             Logger
     */
    public function __construct($serviceAddress, $userToken, $accessToken, $applicationName, $applicationVersion, HttpClient $httpClient, Logger $logger)
    {
        $this->serviceAddress     = $serviceAddress;
        $this->userToken          = $userToken;
        $this->accessToken        = $accessToken;
        $this->applicationName    = $applicationName;
        $this->applicationVersion = $applicationVersion;
        $this->httpClient         = $httpClient;
        $this->logger             = $logger;
    }

    /**
     * Register customer's new activity
     *
     * @param String    $customer Internal customer id
     * @param String    $action   Action name
     * @param \DateTime $date     Action date
     */
    public function newActivity($customer, $action, \DateTime $date = null)
    {
        $parameters = [
            "clientId"           => $customer,
            "action"             => $action,
            "application"        => $this->getApplicationName(),
            "applicationVersion" => $this->getApplicationVersion(),
        ];

        if ($date != null) {
            $parameters["createdAt"] = $date->format("c");
        }

        $address = sprintf("%s/api/user_event/new", $this->getServiceAddress());

        $this->executeRequest($address, $parameters);
    }

    /**
     * Register new customer
     *
     * @param string    $customer  Internal customer ID
     * @param string    $initials  Customer initials
     * @param string    $email     Customer email
     * @param string    $phone     Customer phone number
     * @param boolean   $gender    Customer gender True - Male, False - Female
     * @param string    $country   Customer country
     * @param string    $city      Customer city
     * @param int       $age       Customer age
     * @param \DateTime $createdAt Customer registration date
     * @param array     $tags      Customer custom tags
     */
    public function newCustomer($customer, $initials, $email, $phone, $gender, $country, $city, $age, \DateTime $createdAt, array $tags = [])
    {
        $parameters = [
            "client"           => $customer,
            "initials"         => $initials,
            "email"            => $email,
            "phone"            => $phone,
            "gender"           => ($gender) ? "MALE" : "FEMALE",
            "country"          => $country,
            "city"             => $city,
            "age"              => $age,
            "lastActivity"     => $createdAt->format("c"),
            "createdAt"        => $createdAt->format("c"),
            "registrationDate" => $createdAt->format("c"),
            "tags"             => $tags
        ];

        $address = sprintf("%s/api/customer/new", $this->getServiceAddress());

        $this->executeRequest($address, $parameters);
    }

    /**
     * Register customer's new payment
     *
     * @param string    $customer      Internal customer ID
     * @param string    $paymentId     Internal payment ID
     * @param float     $paymentAmount Payment amount
     * @param \DateTime $createdAt     Payment date
     */
    public function newPayment($customer, $paymentId, $paymentAmount, \DateTime $createdAt = null)
    {
        $jsonParameters = [
            "client"    => $customer,
            "paymentId" => $paymentId,
            "amount"    => $paymentAmount,
        ];

        if ($createdAt != null) {
            $jsonParameters["createdAt"] = $createdAt->format("c");
        }

        $address = sprintf("%s/api/payments/new", $this->getServiceAddress());

        $this->executeRequest($address, $jsonParameters);
    }

    /**
     * Get random UUID
     *
     * @link http://wikipedia.org/wiki/UUID
     *
     * @return string
     */
    private function getUuid()
    {
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    /**
     * Retrieve Http client
     *
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Get default HTTP Headers
     *
     * @return array
     */
    protected function getDefaultHttpHeaders()
    {
        return [
            'Content-type' => "application/json",
            "Accept"       => "application/json"
        ];
    }

    /**
     * Execute Http request try several times if request can't be executed
     *
     * @param string $address    Http Address
     * @param array  $parameters Http POST parameters
     *
     */
    protected function executeRequest($address, $parameters)
    {
        $attempts   = 0; // initial attempts
        $parameters = array_merge($parameters, $this->getDefaultParameters());
        $id         = $parameters["id"]; // request id

        while ($attempts < $this->getMaximumAttempts()) {
            $message = sprintf("Executing request %s...", $address);
            $this->getLogger()->debug($message, ["id" => $id]);

            try {
                $this->getHttpClient()->sendPost($address, $this->getDefaultHttpHeaders(), $parameters);

                $message = sprintf("Request [%s] was successfully executed", $address);

                $this->getLogger()->info($message, ["id" => $id]);
                break;
            } catch (RequestExecutionException $e) {
                $message = sprintf("Request [%s] execution problem: [%s]", $address, $e->getMessage());
                $this->getLogger()->warn($message, ["id" => $id]);

                $attempts++;
            }
        };
    }

    /**
     * Get maximum available attempts for Http request execution
     *
     * @return int
     */
    protected function getMaximumAttempts()
    {
        return 5;
    }

    /**
     * Get Monger Service Http API address
     *
     * @return HttpClient
     */
    protected function getServiceAddress()
    {
        return $this->serviceAddress;
    }

    /**
     * Get application name
     *
     * @return string
     */
    protected function getApplicationName()
    {
        return $this->applicationName;
    }

    /**
     * Get Application version
     *
     * @return string
     */
    protected function getApplicationVersion()
    {
        return $this->applicationVersion;
    }

    /**
     * Get user token
     *
     * @return string
     */
    protected function getUserToken()
    {
        return $this->userToken;
    }

    /**
     * Get user access token
     *
     * @return string
     */
    protected function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get logger
     *
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Get default request Http parameters with Action ID, UserToken and AccessToken
     *
     * @return array
     */
    protected function getDefaultParameters()
    {
        return [
            "id"    => $this->getUuid(),
            "user"  => $this->getUserToken(),
            "token" => $this->getAccessToken()
        ];
    }
}
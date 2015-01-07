<?php
/**
 * Created by PhpStorm.
 * User: ali
 * Date: 07.01.15
 * Time: 15:31
 */

namespace FnkLabs\Monger;

use Curl\Curl;
use Monolog\Logger;

/**
 * Curl Adapter
 */
class CurlAdapter extends Curl implements HttpClient
{
    private $logger;

    function __construct(Logger $logger)
    {
        $this->logger = $logger;

        parent::__construct();
    }


    /**
     * Send post request
     *
     * @param string       $url  http address
     * @param array|string $data post data
     *
     * @return array or null
     */
    public function post($url, $data = array())
    {
        $this->setopt(CURLOPT_URL, $url);
        $this->setopt(CURLOPT_POST, true);
        $this->setopt(CURLOPT_POSTFIELDS, $data);
        $this->_exec();

        $this->logger->debug("Response body: {$this->response}\n");

        return json_decode($this->response, true);
    }

    public function sendPost($uri, array $headers = [], array $parameters = [])
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        $this->setUserAgent(self::USER_AGENT_NAME);

        $response = $this->post($uri, json_encode($parameters));

        $this->reset();

        if (isset($response["status"]) && $response["status"] == true) {
            return;
        }
        if (isset($response["status"]) && $response["status"] == false && isset($response["message"])) {
            throw new RequestExecutionException($response["message"]);
        }

        throw new RequestExecutionException("Unexpected behaviour");
    }
}
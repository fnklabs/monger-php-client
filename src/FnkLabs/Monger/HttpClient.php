<?php
/**
 * Created by PhpStorm.
 * User: ali
 * Date: 07.01.15
 * Time: 14:22
 */

namespace FnkLabs\Monger;

/**
 * Http client interface
 *
 * @package FnkLabs\Monger
 */
interface HttpClient
{
    const USER_AGENT_NAME = "Monger php client";

    /**
     * @param String $uri        Monger Http API address
     * @param array  $headers    Http headers
     * @param Array  $parameters Post parameters
     *
     * @throws RequestExecutionException on request execution problems
     */
    public function sendPost($uri, array $headers = [], array $parameters = []);
}
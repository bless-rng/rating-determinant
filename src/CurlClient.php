<?php

namespace BlessRng;

use Exception;

class CurlClient
{
    /**
     * @param $endpoint
     * @return string|false
     * @throws Exception
     */
    public function getFromEndPoint($endpoint)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER,"Content-type: application/json");
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);

        curl_close($curl);
        return $result;
    }
}
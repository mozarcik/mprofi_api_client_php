<?php

namespace mprofi;

/**
 * Class Client for consuming mProfi API for sending messages.
 *
 * @package mprofi
 */
class Client
{
    /**
     * @var string Base URL for public API
     */
    public $baseUrl = 'https://api.mprofi.pl';

    /**
     * @var string API version
     */
    public $apiVersion = '1.0';

    /**
     * @var string Name of send endpoint
     */
    public $sendEndpoint = 'send';

    /**
     * @var string Name of sendbulk endpoint
     */
    public $sendBulkEndpoint = 'sendbulk';

    /**
     * @var string Name of status endpoint
     */
    public $statusEndpoint = 'status';

    /**
     * @var string API Token
     */
    private $apiToken;

    /**
     * Client constructor.
     *
     * @param string $apiToken
     */
    public function __construct($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Sends many messages at once.
     *
     * @param Message[] $messages
     *
     * @return integer[]
     * @throws InvalidTokenException
     * @throws \Exception
     */
    private function sendBulk(array $messages)
    {
        $payload = array_map(function ($m) {
            /** @var Message $m */
            return $m->getPayload();
        }, $messages);

        $response = $this->sendCurlRequest($this->sendBulkEndpoint, ['apikey' => $this->apiToken, 'messages' => $payload]);
        return array_map(function ($result) {
            return $result['id'];
        }, $response);
    }

    /**
     * Sends message or messages if $message parameter is array.
     *
     * @param Message|Message[] $message
     *
     * @return integer[]
     * @throws InvalidTokenException
     * @throws \Exception
     */
    public function send($message)
    {
        if (empty($message)) {
            throw new \InvalidArgumentException('You must pass at least one message');
        }
        $message = is_array($message) && count($message) === 1 ? reset($message) : $message;
        if (is_array($message)) {
            return $this->sendBulk($message);
        }

        $payload = array_merge($message->getPayload(), ['apikey' => $this->apiToken]);

        $response = $this->sendCurlRequest($this->sendEndpoint, $payload);

        return [$response['id']];
    }

    /**
     * Initializes curl and sends POST request with $payload encoded as json.
     *
     * @param $endpoint
     * @param $payload
     *
     * @return mixed
     * @throws InvalidTokenException
     * @throws \Exception
     */
    protected function sendCurlRequest($endpoint, $payload)
    {
        // init curl
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->createUrl($endpoint));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        switch ($httpCode) {
            default:
                throw new \Exception('API call failed with HTTP ' . $httpCode);
            case 401:
                throw new InvalidTokenException('API call failed with HTTP ' . $httpCode . ' - make sure the supplied API Token is valid');
        }
    }

    /**
     * Fetches status for message identified by $id.
     *
     * @param integer $id
     *
     * @return Status
     * @throws InvalidTokenException
     * @throws \Exception
     */
    public function getStatus($id)
    {
        // init curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->createUrl($this->statusEndpoint, ['apikey' => $this->apiToken, 'id' => $id]));
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpCode === 200) {
            $response = json_decode($response, true);
            return new Status($response['status'], $response['id'], $response['reference'], $response['ts']);
        }

        switch ($httpCode) {
            default:
                throw new \Exception('API call failed with HTTP ' . $httpCode);
            case 401:
                throw new InvalidTokenException('API call failed with HTTP ' . $httpCode . ' - make sure the supplied API Token is valid');
        }
    }

    /**
     * Create api url based on specified endpoint and url params.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return string
     */
    public function createUrl($endpoint, $params = [])
    {
        $url = join('/', [$this->baseUrl, $this->apiVersion, $endpoint, '']);

        if ($params === []) {
            return $url;
        }

        return $url . '?' . http_build_query($params);
    }
}

?>

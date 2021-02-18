<?php

namespace Xhgui\Profiler\Saver;

use Xhgui\Profiler\Exception\ProfilerException;

class UploadSaver implements SaverInterface
{
    /** @var string */
    private $url;
    /** @var int */
    private $timeout;
    /** @var bool */
    private $compress;

    public function __construct($url, $token, $timeout, $compress)
    {
        $this->url = $url;
        if ($token) {
            $this->url .= '?&token=' . $token;
        }

        $this->timeout = $timeout;
        $this->compress = $compress;
    }

    public function isSupported()
    {
        return $this->url && function_exists('curl_init');
    }

    public function save(array $data)
    {
        $json = json_encode($data);
        $this->submit($this->url, $json, $this->hasCompression());

        return true;
    }

    /**
     * @param string $url
     * @param string $payload
     * @param bool $compress
     */
    private function submit($url, $payload, $compress)
    {
        $ch = curl_init($url);
        if (!$ch) {
            throw new ProfilerException('Failed to create cURL resource');
        }

        $headers = array(
            // Prefer to receive JSON back
            'Accept: application/json',
            // The sent data is JSON
            $compress ? 'Content-Type: application/json+gzip' : 'Content-Type: application/json',
        );

        $res = curl_setopt_array($ch, array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $compress ? gzencode($payload) : $payload,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => $this->timeout,
        ));
        if (!$res) {
            throw new ProfilerException('Failed to set cURL options');
        }

        $result = curl_exec($ch);
        if ($result === false) {
            throw new ProfilerException('Failed to submit data');
        }
        curl_close($ch);

        $response = json_decode($result, true);
        if (!$response) {
            throw new ProfilerException('Failed to decode response');
        }

        if (isset($response['error']) && $response['error']) {
            $message = isset($response['message']) ? $response['message'] : 'Error in response';
            throw new ProfilerException($message);
        }
    }

    /**
     * @return bool
     */
    private function hasCompression()
    {
        return $this->compress && function_exists('gzencode');
    }
}

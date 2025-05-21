<?php
// filepath: private/classes/RtkApiClient.php
require_once __DIR__ . '/../config/constants.php';

/**
 * RTK API client for sending signed requests.
 */
class RtkApiClient {
    private string $baseUrl;
    private string $accessKey;
    private string $secretKey;
    private string $signMethod;
    private int $connectTimeout;
    private int $timeout;

    public function __construct(int $connectTimeout = 1, int $timeout = 1) {
        $this->baseUrl = API_BASE_URL;
        $this->accessKey = API_ACCESS_KEY;
        $this->secretKey = API_SECRET_KEY;
        $this->signMethod = API_SIGN_METHOD;
        $this->connectTimeout = $connectTimeout;
        $this->timeout = $timeout;
    }

    /**
     * Send a request to RTK API.
     * @param string $method HTTP method (GET, POST, PUT)
     * @param string $uri API URI path (e.g. '/openapi/broadcast/users')
     * @param array|null $body Optional request body for POST/PUT
     * @return array ['success'=>bool,'data'=>mixed,'error'=>string|null]
     */
    public function request(string $method, string $uri, array $body = null): array {
        $url = $this->baseUrl . $uri;
        // khi GET và có dữ liệu, append vào URL
        if (strtoupper($method) === 'GET' && !empty($body)) {
            $qs  = http_build_query($body);
            $url .= (strpos($url, '?') === false ? '?' : '&') . $qs;
        }

        $nonce = bin2hex(random_bytes(16));
        $timestamp = (string) round(microtime(true) * 1000);
        $headers = [
            'X-Nonce' => $nonce,
            'X-Access-Key' => $this->accessKey,
            'X-Sign-Method' => $this->signMethod,
            'X-Timestamp' => $timestamp,
        ];
        ksort($headers);
        // Build signature string
        $signStr = "$method $uri ";
        foreach ($headers as $k => $v) {
            $signStr .= strtolower($k) . "=$v&";
        }
        $sign = hash_hmac('sha256', rtrim($signStr, '&'), $this->secretKey);
        $headers['Sign'] = $sign;
        //error_log("RTK API Request: $method $uri\n" . json_encode($headers, JSON_PRETTY_PRINT));
        $headers['Content-Type'] = 'application/json';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        // disable SSL verification for self-signed certs
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // Set HTTP method and body
        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        } elseif (strtoupper($method) === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        // Add headers
        $curlHeaders = [];
        foreach ($headers as $k => $v) {
            $curlHeaders[] = "$k: $v";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'data' => null, 'error' => "cURL Error: $curlError"];
        }

        $responseData = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['code']) &&
            in_array($responseData['code'], ['SUCCESS', 'OK'], true)) {
            return ['success' => true, 'data' => $responseData['data'] ?? $responseData, 'error' => null];
        }
        return ['success' => false, 'data' => $responseData, 'error' => $responseData['msg'] ?? "HTTP Error: $httpCode"];
    }
}

<?php

namespace Email\Manager\classes;

class AdminLogin {

    private $admin_email;
    private $admin_password;
    private $cpanel_host;
    private $session_token;
    private $login_url;

    public function __construct($cpanel_host)
    {
        $this->admin_email = 'manuelseffe@gmail.com';
        $this->admin_password = '#newEmail01';
        $this->cpanel_host = $cpanel_host;
        $this->login_url = "https://{$this->cpanel_host}:2083/login/?login_only=1";
    }

    public function login() {
        $postData = [
            'user' => $this->admin_email,
            'pass' => $this->admin_password,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->login_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $response = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error: ' . json_last_error_msg());
        }

        // Check if the required keys exist in the response
        if (!isset($response['status'])) {
            throw new \Exception('Unexpected response format: ' . json_encode($response));
        }

        if ($response['status'] !== 1) {
            $statusMsg = isset($response['message']) ? $response['message'] : 'Unknown error';
            throw new \Exception('Error fetching session token: ' . $statusMsg);
        }

        if (!isset($response['security_token'])) {
            throw new \Exception('No security token found in response: ' . json_encode($response));
        }

        $this->session_token = $response['security_token'];
    }

    public function getSessionToken() {
        if ($this->session_token) {
            return $this->session_token;
        } else {
            throw new \Exception('No session token available. Please login first.');
        }
    }
}
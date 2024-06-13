<?php

namespace Email\Manager\classes;

class Email
{
    private $cpanel_user;
    private $cpanel_pass;
    private $cpanel_host;
    private $session_token;
    private $login_url;
    private $execute_url;

    public function __construct($cpanel_user, $cpanel_pass, $cpanel_host)
    {
        $this->cpanel_user = $cpanel_user;
        $this->cpanel_pass = $cpanel_pass;
        $this->cpanel_host = $cpanel_host;
        $this->login_url = "https://{$this->cpanel_host}:2083/login/?login_only=1";
        $this->execute_url = "https://{$this->cpanel_host}:2083/cpsessXXXXXXXXXX/execute/";
        $this->session_token = $this->getSessionToken();
    }

    private function getSessionToken()
    {
        $url = $this->login_url;
        $postData = [
            'user' => $this->cpanel_user,
            'pass' => $this->cpanel_pass,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $response = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error: ' . json_last_error_msg());
        }

        if ($response['status'] !== 1) {
            throw new \Exception('Error fetching session token: ' . $response['statusmsg']);
        }

        return $response['security_token'];
    }

    private function executeApiRequest($endpoint, $params)
    {
        $url = "{$this->execute_url}$endpoint";
        $query = http_build_query($params);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "{$this->cpanel_user}:{$this->cpanel_pass}",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("HTTP error: $httpCode. Response: $response");
        }

        if (strpos($contentType, 'application/json') === false) {
            throw new \Exception("Unexpected content type: $contentType. Response: $response");
        }

        $response = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error: ' . json_last_error_msg());
        }

        return $response;
    }

    public function listEmail($domain)
    {
        $endpoint = 'Email/list_pops';
        $params = ['domain' => $domain];

        $response = $this->executeApiRequest($endpoint, $params);
        if ($response['status'] == 1) {
            // Filter emails based on the specified domain
            $filteredEmails = array_filter($response['data'], function ($email) use ($domain) {
                return strpos($email['email'], '@' . $domain) !== false;
            });

            return $filteredEmails;
        } else {
            throw new \Exception('Error: ' . $response['errors'][0]);
        }
    }


    public function createEmail($email, $password, $domain)
    {
        $endpoint = 'Email/add_pop';
        $params = [
            'email' => $email,
            'password' => $password,
            'domain' => $domain,
            'quota' => 1024 // in MB, 0 means unlimited
        ];

        $response = $this->executeApiRequest($endpoint, $params);
        if ($response['status'] == 1) {
            return "Email account created successfully.";
        } else {
            throw new \Exception('Error: ' . $response['errors'][0]);
        }
    }

    public function updatePassword($email, $password, $domain)
    {
        $endpoint = 'Email/passwd_pop';
        $params = [
            'email' => $email,
            'password' => $password,
            'domain' => $domain
        ];

        $response = $this->executeApiRequest($endpoint, $params);
        if ($response['status'] == 1) {
            return "Password updated successfully.";
        } else {
            throw new \Exception('Error: ' . $response['errors'][0]);
        }
    }

    public function deleteEmail($email, $domain)
    {
        $endpoint = 'Email/del_pop';
        $params = [
            'email' => $email,
            'domain' => $domain
        ];

        $response = $this->executeApiRequest($endpoint, $params);
        if ($response['status'] == 1) {
            return "Email account deleted successfully.";
        } else {
            throw new \Exception('Error: ' . $response['errors'][0]);
        }
    }
}
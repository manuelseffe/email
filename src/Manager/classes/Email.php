<?php

namespace Email\Manager\classes;

class Email {

    private $cpanel_user;
    private $cpanel_pass;
    private $cpanel_host;
    
    public function __construct($cpanel_user,$cpanel_pass,$cpanel_host)
    {
        $this->cpanel_user = $cpanel_user;
        $this->cpanel_pass = $cpanel_pass;
        $this->cpanel_host = $cpanel_host;
    }

    private function executeApiRequest($endpoint, $params)
    {
        $url = "https://{$this->cpanel_host}:2083/cpsessXXXXXXXXXX/execute/$endpoint";
        $query = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->cpanel_user}:{$this->cpanel_pass}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

        $response = curl_exec($ch);
        if ($response === false) {
            die('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_encode($response, true);
    }

    public function listEmail($domain){
        $endpoint = 'Email/list_pops';
        $params = ['domain' => $domain];

        $response = $this->executeApiRequest($endpoint, $params);
        return $response['status'] == 1 ? $response['data'] : [];
    }

    public function createEmail($email, $password, $domain) {
        $endpoint = 'Email/add_pop';
        $params = [
            'email' => $email,
            'password' => $password,
            'domain' => $domain,
            'quota' => 1024 // in MB, 0 means unlimited
        ];

        $response = $this->executeApiRequest($endpoint, $params);
        return $response['status'] == 1 ? "Email account created successfully." : "Error: " . $response['errors'][0];
    }

    public function updatePassword($email, $password, $domain) {
        $endpoint = 'Email/passwd_pop';
        $params = [
            'email' => $email,
            'password' => $password,
            'domain' => $domain
        ];

        $response = $this->executeApiRequest($endpoint, $params);
        return $response['status'] == 1 ? "Password updated successfully." : "Error: " . $response['errors'][0];
    }

    public function deleteEmail($email, $domain) {
        $endpoint = 'Email/del_pop';
        $params = [
            'email' => $email,
            'domain' => $domain
        ];

        $response = $this->executeApiRequest($endpoint, $params);
        return $response['status'] == 1 ? "Email account deleted successfully." : "Error: " . $response['errors'][0];
    }
}

<?php

namespace App\Email;

use Cake\Core\Configure;
use MailchimpMarketing\ApiClient;

class MailChimp
{
    private $client;

    public function __construct()
    {
        $this->client = new ApiClient();

        $this->client->setConfig([
            'apiKey' => Configure::read('MailChimp.apiKey'),
            'server' => Configure::read('MailChimp.serverPrefix'),
        ]);
    }

    public function ping()
    {
        return $this->client->ping->get();
    }
}

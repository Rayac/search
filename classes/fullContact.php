<?php

namespace Rayac;


use GuzzleHttp\Client;

class fullContact
{
    private $apiKey;
    private $link;
    private $results;

    public function __construct($link)
    {
        $this->apiKey = "7835f83416929db0";
        $this->link = $this->prepareLink($link);
        $client = new Client();
        $this->results = $client->request('GET', $this->link);
    }

    public function results()
    {
        return json_decode($this->results->getBody()->getContents(), true);
    }

    private function prepareLink($link)
    {
        return 'https://api.fullcontact.com/v2/person.json?twitter=' . $link . '&apiKey=' . $this->apiKey;
    }
}
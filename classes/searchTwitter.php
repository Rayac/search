<?php

namespace Rayac;

use Exception;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class searchTwitter implements SearchInterface
{

    private $URL;
    private $name;
    private $Results = [];

    public function __construct($name){
        $this->name = $name;
        $this->prepareURL();
        $this->scrap();
    }

    public function scrap()
    {
        $client = new Client();
        $crawler = $client->request('GET', $this->URL);

        $crawler->filter('.ProfileCard')->each(function ($node) {
            $this->Results[] = [
                'Name' => trim($node->filter('.ProfileNameTruncated-link')->text()),
                'Description' => $node->filter('.ProfileCard-bio')->text(),
                'ImageURL' => $node->filter('.ProfileCard-avatarImage')->attr('src'),
                'ProfileURL' => "https://twitter.com" . $node->filter('.ProfileNameTruncated-link')->attr('href'),
                'Source' => "From Twitter"
            ];
//            $this->profileSearch("https://twitter.com" . $node->filter('.ProfileNameTruncated-link')->attr('href'));
        });
    }

    public function prepareURL()
    {
        $name = urlencode($this->name);

        $this->URL = "https://twitter.com/search?f=users&vertical=default&q=" . $name;
    }

    public function returnResults()
    {
        return $this->Results;
    }

    public function profileSearch($url)
    {
        try{
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $crawler->filter('.ProfileHeaderCard')->each(function ($node) {
            $this->Results[] = [
                'location' => trim($node->filter('.ProfileHeaderCard-locationText')->text()),
                'linksText' => trim($node->filter('.ProfileHeaderCard-urlText')->text()),
                'linksURL' => $node->filter('.ProfileHeaderCard-urlText .u-textUserColor')->attr('href'),
                'vineUrl' => $node->filter('.ProfileHeaderCard-vineProfileText a')->attr('href'),
            ];
        });}catch (Exception $e) {
        }
    }
}
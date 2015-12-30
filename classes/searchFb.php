<?php

namespace Rayac;


use Exception;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class searchFb implements SearchInterface
{

    private $URL;
    private $name;
    private $contents;
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
        try {
            if ($faceHtml = $crawler->filter('#u_0_6')->html()) {
                $first = strstr($faceHtml, '<div>');
                $secound = strstr($first, ' -->', TRUE);
                $this->contents = $secound;
            }
        } catch (Exception $e) {
        }

        if (is_string($this->contents)) {
            $crawler = new Crawler($this->contents);
            $crawler->filter('.detailedsearch_result')->each(function ($node) {
                $this->Results[] = [
                    'Name' => $node->filter('.instant_search_title')->text(),
                    'ImageURL' => $node->filter('.img')->attr('src'),
                    'ProfileURL' => $node->filter('._8o')->attr('href'),
                    'link' => substr(strrchr($node->filter('._8o')->attr('href'), "/"), 1),
                    'Source' => "facebook"
                ];
            });
        }
    }

    public function profileSearch($url)
    {
        
    }

    public function prepareURL()
    {
        $name = str_replace(" ", "+", $this->name);

        $this->URL = "https://www.facebook.com/public?query=" . $name;
    }

    public function returnResults()
    {
        return $this->Results;
    }
}
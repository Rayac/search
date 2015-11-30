<?php
/**
 * Created by PhpStorm.
 * User: nino
 * Date: 28-Nov-15
 * Time: 8:08 PM
 */

namespace Search;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;


class Search
{

    private $contents;
    private $name;
    private $FbURL;
    private $TwitterURL;
    private $Results = [];


    public function findHuman($name)
    {
        $this->name = $name;
        $this->prepareURLforFb();
        $this->prepareURLforTwitter();
        $this->getFbWebsite();
        $this->foundTwitter();
        $this->foundFb();
        return $this->Results;
    }

    private function foundFb()
    {
        $crawler = new Crawler($this->contents);

        $crawler->filter('.detailedsearch_result')->each(function ($node) {
            $this->Results[] = [
                'Name' => $node->filter('.instant_search_title')->text(),
                'ImageURL' => $node->filter('.img')->attr('src'),
                'ProfileURL' => $node->filter('._8o')->attr('href'),
                'Source' => "From Facebook"
            ];
        });

    }

    private function foundTwitter()
    {
        $client = new Client();
        $crawler = $client->request('GET', $this->TwitterURL);

        $crawler->filter('.ProfileCard')->each(function ($node) {
            $this->Results[] = [
                'Name' => trim($node->filter('.ProfileNameTruncated-link')->text()),
                'Description' => $node->filter('.ProfileCard-bio')->text(),
                'ImageURL' => $node->filter('.ProfileCard-avatarImage')->attr('src'),
                'ProfileURL' => $node->filter('.ProfileNameTruncated-link')->attr('href'),
                'Source' => "From Twitter"
            ];
        });
    }


    private function getFbWebsite()
    {
        $client = new Client();
        $crawler = $client->request('GET', $this->FbURL);
        if ($faceHtml = $crawler->filter('#u_0_6')->html()) {
            $first = strstr($faceHtml, '<div>');
            $secound = strstr($first, ' -->', TRUE);
            $this->contents = $secound;
        }

    }


    private function prepareURLforFb()
    {
        $name = str_replace(" ", "+", $this->name);

        $this->FbURL = "https://www.facebook.com/public?query=" . $name;

    }

    private function prepareURLforTwitter()
    {
        $name = urlencode($this->name);

        $this->TwitterURL = "https://twitter.com/search?f=users&vertical=default&q=" . $name;

    }
}
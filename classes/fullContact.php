<?php

namespace Rayac;


use Carbon\Carbon;
use GuzzleHttp\Client;

class fullContact
{
    private $pdo;
    private $apiKey;
    private $results;

    public function __construct($args, $pdoinput)
    {
        $this->apiKey = "7835f83416929db0";
        $this->pdo = $pdoinput;
        $this->results = $this->found($args);
    }

    public function results()
    {
        return unserialize($this->results);
    }

    private function found($args)
    {
        if ($this->inSearchHistory($args['link'])) {
            return $this->lastSearch($this->fetchSearchID($args['link']));
        }

        return $this->search($args);
    }

    private function prepareLinkForTwitter($link)
    {
        return 'https://api.fullcontact.com/v2/person.json?twitter=' . $link . '&apiKey=' . $this->apiKey;
    }

    private function prepareLinkForFacebook($link)
    {
        return 'https://api.fullcontact.com/v2/person.json?facebookUsername=' . $link . '&apiKey=' . $this->apiKey;
    }

    private function inSearchHistory($link)
    {
        $stmt = $this->pdo->prepare("SELECT Search, Date FROM peopleHistory WHERE Search = :search");
        $stmt->execute(['search' => trim($link)]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result && $this->isRecentSearch($result['Date'])) {
            return $result;
        }
        $this->clearSearchData($link);
        $this->updateSearchTime($link);
        return false;
    }

    private function lastSearch($searchID)
    {
        $stmt = $this->pdo->prepare("SELECT SearchData FROM peopleHistory WHERE ID = :id");
        $stmt->execute(['id' => $searchID]);
        $result = $stmt->fetchAll();

        return $result[0]['SearchData'];

    }

    private function saveSearch($link, $result, $source)
    {
        $stmt = $this->pdo->prepare("INSERT INTO peopleHistory SET Search = :search, Date = :date, SearchData = :searchdata, Source = :source");
        $stmt->execute([
            'search' => $link,
            'date' => Carbon::now(),
            'searchdata' => $result,
            'source' => $source
        ]);
    }

    private function fetchSearchID($name)
    {
        $stmt = $this->pdo->prepare("SELECT ID FROM peopleHistory WHERE Search = :search");
        $stmt->execute(['search' => $name]);
        $searchID = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $searchID['ID'];
    }

    private function isRecentSearch($time)
    {
        $time = Carbon::createFromFormat('Y-m-d H:i:s', $time);
        if ($time->diffInHours(Carbon::now()) < 24) {
            return true;
        }
        return false;
    }

    private function updateSearchTime($name)
    {
        $stmt = $this->pdo->prepare("UPDATE peopleHistory SET Date = :date WHERE ID = :id;");
        $stmt->execute([
            'date' => Carbon::now(),
            'id' => $this->fetchSearchID($name)
        ]);
    }

    private function clearSearchData($name)
    {
        $stmt = $this->pdo->prepare("DELETE FROM peopleHistory WHERE ID = :id");
        $stmt->execute([
            'id' => $this->fetchSearchID($name)
        ]);
    }

    private function search($args)
    {
        $client = new Client();
        if ($args['from'] == "twitter") {
            $response= $client->request('GET', $this->prepareLinkForTwitter($args['link']));
            $result = serialize(json_decode($response->getBody()->getContents(), true));
            $this->saveSearch($args['link'], $result, $args['from']);
            return $result;
        }
        if ($args['from'] == "facebook") {
            $response= $client->request('GET', $this->prepareLinkForFacebook($args['link']));
            $result = serialize(json_decode($response->getBody()->getContents(), true));
            $this->saveSearch($args['link'], $result, $args['from']);
            return $result;
        }
    }
}
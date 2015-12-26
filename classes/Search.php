<?php

namespace Rayac;

use Carbon\Carbon;
use Rayac\searchFb;
use Rayac\searchTwitter;

class Search
{
    private $Results = [];
    private $pdo;

    public function __construct($pdoinput)
    {
        $this->pdo = $pdoinput;
    }

    public function searchFor($name)
    {
        $update = $name;
        if ($this->inSearchHistory($update)) {
            return ($this->lastSearch($this->fetchSearchID($name)));
        }
        $this->findHuman($name);
        $this->saveSearch($name);
        return $this->Results;
    }

    private function findHuman($name)
    {
        $result = new searchTwitter($name);
        $this->Results['Twitter'] = $result->returnResults();
        $result = new searchFb($name);
        $this->Results['Facebook'] = $result->returnResults();
        return $this->Results;
    }

    private function inSearchHistory($name)
    {

        $stmt = $this->pdo->prepare("SELECT Search, Date FROM Search_history WHERE Search = :search");
        $stmt->execute(['search' => $name]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result && $this->isRecentSearch($result['Date'], $name)) {
            return $result;
        }
        $this->clearSearchData($name);
        $this->updateSearchTime($name);
        return false;
    }

    private function lastSearch($searchID)
    {
        $stmt = $this->pdo->prepare("SELECT SearchData FROM Search_history WHERE ID = :id");
        $stmt->execute(['id' => $searchID]);
        $result = $stmt->fetchAll();

        return unserialize($result[0]['SearchData']);
    }

    private function saveSearch($name)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Search_history SET Search = :search, Date = :date, SearchData = :searchdata");
        $stmt->execute([
            'search' => $name,
            'date' => Carbon::now(),
            'searchdata' => serialize($this->Results)
        ]);
    }

    private function fetchSearchID($name)
    {
        $stmt = $this->pdo->prepare("SELECT ID FROM Search_history WHERE Search = :search");
        $stmt->execute(['search' => $name]);
        $searchID = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $searchID['ID'];
    }

    private function isRecentSearch($time)
    {
        $time = Carbon::createFromFormat('Y-m-d H:i:s', $time);
        if ($time->diffInHours(Carbon::now()) < 2) {
            return true;
        }
        return false;
    }

    private function updateSearchTime($name)
    {
        $stmt = $this->pdo->prepare("UPDATE Search_history SET Date = :date WHERE ID = :id;");
        $stmt->execute([
            'date' => Carbon::now(),
            'id' => $this->fetchSearchID($name)
        ]);
    }

    private function clearSearchData($name)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Search_history WHERE ID = :id");
        $stmt->execute([
            'id' => $this->fetchSearchID($name)
        ]);
    }
}
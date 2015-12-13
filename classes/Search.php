<?php

namespace Rayac;

use Rayac\searchFb;
use Rayac\searchTwitter;

class Search
{
    private $Results = [];
    private $pdo;
    private $name;
    private $searchID;


    public function __construct($pdoinput)
    {
        $this->pdo = $pdoinput;
    }

    public function searchFor($name)
    {
        $this->name = $name;
        if ($this->inSearchHistory()) {
            $this->fetchSearchID();
            return ($this->lastSearch());
        }
        $this->findHuman();
        $this->saveSearch();
        $this->saveData();
        return $this->Results;

    }

    private function findHuman()
    {
        $result = new searchTwitter($this->name);
        $this->Results['Twitter'] = $result->returnResults();
        $result = new searchFb($this->name);
        $this->Results['Facebook'] = $result->returnResults();
        return $this->Results;
    }

    private function inSearchHistory()
    {
        $stmt = $this->pdo->prepare("SELECT Search, Date FROM Search_history WHERE Search = :search");
        $stmt->execute(['search' => $this->name]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function lastSearch()
    {
        $stmt = $this->pdo->prepare("SELECT Name, Description, ImageURL FROM People WHERE SearchID = :searchid");
        $stmt->execute(['searchid' => $this->searchID['ID']]);
        $result[] = $stmt->fetchAll();
        return $result;

    }

    private function saveSearch()
    {
        $stmt = $this->pdo->prepare("INSERT INTO Search_history SET Search = :search, Date = :date");
        $stmt->execute([
            'search' => $this->name,
            'date' => date("Y-m-d H:i:s")
        ]);
    }

    private function fetchSearchID()
    {
        $stmt = $this->pdo->prepare("SELECT ID FROM Search_history WHERE Search = :search");
        $stmt->execute(['search' => $this->name]);
        $this->searchID = $stmt->fetch(\PDO::FETCH_ASSOC);

    }


    private function saveData()
    {
        $this->fetchSearchID();
        foreach ($this->Results as $value) {
            foreach ($value as $key) {
                if (!isset($key['Description'])) {
                    $key['Description'] = "Nothing found!";
                }
                $stmt = $this->pdo->prepare("INSERT INTO People SET Name = :name, Description = :description, ImageURL = :imageurl, ProfileURL = :profileurl, SearchID = :searchid");
                $stmt->execute([
                    'name' => $key['Name'],
                    'description' => $key['Description'],
                    'imageurl' => $key['ImageURL'],
                    'profileurl' => $key['ProfileURL'],
                    'searchid' => $this->searchID['ID'],
                ]);
            }
        }
    }

}
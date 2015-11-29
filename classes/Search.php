<?php
/**
 * Created by PhpStorm.
 * User: nino
 * Date: 28-Nov-15
 * Time: 8:08 PM
 */

namespace Search;


class Search
{

    private $contents;
    private $name;
    private $url;


    public function findHuman($name)
    {
        $this->name = $name;
        $this->prepareUrl();
        $this->getWebsite();
        return $this->found();
    }

    private function found()
    {
        if ($this->noResults()) {
            return "Nothing found!";
        }

        $String = strstr($this->contents, '<div><div class="mbm detailedsearch_result">');
        if ($final = strstr($String, '<div class="mvs pam clearfix uiBoxGray">', TRUE)){
            return $final;
        }
        $final = strstr($String, '</div> --></code>', TRUE);
        return $final;

    }

    private function getWebsite()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);

        $this->contents = $data;
    }

    private function prepareUrl()
    {
        $name = str_replace(" ", "+", $this->name);

        $this->url = $url = "https://www.facebook.com/public?query=" . $name;

    }

    private function noResults()
    {
        if (strstr($this->contents, "No results")) {
            return true;
        }
        return false;
    }
}
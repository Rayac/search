<?php
/**
 * Created by PhpStorm.
 * User: nino
 * Date: 28-Nov-15
 * Time: 8:08 PM
 */

namespace Search;

use Search\searchFb;
use Search\searchTwitter;

class Search
{
    private $Results = [];


    public function findHuman($name)
    {
        $result = new searchTwitter($name);
        $this->Results['Twitter'] = $result->returnResults();
        $result = new searchFb($name);
        $this->Results['Facebook'] = $result->returnResults();
        return $this->Results;
    }

}
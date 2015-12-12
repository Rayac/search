<?php
/**
 * Created by PhpStorm.
 * User: nino
 * Date: 12-Dec-15
 * Time: 6:43 PM
 */

namespace Search;

interface SearchInterface
{

    public function scrap();
    public function prepareURL();
    public function returnResults();
    public function profileSearch($url);
}
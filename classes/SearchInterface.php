<?php

namespace Rayac;

interface SearchInterface
{

    public function scrap();
    public function prepareURL();
    public function returnResults();
    public function profileSearch($url);
}
<?php

namespace AppBundle\Entity;

class Product
{
    public $name;

    public $price;

    public $isCampaign;

    public function __construct($name, $price, $isCampaign = false)
    {
        $this->name = $name;
        $this->price = $price;
        $this->isCampaign = $isCampaign;
    }
}

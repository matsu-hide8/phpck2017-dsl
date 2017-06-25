<?php

namespace AppBundle\Service\Dsl;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use AppBundle\Entity\Cart;
use Symfony\Component\Yaml\Yaml;
use AppBundle\Entity\Discount;

class Discounter
{
    private $config;

    public function __construct($yaml)
    {
        $this->config = Yaml::parse($yaml);
    }

    public function discount(Cart $cart)
    {
        $expressionLanguage = new ExpressionLanguage();

        $cart->clearDiscount();
        foreach ($this->config as $name => $values) {
            $isSatisfied = $expressionLanguage->evaluate($values['条件'], ['カート' => $cart]);
            if (! $isSatisfied) continue;

            $price = $expressionLanguage->evaluate($values['割引'], ['カート' => $cart]);
            $cart->addDiscount(new Discount($name, $price));
        }
    }
}

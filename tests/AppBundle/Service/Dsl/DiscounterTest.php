<?php

namespace AppBundle\Strategy\Dsl;

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Product;
use AppBundle\Entity\Cart;
use AppBundle\Service\Dsl\Discounter;

class DiscounterTest extends TestCase
{
    /**
     * @test
     */
    public function ルールに従って割引できる()
    {
        $apple = new Product('リンゴ', 100);
        $orange = new Product('オレンジ', 50);
        $banana = new Product('バナナ', 200, true);

        $cart = new Cart();
        $cart->addProduct($apple, 5);
        $cart->addProduct($orange, 5);
        $cart->addProduct($banana, 2);

        $yaml = "
キャンペーン商品の割引:
  条件: カート.キャンペーン商品を含む()
  割引: カート.商品合計() * 0.1

送料無料:
  条件: カート.商品合計() > 1000
  割引: カート.送料()
";

        $discount = new Discounter($yaml);
        $discount->discount($cart);

        $discounts = $cart->getDiscounts();

        $this->assertCount(2, $discounts);

        // リンゴ: 100円 × 5個 = 500円
        // オレンジ: 50円 × 5個 = 250円
        // バナナ: 200円 × 2個 = 400円
        // 商品合計: 1150円

        $this->assertEquals('キャンペーン商品の割引', $discounts[0]->name);
        $this->assertEquals(1150 * 0.1, $discounts[0]->price);

        $this->assertEquals('送料無料', $discounts[1]->name);
        $this->assertEquals($cart->getShippingCost(), $discounts[1]->price);

        $this->assertEquals(1035, $cart->getTotalPrice());
    }
}

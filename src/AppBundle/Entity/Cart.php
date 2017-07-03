<?php

namespace AppBundle\Entity;

class Cart
{
    private $elements = [];

    private $discounts = [];

    public function addProduct(Product $product, $quantity)
    {
        if (! in_array($product->name, $this->elements)) {
            $this->elements[$product->name] = [
                'product' => $product,
                'quantity' => 0
            ];
        }

        $this->elements[$product->name]['quantity'] =
            $this->elements[$product->name]['quantity']
            + $quantity;
    }

    public function getProducts()
    {
        return $this->elements;
    }

    public function addDiscount(Discount $discount)
    {
        $this->discounts[] = $discount;
    }

    public function clearDiscount()
    {
        $this->discounts = [];
    }

    public function getDiscounts()
    {
        return $this->discounts;
    }

    public function getShippingCost()
    {
        return 500;
    }

    public function existsCampaignProduct()
    {
        foreach ($this->elements as $element) {
            if ($element['product']->isCampaign
                && $element['quantity'] > 0) return true;
        }
        return false;
    }

    public function getProductTotalPrice()
    {
        $price = 0;
        foreach ($this->elements as $element) {
            $price += ($element['product']->price * $element['quantity']);
        }
        return $price;
    }

    public function getTotalPrice()
    {
        $productTotalPrice = $this->getProductTotalPrice();

        $discountPrice = 0;
        foreach ($this->discounts as $discount) {
            $discountPrice += $discount->price;
        }

        return $productTotalPrice - $discountPrice + $this->getShippingCost();
    }

    public function 送料()
    {
        return $this->getShippingCost();
    }

    public function キャンペーン商品を含む()
    {
        return $this->existsCampaignProduct();
    }

    public function 商品合計()
    {
        return $this->getProductTotalPrice();
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Service\Dsl\Discounter;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="cart_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $cart = $this->createCart();

        $rule = "
キャンペーン商品の割引:
  条件: カート.キャンペーン商品を含む()
  割引: カート.商品合計() * 0.1

送料無料:
  条件: カート.商品合計() > 1000
  割引: カート.送料()
";

        return $this->render(
            'cart/index.html.twig',
            [
                'cart' => $cart,
                'rule' => $rule,
            ]);
    }

    /**
     * @Route("/cart", name="cart_index_post")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $cart = $this->createCart();
        // 実際にはバリデーションが必要です。
        $this->setQuantity($cart, $request);

        $discounter = new Discounter($request->request->get('rule'));
        $discounter->discount($cart);
dump($cart);
        return $this->render(
            'cart/index.html.twig',
            [
                'cart' => $cart,
                'rule' => $request->request->get('rule'),
            ]);
    }

    private function createCart()
    {
        $apple = new Product('リンゴ', 100);
        $orange = new Product('オレンジ', 50);
        $banana = new Product('バナナ', 200, true);

        $cart = new Cart();
        $cart->addProduct($apple, 0);
        $cart->addProduct($orange, 0);
        $cart->addProduct($banana, 0);

        return $cart;
    }

    private function setQuantity(Cart $cart, Request $request)
    {
        $names = [];
        $quantities = [];
        foreach ($request->request->all() as $key => $value) {
            if (strpos($key, 'name') === 0) $names[] = $value;
            if (strpos($key, 'quantity') === 0) $quantities[] = $value;
        }

        foreach ($names as $index => $name) {
            foreach ($cart->getProducts() as $element) {
                if ($element['product']->name != $name) continue;

                $cart->addProduct($element['product'], $quantities[$index]);
                break;
            }
        }
    }
}

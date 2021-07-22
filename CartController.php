<?php

class CartController
{
  protected $products;

  public function __construct($products) {
    $this->setProducts($products);
  }

  public function addToCart($product_name) {
    if(in_array($product_name, array_map(function($row) {
      return $row['name'];
    }, $this->products))) {
      $cart_data = $this->getCart();

      if(isset($cart_data[$product_name])) {
        $cart_data[$product_name] += 1;
        $result = ['type' => 'success', 'msg' => $product_name . " successfully increased on cart."];
      } else {
        $cart_data[$product_name] = 1;
        $result = ['type' => 'success', 'msg' => $product_name . " successfully added to cart."];
      }

      $this->setCart($cart_data);
      return $result;
    } else {
      return ['type' => 'error', 'msg' => $product_name . " does not exists!"];
    }
  }

  public function removeFromCart($product_name) {
    $cart_data = $this->getCart();
    if(isset($cart_data[$product_name])) {
      if($cart_data[$product_name] > 1) {
        $cart_data[$product_name] -= 1;
        $result = ['type' => 'success', 'msg' => $product_name . " successfully decucted from cart."];
      } else {
        unset($cart_data[$product_name]);
        $result = ['type' => 'success', 'msg' => $product_name . " successfully removed from cart."];
      }
      $this->setCart($cart_data);
      return $result;
    } else {
      return ['type' => 'error', 'msg' => $product_name . " does not exists in cart!"];
    }
  }

  public function getCartWithPrice() {
    $cart_products = $this->getCart();

    return array_map(function($item) use($cart_products) {
      $item['quantity'] = $cart_products[$item['name']];
      return $item;
    }, array_filter($this->getProducts(), function($item) use($cart_products) {
      return isset($cart_products[$item['name']]);
    }));
  }

  public function setCart($products) {
    $_SESSION['cart'] = $products;
  }

  public function getCart() {
    return $_SESSION['cart'] ?? [];
  }

  public function setProducts($products) {
    $this->products = array_map(function($row) {
      $row['price'] = round($row['price'], 2);
      return $row;
    }, $products);
  }

  public function getProducts() {
    return $this->products;
  }
}

?>

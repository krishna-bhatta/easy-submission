<?php
session_start();
ini_set('error_reporting', E_ALL);
$base_url = "/";
include 'CartController.php';

$cart_controller = new CartController([
  [ "name" => "Sledgehammer", "price" => 125.75 ],
  [ "name" => "Axe", "price" => 190.50 ],
  [ "name" => "Bandsaw", "price" => 562.131 ],
  [ "name" => "Chisel", "price" => 12.9 ],
  [ "name" => "Hacksaw", "price" => 18.45 ],
]);

$view = $_GET['view'] ?? 'index';


if(isset($_GET['action'])) {
  switch ($_GET['action']) {
    case 'addToCart':
      if(isset($_GET['product'])) {
        $_SESSION["toast"] = $cart_controller->addToCart($_GET['product']);
      } else {
        $_SESSION["toast"] = [
          'type' => 'error',
          'msg' => 'Unable to find product!'
        ];
      }
      break;
    case 'removeFromCart':
      if(isset($_GET['product'])) {
        $_SESSION["toast"] = $cart_controller->removeFromCart($_GET['product']);
      } else {
        $_SESSION["toast"] = [
          'type' => 'error',
          'msg' => 'Unable to find product!'
        ];
      }
      break;
    
    default:
      $_SESSION["toast"] = [
        'type' => 'error',
        'msg' => 'Invalid action: ' . $_GET['action'] . '.'
      ];
      break;
  }
  header("Location: $base_url?view=$view");
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css" integrity="sha512-usVBAd66/NpVNfBge19gws2j6JZinnca12rAe2l+d+QkLU9fiG02O1X8Q6hepIpr/EYKZvKx/I9WsnujJuOmBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Cart Demo</title>
</head>
<body>
<?php if($view == "index"): ?>
<div class="d-flex flex-column flex-md-row justify-content-around p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
  <h5 class="my-0 mr-md-auto font-weight-normal">Product List</h5>
  <a class="btn btn-primary" href="<?php echo $base_url . "?view=viewCart"; ?>">
    Cart <span class="badge badge-light"><?php echo count($cart_controller->getCart()); ?></span>
  </a>
</div>
<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-center row">
      <?php foreach($cart_controller->getProducts() as $product_row): ?>
      <div class="col-md-10">
        <div class="row p-2 bg-white border rounded">
            <div class="col-md-9 mt-1">
                <h5><?php echo $product_row['name']; ?></h5>
            </div>
            <div class="align-items-center align-content-center col-md-3 border-left mt-1">
                <div class="d-flex flex-row align-items-center">
                    <h4 class="mr-1">$<?php echo sprintf('%0.2f', $product_row['price']); ?></h4>
                </div>
                <div class="d-flex flex-column mt-4"><a href="<?php echo $base_url . "?view=$view&action=addToCart&product=" . $product_row['name']; ?>" class="btn btn-primary btn-sm">Add To Cart</a></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php elseif($view == 'viewCart'): ?>
<div class="d-flex flex-column flex-md-row justify-content-around p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
  <h5 class="my-0 mr-md-auto font-weight-normal">Cart View</h5>
  <a class="btn btn-primary" href="<?php echo $base_url; ?>">
    Product List
  </a>
</div>
<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-center row">
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Price</th>
          <th scope="col">Quantity</th>
          <th scope="col">Total</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
      <?php $cart_with_price = $cart_controller->getCartWithPrice();
      if(count($cart_with_price)): $grand_total = 0; ?>
      <?php foreach($cart_with_price as $product_row): 
        $row_total = round($product_row['price'] * $product_row['quantity'], 2); 
        $grand_total += $row_total; ?>
        <tr>
          <td><?php echo $product_row['name']; ?></td>
          <td>$<?php echo sprintf('%0.2f', $product_row['price']); ?></td>
          <td><?php echo $product_row['quantity']; ?></td>
          <td>$<?php echo sprintf('%0.2f', $row_total); ?></td>
          <td>
            <a href="<?php echo $base_url . "?view=$view&action=addToCart&product=" . $product_row['name']; ?>" class="btn btn-success btn-sm">+</a>
            <a href="<?php echo $base_url . "?view=$view&action=removeFromCart&product=" . $product_row['name']; ?>" class="btn btn-outline-danger btn-sm">-</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <th colspan="3" class="text-center">Grand Total</th>
          <th colspan="2">$<?php echo sprintf('%0.2f', $grand_total); ?></th>
        </tr>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">Empty</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
  <h4>404 - The page cannot be found</h4>
<?php endif; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/js/bootstrap.min.js" integrity="sha512-a6ctI6w1kg3J4dSjknHj3aWLEbjitAXAjLDRUxo2wyYmDFRcz2RJuQr5M3Kt8O/TtUSp8n2rAyaXYy1sjoKmrQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php if(isset($_SESSION['toast'], $_SESSION['toast']['type']) && in_array($_SESSION['toast']['type'], ['success', 'info', 'warning', 'error'])): ?>
<script>
  //Available types, success, info, warning, error
  toastr.<?php echo $_SESSION['toast']['type']; ?>('<?php echo $_SESSION['toast']['msg']; unset($_SESSION['toast']); ?>')
</script>
<?php endif; ?>
</body>
</html>
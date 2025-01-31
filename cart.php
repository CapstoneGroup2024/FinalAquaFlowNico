<!--------------- INCLUDES --------------->
<?php
    session_start();
    if (!isset($_SESSION['auth'])) {
        // User is not authenticated, redirect to index.php
        $_SESSION['message'] = "Please login first";
        header('Location: index.php');
        exit();
    }
    include('includes/header.php');
    include('includes/orderbar.php');
    include('functions/userFunctions.php');

    $userId = $_SESSION['user_id'];
?>

<section class="p-5 p-md-5 text-sm-start" id="Cart" style="margin-bottom: 100px">
    <form action="functions/order_code.php" method="POST">
        <div class="container" style="margin-top: 60px;">
            <div class="row">
                <div class="col-md-10">
                    <h1 style="font-family: 'suez one';color: #013D67;"><i class="fas fa-shopping-cart"></i> Cart</h1>
                </div>
            </div>
            <!--------------- CART BODY --------------->
            <div class="card-body shadow">
                <div class="row align-items-center p-2">
                    <div class="col-md-3">
                        <h6 style="font-family: 'Poppins'; font-size: 22px;">Items</h6>
                    </div>
                    <div class="col-md-2">
                        <h6 style="font-family: 'Poppins'; font-size: 22px;">Category</h6>
                    </div>
                    <div class="col-md-1">
                        <h6 style="font-family: 'Poppins'; font-size: 22px;">Price</h6>
                    </div>
                    <div class="col-md-1">
                        <h6 style="font-family: 'Poppins'; font-size: 22px;">Stocks</h6>
                    </div>
                    <div class="col-md-2">
                        <h6 style="font-family: 'Poppins'; font-size: 22px;">Quantity</h6>
                    </div>
                    <div class="col-md-1">
                        <h6 style="font-family: 'Poppins'; font-size: 22px;">Total</h6>
                    </div>
                    <div class="col-md-2">
                        <h6 style="font-family: 'Poppins'; font-size: 22px;">Remove</h6>
                    </div>
                </div>
                
                <?php
                $cartItems = getCartItemsByUserId($userId); // GET CART ITEMS BASED ON USER ID
                $_SESSION['cart'] = $cartItems;
                if (mysqli_num_rows($cartItems) > 0) {
                    foreach ($cartItems as $cart) { 
                        $productActive = isProductActive($cart['product_id'], $cart['category_id'], $con);
                        
                        if($productActive){
                            // Retrieve available stock for the current product
                            $availableStockQuery = "SELECT quantity FROM product WHERE id='{$cart['product_id']}'";
                            $stockResult = mysqli_query($con, $availableStockQuery);
                            $stockData = mysqli_fetch_assoc($stockResult);
                            $availableStock = $stockData['quantity'];
                    ?>
                <!--------------- CART ITEMS --------------->
                <div class="card shadow-sm mb-3 cart_data cartpage">
                    <div class="row align-items-center p-3">
                        <div class="col-md-1">
                            <img src="uploads/<?= $cart['product_image']; ?>" width="80px" alt="<?= $cart['product_name']; ?>" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-2">
                            <h5><?= $cart['product_name']; ?></h5>
                        </div>
                        <div class="col-md-2">
                            <h5><?= $cart['category_name']; ?></h5>
                        </div>
                        <div class="col-md-1">
                            <h5><span class="iprice"><?= $cart['selling_price']; ?></span></h5>
                            <span class="additional_price_hidden" style="display:none;"><?= $cart['additional_price']; ?></span>
                        </div>
                        <div class="col-md-1">
                            <h5 class="available-stock"><?= $availableStock ?></h5>
                        </div>

                        <div class="col-md-2" id="qty">
                            <div class="input-group mb-1" style="width:115px;">
                                <button class="input-group-text decrement-btn changeQuantity">-</button>
                                <input type="text" class="form-control bg-white text-center iqty input-qty" onchange="subTotal()" id="qty_<?= $cart['id']; ?>" value="<?= $cart['quantity']; ?>">
                                <button class="input-group-text increment-btn changeQuantity">+</button>
                                <input type="hidden" class="cart_id" name="cart_id" value="<?= $cart['id']; ?>">
                                <input type="hidden" class="product_id" name="product_id" value="<?= $cart['product_id']; ?>">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <h5><span class="total-price itotal"></span></h5>
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" name="cart_id" value="<?= $cart['id']; ?>">
                            <input type="submit" class="btn btn-danger text-white" name="deleteOrderBtn" value="Delete"></input>
                        </div>
                    </div>
                </div>
            <?php
                        } else {
                            ?>
                            <div class="card shadow-sm mb-3 cartpage" style="background-color: #DFE3E5;  opacity: 0.8;">
                                <div class="row align-items-center p-3">
                                    <div class="col-md-2">
                                        <img src="uploads/<?= $cart['product_image']; ?>" width="80px" alt="<?= $cart['product_name']; ?>" style="border-radius: 10px;">
                                    </div>
                                    <div class="col-md-2">
                                        <h5><?= $cart['product_name']; ?></h5>
                                    </div>
                                    <div class="col-md-2">
                                        <h5><?= $cart['category_name']; ?></h5>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 style="color: red; font-weight: bold; font-size: 17px">PRODUCT NOT AVAILABLE</h5>
                                    </div>
                                    <div class="col-md-1">
                                        <h5></h5>
                                    </div>
                                    <div class="col-md-1">
                                        <input type="hidden" name="cart_id" value="<?= $cart['id']; ?>">
                                        <input type="submit" class="btn btn-danger text-white" name="deleteOrderBtn" value="Delete"></input>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } else {
                    echo "No records found";
                }
            ?>

            </div>
        </div>

        <div class="container" style="width: 320px; display: flex; float:left;">
            <div class="card-body shadow p-3" style="border-radius: 20px;" style="font-family: 'Poppins';">
                <div class="col align-items-center p-2" style="text-align: center">
                    <h4>MODE OF PAYMENT</h4>
                    <div class="row-md-2 p-2 shadow-sm">
                        <h5 name="cod">✔ CASH ON DELIVERY</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" style="width: 400px; display: flex; float:right;">
            <div class="card-body shadow p-3" style="border-radius: 20px;">
                <div class="col align-items-center p-2" >
                <!--------------- SUBTOTAL --------------->
                    <div class="row-md-2 shadow-sm p-2" style="padding-top: 20px; margin-bottom: 0px; border-radius: 8px; align-items:center;">
                        <input type="hidden" name="subtotal" value="" class="subtotal-hidden">
                        <h5>Subtotal: <span class="subtotal-price" style="display: flex; float:right;"></span></h5>
                    </div>
                    <!--------------- ADDITIONAL FEE --------------->
                    <div class="row-md-2 shadow-sm p-2" style="padding-top: 20px; margin-bottom: 0px; border-radius: 8px; align-items:center;">
                        <input type="hidden" name="additional_fee" value="additional-fee-hidden" class="additional-fee-hidden">
                        <h5>Additional Fee: <span class="additional-fee" style="display: flex; float:right;"></span></h5>
                    </div>
                    <!--------------- GRAND TOTAL --------------->
                    <div class="row-md-2 shadow-sm p-2" style="padding-top: 20px; margin-bottom: 0px; border-radius: 8px; align-items:center;">
                        <input type="hidden" name="grandtotal" value="" class="grand-total-hidden">
                        <h5>Grand Total: <span class="grand-total" style="display: flex; float:right;"></span></h5>
                    </div>
                    <div>
                        <input type="submit" class="btn text-white" style="background-color: #013D67; width: 100%; font-family: 'Suez one'; margin-top:10px" name="placeOrderBtn" value="Place Order"></input>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>


<script src="assets/js/cartQty.js"></script>
<script src="assets/js/order.js"></script>
<!--------------- ALERTIFY JS --------------->
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
<?php
if (isset($_SESSION['message'])) { // CHECK IF SESSION MESSAGE VARIABLE IS SET
?>
    alertify.set('notifier','position', 'top-right');
    
    // Check if the message indicates success or failure
    <?php if ($_SESSION['success'] === true): ?>
        alertify.success('<?php echo $_SESSION['message']; ?>'); // DISPLAY SUCCESS MESSAGE NOTIF
    <?php else: ?>
        alertify.error('<?php echo $_SESSION['message']; ?>'); // DISPLAY ERROR MESSAGE NOTIF
    <?php endif; ?>
    
<?php
    unset($_SESSION['message']); // UNSET THE SESSION MESSAGE VARIABLE
    unset($_SESSION['success']); // UNSET THE SESSION SUCCESS VARIABLE
}
?>
</script>
<!--------------- FOOTER --------------->
<?php include('includes/footer.php'); ?>

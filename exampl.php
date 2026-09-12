<?php 
include('db.php'); 
session_start();

// Security: Only allow Sellers to see this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'seller') {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

// Get the Seller ID for this user
$seller_query = mysqli_query($conn, "SELECT id FROM sellers WHERE user_id = '$uid'");
$seller_data = mysqli_fetch_assoc($seller_query);

// If this user is a seller but hasn't set up their shop details yet
if (!$seller_data) {
    echo "<script>alert('Tafadhali kamilisha usajili wa duka lako kwanza.'); window.location='setup_shop.php';</script>";
    exit();
}

$seller_id = $seller_data['id'];
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard | Soko Chap</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; margin: 0; background: #f4f7f6; padding-bottom: 80px; }
        .nav { background: #27ae60; color: white; padding: 15px; text-align: center; font-weight: bold; }
        .container { padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h3 { margin-top: 0; color: #27ae60; }
        
        input, select, button { width: 100%; padding: 12px; margin: 8px 0; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; }
        label { font-size: 0.9rem; font-weight: bold; color: #555; }
        .btn-save { background: #27ae60; color: white; border: none; cursor: pointer; font-size: 1rem; }
        
        .product-item { display: flex; align-items: center; gap: 15px; padding: 10px 0; border-bottom: 1px solid #eee; }
        .product-item img { width: 50px; height: 50px; border-radius: 5px; object-fit: cover; }
    </style>
</head>
<body>

    <div class="nav">PANELI YA MUUZAJI (SELLER)</div>

    <div class="container">
        <!-- FORM TO ADD NEW PRODUCT -->
        <div class="card">
            <h3>Ongeza Bidhaa Mpya</h3>
            <form action="seller_actions.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>">
                
                <label>Jina la Bidhaa (Mfano: Nyanya chungu)</label>
                <input type="text" name="product_name" placeholder="Ingiza jina la bidhaa" required>

                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Bei (Tsh)</label>
                        <input type="number" name="price" placeholder="Mfano: 2000" required>
                    </div>
                    <div style="flex:1;">
                        <label>Inauzwa kwa (Unit)</label>
                        <select name="base_unit" required>
                            <option value="Kilo">Kilo (Kg)</option>
                            <option value="Fungu">Fungu</option>
                            <option value="Lita">Lita</option>
                            <option value="Gunia">Gunia</option>
                            <option value="Ndoo">Ndoo</option>
                        </select>
                    </div>
                </div>

                <label>Picha ya Bidhaa</label>
                <input type="file" name="image" accept="image/*" required>

                <button type="submit" name="add_product" class="btn-save">Weka Sokoni</button>
            </form>
        </div>
<!-- dddd -->
        <!-- LIST OF CURRENT PRODUCTS -->
        <div class="card">
            <h3>Bidhaa Zako Sokoni</h3>
            <?php
            $my_prods = mysqli_query($conn, "SELECT * FROM products WHERE seller_id = '$seller_id' ORDER BY id DESC");
            if (mysqli_num_rows($my_prods) > 0) {
                while($p = mysqli_fetch_assoc($my_prods)) {
                    echo "<div class='product-item'>
                            <img src='uploads/".$p['image']."' onerror=\"this.src='https://via.placeholder.com/50'\">
                            <div style='flex:1;'>
                                <b>".$p['product_name']."</b><br>
                                <small>Tsh ".number_format($p['price'])." kwa ".$p['base_unit']."</small>
                            </div>
                            <a href='seller_actions.php?delete=".$p['id']."' style='color:red;' onclick=\"return confirm('Futa bidhaa hii?')\"><i class='fa fa-trash'></i></a>
                          </div>";
                }
            } else {
                echo "<p style='color:grey; text-align:center;'>Huna bidhaa sokoni bado.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Bottom Nav -->
    <div style="position: fixed; bottom: 0; width: 100%; background: white; display: flex; justify-content: space-around; padding: 15px 0; border-top: 1px solid #ddd;">
        <a href="index.php" style="text-decoration:none; color:#888;"><i class="fa fa-home"></i> Nyumbani</a>
        <a href="profile.php" style="text-decoration:none; color:#27ae60;"><i class="fa fa-user"></i> Akaunti</a>
    </div>
















     <h2>Oda Mpya za Wateja</h2>

    <?php
    // Query order_items joined with main orders to see who is buying
    $q = "SELECT oi.*, o.buyer_name, o.buyer_phone, o.delivery_location, o.created_at, p.product_name 
          FROM order_items oi
          JOIN orders o ON oi.order_id = o.id
          JOIN products p ON oi.product_id = p.id
          WHERE oi.seller_id = '$seller_id'
          ORDER BY o.created_at DESC";

    $res = mysqli_query($conn, $q);

    if(mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)) {
            echo "<div class='order-card'>
                    <p><strong>Bidhaa:</strong> ".$row['product_name']." (x".$row['quantity'].")</p>
                    <p><strong>Mteja:</strong> ".$row['buyer_name']." (".$row['buyer_phone'].")</p>
                    <p><strong>Mahali:</strong> ".$row['delivery_location']."</p>
                    <small>Saa: ".$row['created_at']."</small>
                    <hr>
                    <button style='background: #27ae60; color:white; border:none; padding:5px 10px; border-radius:5px;'>Nimeandaa</button>
                  </div>";
        }
    } else {
        echo "<p>Bado hujapata oda yoyote.</p>";
    }
    ?>

</body>
</html>
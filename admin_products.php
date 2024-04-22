<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['add_product'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $category = mysqli_real_escape_string($conn, $_POST['category']);
   $price = mysqli_real_escape_string($conn, $_POST['price']);
   $details = mysqli_real_escape_string($conn, $_POST['details']);
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folter = 'uploaded_img/'.$image; 
      

   $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_product_name) > 0){
      $message[] = 'product name already exist!';
   }else{
      $latest_prod_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(product_id) AS id FROM `products`")) or die('query failed');
      $Prod = $latest_prod_id ['id'];
      $insert_product = mysqli_query($conn, "INSERT INTO `products`(product_id,prod_cat_id, name, details, price, image) VALUES($Prod+1,'$category', '$name', '$details', '$price', '$image')") or die('query failed');

      if($insert_product){
         if($image_size > 2000000){
            $message[] = 'image size is too large!';
         }else{
            move_uploaded_file($image_tmp_name, $image_folter);
            $message[] = 'product added successfully!';

            $checkbox1 = $_POST['chk1'] ;
            for ($i=0; $i<sizeof ($checkbox1);$i++) {  
               $query  = mysqli_query($conn, "INSERT INTO skin_type (product_id,skin_type_name) VALUES ($Prod+1,'".$checkbox1[$i]."')") or die('query failed');  
            } 
            $checkbox2 = $_POST['chk2'] ;
            for ($i=0; $i<sizeof ($checkbox2);$i++) {  
               $query  = mysqli_query($conn, "INSERT INTO skin_concern (product_id,skin_type_name) VALUES ($Prod+1,'".$checkbox2[$i]."')") or die('query failed');  
            } 
         }
      }

   }

}



if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $select_delete_image = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($select_delete_image);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
   mysqli_query($conn, "DELETE FROM `wishlist` WHERE pid = '$delete_id'") or die('query failed');
   mysqli_query($conn, "DELETE FROM `cart` WHERE pid = '$delete_id'") or die('query failed');
   mysqli_query($conn, "DELETE FROM skin_type WHERE skin_type.product_id IN (SELECT products.product_id FROM products WHERE products.id= '$delete_id')");
   mysqli_query($conn, "DELETE FROM skin_concern WHERE skin_concern.product_id IN (SELECT products.product_id FROM products WHERE products.id= '$delete_id')");
   header('location:admin_products.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<section class="add-products">

   <form action="" method="POST" enctype="multipart/form-data">
      <h3>add new product</h3>
      <input type="text" class="box" required placeholder="enter product name" name="name">
      
      <select name="category" id="" class="box" required>
         <option value="" disabled selected>Select product category</option>
         <option value="1">Cleansing</option>
         <option value="2">Toner</option>
         <option value="3">Treatment</option>
         <option value="4">Moisturizer</option>
         <option value="5">Sun Protection</option>
      </select>
      
      <input type="number" min="0" class="box" required placeholder="enter product price" name="price">
      <textarea name="details" class="box" required placeholder="enter product details" cols="30" rows="10"></textarea>

      <h4 style="font-size: 18px; text-transform: uppercase; color:var(--black); margin-bottom: 1rem;">
         Skin type Suitable with the products:
      </h4>
      <input type="checkbox" name="chk1[ ]" value="oily">Oily Skin &nbsp;
      <input type="checkbox" name="chk1[ ]" value="combination">Combination Skin &nbsp;
      <input type="checkbox" name="chk1[ ]" value="dry">Dry Skin&nbsp;
      <input type="checkbox" name="chk1[ ]" value="sensitive">Sensitive Skin&nbsp;
      
      <h4 style="font-size: 18px; text-transform: uppercase; color:var(--black); margin-bottom: 1rem; margin-top: 1rem;">
      Skin concern Suitable with the products:
      </h4>
      <input type="checkbox" name="chk2[ ]" value="Acne & Scarring">Acne & Scarring &nbsp;
      <input type="checkbox" name="chk2[ ]" value="Dull & Uneven Skin Tones">Dull & Uneven Skin Tones &nbsp;
      <input type="checkbox" name="chk2[ ]" value="Aging Skin">Aging Skin&nbsp;
      <input type="checkbox" name="chk2[ ]" value="Sun Damage">Sun Damage&nbsp;

      <input type="file" accept="image/jpg, image/jpeg, image/png" required class="box" name="image">
      <input type="submit" value="add product" name="add_product" class="btn">
   </form>

</section>

<section class="show-products">

   <div class="box-container">

      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <div class="box">
         <div class="price">RM<?php echo $fetch_products['price']; ?>/-</div>
         <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
         <div class="name"><?php echo $fetch_products['name']; ?></div>
         <div class="details"><?php echo $fetch_products['details']; ?></div>
         <a href="admin_update_product.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">edit</a>
         <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
      ?>
   </div>
   

</section>












<script src="js/admin_script.js"></script>

</body>
</html>
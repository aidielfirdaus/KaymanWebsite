<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['update_product'])){

   $update_p_id = $_POST['update_p_id'];
   $update_prod_id = $_POST['update_prod_id'];
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = mysqli_real_escape_string($conn, $_POST['price']);
   $details = mysqli_real_escape_string($conn, $_POST['details']);

   mysqli_query($conn, "UPDATE `products` SET name = '$name', details = '$details', price = '$price' WHERE id = '$update_p_id'") or die('query failed');

   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folter = 'uploaded_img/'.$image;
   $old_image = $_POST['update_p_image'];
   
   $checkbox1 = $_POST['chk1'] ;
   mysqli_query($conn, "DELETE FROM skin_type WHERE skin_type.product_id IN (SELECT products.product_id FROM products WHERE products.id= '$update_p_id')");
   for ($i=0; $i<sizeof ($checkbox1);$i++) {
      $query  = mysqli_query($conn, "INSERT INTO skin_type (product_id,skin_type_name) VALUES ($update_prod_id,'".$checkbox1[$i]."')") or die('query failed');  
   } 
   $checkbox2 = $_POST['chk2'] ;
   mysqli_query($conn, "DELETE FROM skin_concern WHERE skin_concern.product_id IN (SELECT products.product_id FROM products WHERE products.id= '$update_p_id')");
   for ($i=0; $i<sizeof ($checkbox2);$i++) {  
      $query  = mysqli_query($conn, "INSERT INTO skin_concern (product_id,skin_concern_name) VALUES ($update_prod_id,'".$checkbox2[$i]."')") or die('query failed');  
   } 

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image file size is too large!';
      }else{
         mysqli_query($conn, "UPDATE `products` SET image = '$image' WHERE id = '$update_p_id'") or die('query failed');
         move_uploaded_file($image_tmp_name, $image_folter);
         unlink('uploaded_img/'.$old_image);
         $message[] = 'image updated successfully!';
      }
   }

   $message[] = 'product updated successfully!';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update product</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<section class="update-product">

<?php

   $update_id = $_GET['update'];
   $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('query failed');
   if(mysqli_num_rows($select_products) > 0){
      while($fetch_products = mysqli_fetch_assoc($select_products)){
?>

<form action="" method="post" enctype="multipart/form-data">
   <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" class="image"  alt="">
   <input type="hidden" value="<?php echo $fetch_products['id']; ?>" name="update_p_id">
   <input type="hidden" value="<?php echo $fetch_products['product_id']; ?>" name="update_prod_id">
   <input type="hidden" value="<?php echo $fetch_products['image']; ?>" name="update_p_image">
   <input type="text" class="box" value="<?php echo $fetch_products['name']; ?>" required placeholder="update product name" name="name">
   <input type="number" min="0" class="box" value="<?php echo $fetch_products['price']; ?>" required placeholder="update product price" name="price">
   <textarea name="details" class="box" required placeholder="update product details" cols="30" rows="10"><?php echo $fetch_products['details']; ?></textarea>

   <?php
   /*Oily Skin */
   $select_skin_type = mysqli_fetch_array(mysqli_query($conn, "SELECT skin_type_name FROM skin_type t LEFT JOIN products p on p.product_id=t.product_id WHERE p.id = '$update_id'")) or die('query failed');
         if($select_skin_type['skin_type_name'] == 'oily'){?>
            <input type="checkbox" name="chk1[ ]" value="oily" checked>Oily Skin &nbsp;
   <?php } else{?>
         <input type="checkbox" name="chk1[ ]" value="oily">Oily Skin &nbsp;  
   <?php }
   /*Combination Skin */
   $select_skin_type = mysqli_fetch_array(mysqli_query($conn, "SELECT skin_type_name FROM skin_type t LEFT JOIN products p on p.product_id=t.product_id WHERE p.id = '$update_id'")) or die('query failed');
         if($select_skin_type['skin_type_name'] == 'combination'){
   ?>
         <input type="checkbox" name="chk1[ ]" value="combination" checked>Combination Skin &nbsp;
   <?php } else{?>
         <input type="checkbox" name="chk1[ ]" value="combination">Combination Skin &nbsp;  
   <?php }
   /*Dry Skin */
   $select_skin_type = mysqli_fetch_array(mysqli_query($conn, "SELECT skin_type_name FROM skin_type t LEFT JOIN products p on p.product_id=t.product_id WHERE p.id = '$update_id'")) or die('query failed');
         if($select_skin_type['skin_type_name'] == 'dry'){
   ?>
         <input type="checkbox" name="chk1[ ]" value="dry" checked>Dry Skin &nbsp;
   <?php } else{?>
         <input type="checkbox" name="chk1[ ]" value="dry">Dry Skin &nbsp;  
   <?php }
   /*Sensitive Skin */
   $select_skin_type = mysqli_fetch_array(mysqli_query($conn, "SELECT skin_type_name FROM skin_type t LEFT JOIN products p on p.product_id=t.product_id WHERE p.id = '$update_id'")) or die('query failed');
         if($select_skin_type['skin_type_name'] == 'sensitive'){
   ?>
         <input type="checkbox" name="chk1[ ]" value="sensitive" checked>Sensitive Skin &nbsp;
   <?php } else{?>
         <input type="checkbox" name="chk1[ ]" value="sensitive">Sensitive Skin &nbsp;  
   <?php }
   ?>
   <br>
   <br>
   
    <!-- SKin concern belum buat -->
   <?php
   /*Acne & Scarring */
   $select_skin_concern = mysqli_query($conn, "SELECT skin_concern_name FROM skin_concern c LEFT JOIN products p on p.product_id=c.product_id WHERE p.id = '$update_id'") or die('query failed');
   if(mysqli_num_rows($select_skin_concern) > 0){
      while($fetch_skin_concern = mysqli_fetch_assoc($select_skin_concern)){
         if($fetch_skin_concern['skin_concern_name'] == 'Acne & Scarring'){
   ?>
         <input type="checkbox" name="chk2[ ]" value="Acne & Scarring" checked>Acne & Scarring &nbsp;
   <?php } else{?>
         <input type="checkbox" name="chk2[ ]" value="Acne & Scarring">Acne & Scarring &nbsp;  
   <?php } break;} } 
   /*Dull & Uneven Skin Tones */
   $select_skin_concern = mysqli_query($conn, "SELECT skin_concern_name FROM skin_concern c LEFT JOIN products p on p.product_id=c.product_id WHERE p.id = '$update_id'") or die('query failed');
   if(mysqli_num_rows($select_skin_concern) > 0){
      while($fetch_skin_concern = mysqli_fetch_assoc($select_skin_concern)){
         if($fetch_skin_concern['skin_concern_name'] == 'Dull & Uneven Skin Tones'){
   ?>
         <input type="checkbox" name="chk2[ ]" value="Dull & Uneven Skin Tones" checked>Dull & Uneven Skin Tones &nbsp;
   <?php } else{?>
         <input type="checkbox" name="chk2[ ]" value="Dull & Uneven Skin Tones">Dull & Uneven Skin Tones &nbsp;  
   <?php } break;} } 
   /*Aging Skin */
   $select_skin_concern = mysqli_query($conn, "SELECT skin_concern_name FROM skin_concern c LEFT JOIN products p on p.product_id=c.product_id WHERE p.id = '$update_id'") or die('query failed');
   if(mysqli_num_rows($select_skin_concern) > 0){
      while($fetch_skin_concern = mysqli_fetch_assoc($select_skin_concern)){
         if($fetch_skin_concern['skin_concern_name'] == 'Aging Skin'){
   ?>
         <input type="checkbox" name="chk2[ ]" value="Aging Skin" checked>Aging Skin &nbsp;
   <?php } else{?>
         <input type="checkbox" name="chk2[ ]" value="Aging Skin">Aging Skin &nbsp;  
   <?php } break;} }
   /*Sensitive Skin */
   $select_skin_concern = mysqli_query($conn, "SELECT skin_concern_name FROM skin_concern c LEFT JOIN products p on p.product_id=c.product_id WHERE p.id = '$update_id'") or die('query failed');
   if(mysqli_num_rows($select_skin_concern) > 0){
      while($fetch_skin_concern = mysqli_fetch_assoc($select_skin_concern)){
         if($fetch_skin_concern['skin_concern_name'] == 'Sun Damage'){
   ?>
         <input type="checkbox" name="chk2[ ]" value="Sun Damage" checked>Sun Damage &nbsp;
   <?php } else{?>
         <input type="checkbox" name="chk2[ ]" value="Sun Damage">Sun Damage &nbsp;  
   <?php } break;} } 
   ?>

   

   <input type="file" accept="image/jpg, image/jpeg, image/png" class="box" name="image">
   <input type="submit" value="update product" name="update_product" class="btn">
   <a href="admin_products.php" class="option-btn">go back</a>
</form>

<?php
      }
   }else{
      echo '<p class="empty">no update product select</p>';
   }
?>

</section>













<script src="js/admin_script.js"></script>

</body>
</html>
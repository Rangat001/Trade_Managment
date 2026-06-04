<?php
require_once 'includes/auth_check.php';

// Check permission - only admin can add products
if (!$is_admin) {
    $_SESSION['error'] = 'You do not have permission to add products.';
    header('Location: products.php');
    exit;
}
?>
<?php
   
        $dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
        $company_id = $_POST['company_id'];
        $product_name = $_POST['product_name'];
        $hsn_code = $_POST['hsn_code'];
        $catogery = $_POST['category']; 
        $barcode_no = $_POST['barcode_no'];
        $gst = $_POST['gst'];
        $purchase_price = $_POST['purchase_price'];
        $selling_price = $_POST['selling_price'];
        $assurance = $_POST['assurance'];
        $validity = $_POST['validity'];
        $initial_quantity = 0; // current_stock
  
        if(empty($company_id) || empty($product_name) || empty($hsn_code) || empty($barcode_no) || empty($gst) || empty($purchase_price) || empty($selling_price) || empty($assurance) || empty($validity) ){    // isset for quantity because it can be 0
            print_r($_POST);
            die("All fields are required.");
        }
        // Handle Form Submission
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $sql = "INSERT INTO `products`(`dealer_id`, `company_id`, `product_name`,`category`,  `HSN`,`GST`,`Barcode`,`base_price`, `selling_price`, `assurance`, `validity`, `current_stock`) VALUES ('$dealer_id','$company_id','$product_name','$catogery','$hsn_code','$gst','$barcode_no', '$purchase_price','$selling_price','$assurance','$validity','$initial_quantity')";
            if ($conn->query($sql) === TRUE) {
                $product_id = $conn->insert_id; // Get the ID of the newly inserted product
                // Insert product price history when procut added for the first time and create new row for same when price updates
                $price_history_sql = "INSERT INTO `product_price_history`(`dealer_id`,`product_id`, `base_price`, `selling_price`,`effective_from`) VALUES ('$dealer_id','$product_id','$purchase_price','$selling_price',CURDATE())";
                if ($conn->query($price_history_sql) === TRUE) {
                    // Price history inserted successfully
                } else {
                    die("Error: " . $price_history_sql . "<br>" . $conn->error);
                }
                header("Location: products.php");
            } else {
                die("Error: " . $sql . "<br>" . $conn->error);
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

?>
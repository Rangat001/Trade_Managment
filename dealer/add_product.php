<?php
    require '../includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['rgt_logedin_user_id']) && (trim ($_SESSION['rgt_logedin_user_id']) !== '')){
        $user_id = $_SESSION['rgt_logedin_user_id'];
        $user_role = $_SESSION['rgt_logedin_user_role'];
        if($user_role !== "ADMIN"){
            header("Location: ../404.php");
        }
    }else{
        header("Location: ../sign-in.php");
    }
?>
<?php
   
        $dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
        $company_id = $_POST['company_id'];
        $product_name = $_POST['product_name'];
        $purchase_price = $_POST['purchase_price'];
        $selling_price = $_POST['selling_price'];
        $initial_quantity = $_POST['initial_quantity']; // current_stock
  
        if(empty($company_id) || empty($product_name) || empty($purchase_price) || empty($selling_price) || !isset($initial_quantity)){    // isset for quantity because it can be 0
            print_r($_POST);
            die("All fields are required.");
        }
        // Handle Form Submission
        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $sql = "INSERT INTO `products`(`dealer_id`, `company_id`, `product_name`, `base_price`, `selling_price`, `current_stock`) VALUES ('$dealer_id','$company_id','$product_name','$purchase_price','$selling_price','$initial_quantity')";
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
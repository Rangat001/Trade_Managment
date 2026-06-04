<?php
require_once 'includes/auth_check.php';

// Check permission - only admin can edit products
if (!$is_admin) {
    $_SESSION['error'] = 'You do not have permission to edit products.';
    header('Location: products.php');
    exit;
}

// Handle Form Submission
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
        $product_id = $_POST['product_id'];
        $category = $_POST['category'];
        $product_name = $_POST['product_name'];
        $purchase_price = $_POST['purchase_price'];
        $selling_price = $_POST['selling_price'];
        
        // Validate input
        if(empty($product_id) || empty($product_name) || empty($purchase_price) || empty($selling_price)){
            $_SESSION['error_message'] = "All fields are required.";
            print_r($_POST);
            die("All fields are required.");
            // header("Location: products.php");
            exit();
        }
        
        // Update product details
        $update_sql = "UPDATE products 
                      SET product_name = '$product_name', 
                          base_price = '$purchase_price', 
                          selling_price = '$selling_price',
                          category = '$category'
                      WHERE id = '$product_id' AND dealer_id = '$dealer_id'";
        
        if($conn->query($update_sql) === TRUE){
            // If price changed, insert new row in price history
            
                $price_history_sql = "INSERT INTO product_price_history 
                                     (dealer_id, product_id, base_price, selling_price, effective_from) 
                                     VALUES 
                                     ('$dealer_id', '$product_id', '$purchase_price', '$selling_price', NOW())";
                
                if($conn->query($price_history_sql) === TRUE){
                    $_SESSION['success_message'] = "Product updated successfully and price history recorded.";
                } else {
                    $_SESSION['error_message'] = "Product updated but failed to record price history: " . $conn->error;
                }
            
            
            header("Location: products.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating product: " . $conn->error;
            header("Location: products.php");
            exit();
        }
       
    } else {
        header("Location: products.php");
        exit();
    }
    
    $conn->close();
?>
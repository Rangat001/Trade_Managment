<?php
session_start();
require '../includes/scripts/connection.php';
require_once '../vendor/autoload.php';

use Mpdf\Mpdf;

if (!isset($_SESSION['rgt_logedin_user_dealer_id'])) {
    die('Unauthorized');
}

$dealer_id = $_SESSION['rgt_logedin_user_dealer_id'];
$sale_id   = (int) ($_GET['sale_id'] ?? 0);

if ($sale_id <= 0) {
    die('Invalid Sale');
}

/* ===============================
   FETCH SALE DATA
================================ */

$dealerStmt = $conn->prepare("
    SELECT business_name
    FROM dealer
    WHERE id = ?
");

$dealerStmt->bind_param("i", $dealer_id);
$dealerStmt->execute();
$dealer = $dealerStmt->get_result()->fetch_assoc();
$dealerStmt->close();


$saleStmt = $conn->prepare("
    SELECT *
    FROM sales
    WHERE id = ? AND dealer_id = ? AND is_deleted = 0
");

$saleStmt->bind_param("ii", $sale_id, $dealer_id);
$saleStmt->execute();
$sale = $saleStmt->get_result()->fetch_assoc();
$saleStmt->close();

if (!$sale) {
    die('Sale not found');
}

/* ===============================
   FETCH SALE ITEMS
================================ */

$itemStmt = $conn->prepare("
    SELECT 
        p.product_name,
        si.quantity,
        si.selling_price,
        si.base_price,
        (si.quantity * si.selling_price) AS total
    FROM sale_items si
    JOIN products p ON p.id = si.product_id
    WHERE si.sale_id = ?
");
$itemStmt->bind_param("i", $sale_id);
$itemStmt->execute();
$items = $itemStmt->get_result();

/* ===============================
   BUILD HTML
================================ */

$html = '
<style>
body { font-family: sans-serif; font-size: 12px; }
.header { text-align: center; }
.invoice-title { font-size: 20px; font-weight: bold; }
.table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.table th, .table td {
    border: 1px solid #000;
    padding: 8px;
}
.table th { background: #f2f2f2; }
.total { text-align: right; font-weight: bold; }
.footer { text-align: center; margin-top: 30px; font-size: 11px; }
</style>

<div class="header">
    <div class="invoice-title">'.htmlspecialchars($dealer['business_name']).'</div>
    <div class="invoice-title">SALES INVOICE : BL'.str_pad($sale_id, 6, '0', STR_PAD_LEFT).'</div>
    <p>Date: '.date('d-m-Y', strtotime($sale['sale_date'])).'</p>
</div>

<table class="table">
<tr>
    <th>#</th>
    <th>Product</th>
    <th>Qty</th>
    <th>Rate</th>
    <th>Total</th>
</tr>
';

$sr = 1;
$grandTotal = 0;

while ($row = $items->fetch_assoc()) {
    $grandTotal += $row['total'];

    $html .= '
    <tr>
        <td>'.$sr++.'</td>
        <td>'.htmlspecialchars($row['product_name']).'</td>
        <td>'.$row['quantity'].'</td>
        <td>₹'.number_format($row['selling_price'], 2).'</td>
        <td>₹'.number_format($row['total'], 2).'</td>
    </tr>';
}

$html .= '
<tr>
    <td colspan="4" class="total">Grand Total</td>
    <td><strong>₹'.number_format($grandTotal, 2).'</strong></td>
    
    
</tr>
<tr>
    <td colspan="4" class="total">Discount</td>
    <td><strong>₹'.number_format($sale['bill_amount'] - $sale['total_amount'] ?? 0, 2).'</strong></td>
</tr>
<tr>
    <td colspan="4" class="total">Total</td>
    <td><strong>₹'.number_format($sale['total_amount'] ?? 0, 2).'</strong></td>
</tr>
</table>

<p><strong>Billing Type:</strong> '.$sale['billing_type'].'</p>
<p><strong>Payment Mode:</strong> '.$sale['payment_mode'].'</p>

<div class="footer">
    Thank you
</div>
';

/* ===============================
   GENERATE PDF
================================ */

$mpdf = new Mpdf([
    'format' => 'A4',
    'margin_top' => 10,
    'margin_bottom' => 10
]);

$mpdf->WriteHTML($html);
$mpdf->Output('Invoice_'.$sale_id.'.pdf', 'I');

<?php
require '../includes/scripts/connection.php';

$sale_id = (int)($_GET['sale_id'] ?? 0);
if ($sale_id <= 0) die('Invalid Sale');

// Fetch sale
$saleStmt = $conn->prepare("SELECT * FROM sales WHERE id = ? AND is_deleted = 0");
$saleStmt->bind_param("i", $sale_id);
$saleStmt->execute();
$sale = $saleStmt->get_result()->fetch_assoc();
$saleStmt->close();
if (!$sale) die('Sale not found');

// Fetch dealer
$dealerStmt = $conn->prepare("SELECT business_name, phone, email, GST_NO, Address FROM dealer WHERE id = ?");
$dealerStmt->bind_param("i", $sale["dealer_id"]);
$dealerStmt->execute();
$dealer = $dealerStmt->get_result()->fetch_assoc();
$dealerStmt->close();

// Fetch sale items with product details
$itemStmt = $conn->prepare("
    SELECT 
        p.product_name,
        p.HSN,
        p.Barcode,
        p.GST,
        p.assurance,
        p.validity,
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
$items = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$itemStmt->close();

//is GST bill

$isGST = ($sale["billing_type"] == "GST");

// Calculations
$grandTotal   = array_sum(array_column($items, 'total'));
$discount     = $sale['bill_amount'] - $sale['total_amount'];
$totalAmount  = $sale['total_amount'];
$invoiceNo    = 'BL' . str_pad($sale_id, 6, '0', STR_PAD_LEFT);
$saleDate     = date('d-m-Y', strtotime($sale['sale_date']));
$printTime    = date('d-m-Y H:i:s');

// Apply discount proportionally
$totalGSTAmount = 0;
$taxableAmount  = 0;

foreach ($items as $item) {
    $itemTotal       = $item['quantity'] * $item['selling_price'];
    $gstAmount       = $itemTotal * ($item['GST'] / 100);
    $totalGSTAmount += $gstAmount;
    $taxableAmount  += $itemTotal; // before GST addition
}

// GST breakdown (assuming total_amount is GST inclusive)
// Weighted average GST % across items
$discountRatio  = $totalAmount / $grandTotal;
$taxableAmount  = $taxableAmount * $discountRatio;
$totalGSTAmount = $totalGSTAmount * $discountRatio;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Receipt <?= $invoiceNo ?></title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    width: 80mm;
    margin: 0 auto;
    padding: 4px 6px;
  }

  .center  { text-align: center; }
  .right   { text-align: right; }
  .bold    { font-weight: bold; }
  .large   { font-size: 13px; }
  .small   { font-size: 10px; }

  .divider-solid  { border-top: 1px solid #000; margin: 4px 0; }
  .divider-dashed { border-top: 1px dashed #000; margin: 4px 0; }

  /* Info rows */
  .info-row { display: flex; justify-content: space-between; margin: 1px 0; }
  .info-row .label { }
  .info-row .value { text-align: right; }

  /* Items */
  .item-block { margin: 4px 0; }
  .item-name-row { display: flex; justify-content: space-between; }
  .item-name  { flex: 1; font-weight: bold; }
  .item-price { text-align: right; min-width: 50px; }
  .item-meta  { color: #333; font-size: 10px; margin-left: 2px; }
  .item-qty-row { display: flex; justify-content: space-between; font-size: 10px; }

  /* Summary */
  .summary-row { display: flex; justify-content: space-between; margin: 1px 0; }
  .summary-row.bold-row { font-weight: bold; font-size: 12px; }

  .footer { text-align: center; margin-top: 6px; font-size: 10px; }

  @media print {
    html, body { width: 80mm; }
    .no-print  { display: none; }
    @page {
      size: 80mm auto;
      margin: 0;
    }
  }
</style>
</head>
<body>

<!-- ░░ DEALER HEADER ░░ -->
<div class="center">
    <div class="bold large"><?= htmlspecialchars($dealer['business_name']) ?></div>
    <div><?= htmlspecialchars($dealer['Address']) ?></div>
    <div>Contact : <?= htmlspecialchars($dealer['phone']) ?></div>
    <div>Email : <?= htmlspecialchars($dealer['email']) ?></div>
    <div>GST No : <?= htmlspecialchars($dealer['GST_NO']) ?></div>
</div>

<div class="divider-solid"></div>
<div class="center bold large">TAX INVOICE</div>
<div class="center">Invoice No : <?= $invoiceNo ?></div>
<div class="divider-dashed"></div>

<!-- ░░ BILL INFO ░░ -->

<div>Bill Time &nbsp; : <?= $saleDate ?></div>
<div>Print Time &nbsp;: <?= $printTime ?></div>

<div class="divider-dashed"></div>

<!-- ░░ CUSTOMER INFO ░░ -->
<div>Customer Mobile : <?= htmlspecialchars($sale['mobile_no'] ?? 'N/A') ?></div>

<div class="divider-dashed"></div>

<!-- ░░ PAYMENT ░░ -->
<div class="info-row">
    <span>Cash Received</span>
    <span>&#8377;<?= number_format($sale['bill_amount'], 2) ?></span>
</div>

<div class="divider-dashed"></div>

<!-- ░░ ITEMS HEADER ░░ -->
<div class="info-row bold small">
    <span style="flex:1">Item</span>
    <span style="width:30px; text-align:center; margin-right:10px ">Qty</span>
    <span style="width:55px; text-align:right">Price</span>
</div>
<div class="divider-dashed"></div>

<!-- ░░ ITEMS ░░ -->
<?php foreach ($items as $item): ?>
<div class="item-block">
    <div class="item-name-row">
        <span class="item-name"><?= htmlspecialchars($item['product_name']) ?></span>
        <span class="Qty" style="display:inline-block; width:28px; text-align:center; margin-right:10px" ><?= htmlspecialchars($item['quantity']) ?></span>
        <span class="item-price">&#8377;<?= number_format($item['total'], 2) ?></span>
    </div>
    <div class="item-meta">&#8377;<?= number_format($item['selling_price'], 2) ?></div>
    <!-- <div class="item-meta">Qty: <?= htmlspecialchars($item['quantity']) ?></div> -->
    <div class="item-meta">Barcode &nbsp;: <?= htmlspecialchars($item['Barcode']) ?></div>
    <div class="item-meta">Assurance : <?= htmlspecialchars($item['assurance']) ?></div>
    <div class="item-meta">Validity : <?= htmlspecialchars($item['validity']) ?> Months</div>

    <div class="item-qty-row">
        <span>HSN/SAC : <?= $item['HSN'] ?></span>
        <?php 
            if ($isGST) {
                echo "<span>GST : {$item['GST']}%</span>";
            }
        ?>
        
    </div>
</div>
<div class="divider-dashed"></div>
<?php endforeach; ?>

<!-- ░░ SUMMARY ░░ -->
<div class="summary-row">
    <span><?= count($items) ?> Item(s) (<?= array_sum(array_column($items, 'quantity')) ?> Qty)</span>
    <span>&#8377;<?= number_format($grandTotal, 2) ?></span>
</div>
<div class="summary-row">
    <span>Discount</span>
    <span>(-) &#8377;<?= number_format($discount, 2) ?></span>
</div>
<div class="summary-row">
    <span>Taxable Amount</span>
    <span>&#8377;<?= number_format($taxableAmount, 2) ?></span>
</div>
<div class="summary-row">
    <span>GST</span>
    <?php 
            if ($isGST) {
                echo "<span>&#8377;<?= number_format($totalGSTAmount, 2) ?></span>";
            }
        ?>
    
</div>

<div class="divider-solid"></div>

<div class="summary-row bold-row">
    <span>TOTAL</span>
    <span>&#8377;<?= number_format($totalAmount, 2) ?></span>
</div>
<!-- <div class="summary-row">
    <span>Billing Type</span>
    <span><?= htmlspecialchars($sale['billing_type']) ?></span>
</div> -->
<div class="summary-row">
    <span>Payment Mode</span>
    <span><?= htmlspecialchars($sale['payment_mode']) ?></span>
</div>

<div class="divider-solid"></div>
<div class="footer">*** Thank You — Visit Again ***</div>

<!-- Print Button -->
<div class="no-print" style="margin-top:10px; text-align:center;">
    <button onclick="window.print()">🖨 Print</button>
</div>

<script>
    window.onload = () => window.print();
</script>

</body>
</html>
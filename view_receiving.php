<?php include 'db_connect.php';

if (isset($_GET['id'])) {
    $receiving_id = $_GET['id'];
    $receiving = $conn->query("SELECT * FROM receiving_list WHERE id=" . $receiving_id)->fetch_assoc();
    $supplier_id = $receiving['supplier_id'];
    $supplier = $conn->query("SELECT supplier_name, address, contact FROM supplier_list WHERE id=" . $supplier_id)->fetch_assoc();
    $inv = $conn->query("SELECT * FROM inventory WHERE type=1 AND form_id=" . $receiving_id);
}

$total_qty = 0;
$total_price = 0;
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4>Receiving Details</h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<p><strong>Supplier Name:</strong> <?php echo $supplier['supplier_name']; ?></p>
						<p><strong>Supplier Address:</strong> <?php echo $supplier['address']; ?></p>
						<p><strong>Contact Number:</strong> <?php echo $supplier['contact']; ?></p>
					</div>
				</div>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="text-center">Product Name</th>
							<th class="text-center">Quantity</th>
							<th class="text-center">Price</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						while ($row = $inv->fetch_assoc()): 
							$product = $conn->query("SELECT name FROM product_list WHERE id=" . $row['product_id'])->fetch_assoc();
							$other_details = json_decode($row['other_details'], true);
							$total_qty += $row['qty'];
							$price = $other_details['price'];
							$total_price += $price * $row['qty'];
						?>
							<tr>
								<td class="text-center"><?php echo $product['name'] ?></td>
								<td class="text-center"><?php echo $row['qty'] ?></td>
								<td class="text-center"><?php echo $price ?></td>
							</tr>
						<?php endwhile; ?>
					</tbody>
					<tfoot>
						<tr>
							<th class="text-right">Total Quantity</th>
							<th class="text-center"><?php echo $total_qty; ?></th>
							<th></th>
						</tr>
						<tr>
							<th class="text-right">Total Price</th>
							<th class="text-center" colspan="2"><?php echo number_format($total_price, 2); ?></th>
						</tr>
						<tr>
							<th class="text-right">VAT (12%)</th>
							<th class="text-center" colspan="2">
								<?php 
								$tax_percentage = $receiving['tax'] * 100; // Get tax percentage from receiving_list
								$tax_amount = $total_price * ($receiving['tax']); // Calculate tax amount
								echo number_format($tax_amount, 2); 
								?>
							</th>
						</tr>
						<tr>
							<th class="text-right">Grand Total</th>
							<th class="text-center" colspan="2"><?php echo number_format($total_price + $tax_amount, 2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div> 
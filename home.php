<style>
   
</style>

<div class="containe-fluid">

	<div class="row">
		<div class="col-lg-12">
			
		</div>
	</div>

	<div class="row mt-3 ml-3 mr-3">
			<div class="col-lg-12">
			<div class="card">
				<div class="card-body">
				<?php echo "Welcome back ".$_SESSION['login_name']."!"  ?>
									
				</div>
				<hr>
				<div class="alert alert-success col-md-4 ml-4">
					<p><b><large>Total Sales Today</large></b></p>
				<hr>
					<p class="text-right"><b><large><?php 
					include 'db_connect.php';
					$sales_today = $conn->query("SELECT SUM(total_amount) as amount FROM sales_list WHERE date(date_updated) = '" . date('Y-m-d') . "'");
					echo $sales_today->num_rows > 0 ? number_format($sales_today->fetch_array()['amount'], 2) : "0.00"; 

					 ?></large></b></p>
				</div>
				<div class="alert alert-info col-md-4 ml-4">
					<p><b><large>Total Expenses</large></b></p>
					<hr>
					<p class="text-right"><b><large><?php
					// Calculate total expenses
					$total_expenses = 0;
					$expenses_query = $conn->query("SELECT other_details FROM inventory WHERE stock_from = 'receiving'");
					while ($row = $expenses_query->fetch_assoc()) {
						$other_details = json_decode($row['other_details'], true);
						if (isset($other_details['price']) && isset($other_details['qty'])) {
							$total_expenses += $other_details['price'] * $other_details['qty'];
						}
					}
					echo number_format($total_expenses, 2);
					?></large></b></p>
				</div>
				<div class="alert alert-warning col-md-4 ml-4">
					<p><b><large>Total Sales</large></b></p>
					<hr>
					<p class="text-right"><b><large><?php
					// Calculate total sales
					$total_sales = 0;
					$sales_query = $conn->query("SELECT other_details FROM inventory WHERE stock_from = 'sales'");
					while ($row = $sales_query->fetch_assoc()) {
						$other_details = json_decode($row['other_details'], true);
						if (isset($other_details['price']) && isset($other_details['qty'])) {
							$total_sales += $other_details['price'] * $other_details['qty'];
						}
					}
					echo number_format($total_sales, 2);
					?></large></b></p>
				</div>
			</div>
			
		</div>
		</div>
	</div>

</div>
<script>
	
</script>
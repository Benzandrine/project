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
					// Calculate total expenses from receiving_list
					$total_expenses = 0;
					$expenses_query = $conn->query("SELECT SUM(total_amount) as total FROM receiving_list");
					if ($expenses_query->num_rows > 0) {
						$total_expenses = $expenses_query->fetch_array()['total'];
					}
					echo number_format($total_expenses, 2);
					?></large></b></p>
				</div>
				<div class="alert alert-warning col-md-4 ml-4">
					<p><b><large>Total Sales</large></b></p>
					<hr>
					<p class="text-right"><b><large><?php
					// Calculate total sales from sales_list
					$total_sales = 0;
					$sales_query = $conn->query("SELECT SUM(total_amount) as total FROM sales_list");
					if ($sales_query->num_rows > 0) {
						$total_sales = $sales_query->fetch_array()['total'];
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
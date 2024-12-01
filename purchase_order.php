<?php include 'db_connect.php' ?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<button class="col-md-2 float-right btn btn-primary btn-sm" id="new_purchase_order"><i class="fa fa-plus"></i> New Purchase Order</button>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<th class="text-center">#</th>
								<th class="text-center">Date</th>
								<th class="text-center">Reference #</th>
								<th class="text-center">Customer</th>
								<th class="text-center">Action</th>
							</thead>
							<tbody>
							<?php 
								$customer = $conn->query("SELECT * FROM customer_list order by name asc");
								while($row=$customer->fetch_assoc()):
									$cus_arr[$row['id']] = $row['name'];
								endwhile;
									$cus_arr[0] = "GUEST";

								$i = 1;
								$sales = $conn->query("SELECT * FROM sales_list  order by date(date_updated) desc");
								while($row=$sales->fetch_assoc()):
							?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class=""><?php echo date("M d, Y",strtotime($row['date_updated'])) ?></td>
									<td class=""><?php echo $row['ref_no'] ?></td>
									<td class=""><?php echo isset($cus_arr[$row['customer_id']])? $cus_arr[$row['customer_id']] :'N/A' ?></td>
									<td class="text-center">
										
										<a class="btn btn-sm btn-primary" href="index.php?page=pos2&id=<?php echo $row['id'] ?>&customer_id=<?php echo $row['customer_id'] ?>">View</a>
										<a class="btn btn-sm btn-danger delete_sales" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
										<a class="btn btn-sm btn-success print_receipt" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><i class="fa fa-print"></i> Print</a>
									</td>
								</tr>
							<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
	$('table').dataTable()
	$('#new_purchase_order').click(function(){
		location.href = "index.php?page=new_po"
	})
	$('.delete_sales').click(function(){
		_conf("Are you sure to delete this data?","delete_sales",[$(this).attr('data-id')])
	})
	function delete_sales($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_sales',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
// Print button functionality without PDF save option
$('.print_receipt').click(function(){
    let salesId = $(this).attr('data-id');

    // Open print window for the receipt
    let newWindow = window.open('print_sales.php?id=' + salesId, '_blank', 'width=700,height=600,scrollbars=yes,resizable=yes');

    // Trigger print dialog once the print page loads
    newWindow.onload = function () {
        newWindow.print(); // Open the print dialog
        setTimeout(() => { newWindow.close(); }, 1500); // Close the window after 1.5 seconds
    };
});

</script>
<?php include 'db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM sales_list where id=".$_GET['id'])->fetch_array();
	foreach($qry as $k => $val){
		$$k = $val;
	}
	$inv = $conn->query("SELECT * FROM inventory where type=2 and form_id=".$_GET['id']);

}

// Fetch supplier name based on supplier_id from URL
$supplier_id = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : '';
$supplier_name = '';
if ($supplier_id) {
    $supplier_query = $conn->query("SELECT supplier_name FROM supplier_list WHERE id = $supplier_id");
    if ($supplier_query->num_rows > 0) {
        $supplier_name = $supplier_query->fetch_assoc()['supplier_name'];
    }
}

// Fetch products for the supplier
$product_options = '';
if ($supplier_id) {
    $product_query = $conn->query("SELECT * FROM product_list WHERE supplier = $supplier_id ORDER BY name ASC");
    while ($row = $product_query->fetch_assoc()) {
        $product_options .= '<option value="' . $row['id'] . '" data-name="' . $row['name'] . '" data-description="' . $row['description'] . '">' . $row['name'] . ' | ' . $row['sku'] . '</option>';
    }
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Get supplier_id from URL and log it to the console
    var supplierId = getUrlParameter('supplier_id');
    if (supplierId) {
        console.log("Supplier ID:", supplierId);
    }
});
</script>
    <!-- Include Select2 CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4>Purchase Order</h4>
			</div>
			<div class="card-body">
				<form action="" id="manage-sales">
					<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
					<input type="hidden" name="ref_no" value="<?php echo isset($ref_no) ? $ref_no : '' ?>">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group col-md-5">
								<label class="control-label">Supplier</label>
								<input type="text" class="form-control" value="<?php echo $supplier_name; ?>" readonly>
							</div>
						</div>
						<hr>
						<div class="row mb-3">
								<div class="col-md-4">
									<label class="control-label">Product</label>
									<select name="" id="product" class="custom-select browser-default select2">
										<option value=""></option>
										<?php echo $product_options; ?>
									</select>
								</div>
								<div class="col-md-2">
									<label class="control-label">Qty</label>
									<input type="number" class="form-control text-right" step="any" id="qty" >
								</div>
								<div class="col-md-3">
									<label class="control-label">&nbsp</label>
									<button class="btn btn-block btn-sm btn-primary" type="button" id="add_list"><i class="fa fa-plus"></i> Add to List</button>
								</div>


						</div>
						<div class="row">
							<table class="table table-bordered" id="list">
								<colgroup>
									<col width="30%">
									<col width="10%">
									<col width="25%">
									<col width="25%">
									<col width="10%">
								</colgroup>
								<thead>
									<tr>
										<th class="text-center">Product</th>
										<th class="text-center">Qty</th>
										<th class="text-center">Price</th>
										<th class="text-center">Amount</th>
										<th class="text-center"></th>
									</tr>
								</thead>
								<tbody>
									<?php 
									if(isset($id)):
									while($row = $inv->fetch_assoc()): 
										foreach(json_decode($row['other_details']) as $k=>$v){
											$row[$k] = $v;
										}
									?>
										<tr class="item-row">
											<td>
												<input type="hidden" name="inv_id[]" value="<?php echo $row['id'] ?>">
												<input type="hidden" name="product_id[]" value="<?php echo $row['product_id'] ?>">
												<p class="pname">Name: <b><?php echo $prod[$row['product_id']]['name'] ?></b></p>
												<p class="pdesc"><small><i>Description: <b><?php echo $prod[$row['product_id']]['description'] ?></b></i></small></p>
											</td>
											<td>
												<input type="number" min="1" step="any" name="qty[]" value="<?php echo $row['qty'] ?>" class="text-right">
											</td>
											<td>
												<input type="hidden" min="1" step="any" name="price[]" value="<?php echo $row['price'] ?>" class="text-right">
												<p class="text-right"><?php echo $row['price'] ?></p>
											</td>
											<td>
												<p class="amount text-right"></p>
											</td>
											<td class="text-center">
												<buttob class="btn btn-sm btn-danger" onclick = "rem_list($(this))"><i class="fa fa-trash"></i></buttob>
											</td>
										</tr>
									<?php endwhile; ?>
									<?php endif; ?>
								</tbody>
								<tfoot>
									<tr>
										<th class="text-right" colspan="3">Total</th>
										<th class="text-right tamount"></th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="row">
							<button class="btn btn-primary btn-sm btn-block float-right " type="button" id="pay">Purchase</button>
						</div>
					</div>
					<div class="modal fade" id="pay_modal" role='dialog'>
					    <div class="modal-dialog modal-md" role="document">
					      <div class="modal-content">
					        <div class="modal-header">
					        <h5 class="modal-title"></h5>
					      </div>
					      <div class="modal-body">
					      	<div class="container-fluid">
					      		<div class="form-group">
					      			<label for="" class="control-label">Total Amount</label>
					      			<input type="text" name="tamount" value="" class="form-control text-right" readonly="">
					      		</div>
					      		<div class="form-group">
					      			<label for="" class="control-label">Amount Tendered</label>
					      			<input type="number" name="amount_tendered" value="0" min="0" class="form-control text-right" >
					      		</div>
					      		<div class="form-group">
					      			<label for="" class="control-label">Change</label>
					      			<input type="number" name="change" value="0" min="0" class="form-control text-right" readonly="">
					      		</div>
					      	</div>
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-primary" id='submit' onclick="$('#manage-sales').submit()">Purchase</button>
					        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					      </div>
					      </div>
					    </div>
					  </div>
				</form>
			</div>
			
		</div>
	</div>
</div>
<div id="tr_clone">
	<table>
	<tr class="item-row">
		<td>
			<input type="hidden" name="inv_id[]" value="">
			<input type="hidden" name="product_id[]" value="">
			<p class="pname">Name: <b>product</b></p>
			<p class="pdesc"><small><i>Description: <b>Description</b></i></small></p>
		</td>
		<td>
			<input type="number" min="1" step="any" name="qty[]" value="" class="text-right">
		</td>
		<td>
			<input type="hidden" min="1" step="any" name="price[]" value="" class="text-right" readonly="">
			<p class="price text-right">0</p>
		</td>
		<td>
			<p class="amount text-right"></p>
		</td>
		<td class="text-center">
			<buttob class="btn btn-sm btn-danger" onclick = "rem_list($(this))"><i class="fa fa-trash"></i></buttob>
		</td>
	</tr>
	</table>
</div>
<style type="text/css">
	#tr_clone{
		display: none;
	}
	td{
		vertical-align: middle;
	}
	td p {
		margin: unset;
	}
	td input[type='number']{
		height: calc(100%);
		width: calc(100%);

	}
	input[type=number]::-webkit-inner-spin-button, 
	input[type=number]::-webkit-outer-spin-button { 
	  -webkit-appearance: none; 
	  margin: 0; 
	}
</style>
<script>
	$('.select2').select2({
	 	placeholder:"Please select here",
	 	width:"100%"
	})

	// Add event listener for supplier dropdown change
	$('[name="customer_id"]').change(function(){
		var supplier_id = $(this).val();
		console.log("Supplier ID selected:", supplier_id);

		// Log the data being sent to the server
		console.log("Data sent to server:", {supplier_id: supplier_id});

		$.ajax({
			url: 'ajax.php?action=get_products_by_supplier',
			method: 'POST',
			data: {supplier_id: supplier_id},
			success: function(resp){
				try {
					resp = JSON.parse(resp);
					if (resp.error) {
						console.error("Error from server:", resp.error);
					} else {
						var productDropdown = $('#product');
						productDropdown.empty(); // Clear existing options
						productDropdown.append('<option value=""></option>'); // Add default empty option
						resp.forEach(function(product){
							productDropdown.append('<option value="'+product.id+'" data-name="'+product.name+'" data-description="'+product.description+'">'+product.name+' | '+product.sku+'</option>');
						});
						productDropdown.select2({
							placeholder: "Please select here",
							width: "100%"
						});
					}
				} catch (e) {
					console.error("Failed to parse JSON response:", e);
				}
			},
			error: function(xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
		});
	});

	$('#pay').click(function(){
		if($("#list .item-row").length <= 0){
			alert_toast("Please insert atleast 1 item first.",'danger');
			end_load();
			return false;
		}
		$('#pay_modal').modal('show')
	})
	$(document).ready(function(){
		if('<?php echo isset($id) ?>' == 1){
			$('[name="supplier_id"]').val('<?php echo isset($supplier_id) ? $supplier_id :'' ?>').select2({
				placeholder:"Please select here",
	 			width:"100%"
			})
			calculate_total()
		}
	})
	function rem_list(_this){
		_this.closest('tr').remove()
	}
	$('#add_list').click(function(){
		// alert("TEST");
		// return false;

		var tr = $('#tr_clone tr.item-row').clone();
		var product = $('#product').val(),
			qty = $('#qty').val(),
			price = $('#price').val();
			if($('#list').find('tr[data-id="'+product+'"]').length > 0){
				alert_toast("Product already on the list",'danger')
				return false;
			}

			if(product == '' || qty == ''){
				alert_toast("Please complete the fields first",'danger')
				return false;
			}
			$.ajax({
				url:'ajax.php?action=chk_prod_availability',
				method:'POST',
				data:{id:product},
				success:function(resp){
					resp = JSON.parse(resp);
					if(resp.available >= qty){
						tr.attr('data-id',product)
						tr.find('.pname b').html($("#product option[value='"+product+"']").attr('data-name'))
						tr.find('.pdesc b').html($("#product option[value='"+product+"']").attr('data-description'))
						tr.find('.price').html(resp.price)
						tr.find('[name="product_id[]"]').val(product)
						tr.find('[name="qty[]"]').val(qty)
						tr.find('[name="price[]"]').val(resp.price)
						var amount = parseFloat(price) * parseFloat(qty);
						tr.find('.amount').html(parseFloat(amount).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
						$('#list tbody').append(tr)
						calculate_total()
						$('[name="qty[]"],[name="price[]"]').keyup(function(){
							calculate_total()
						})
						 $('#product').val('').select2({
						 	placeholder:"Please select here",
					 		width:"100%"
						 })
							$('#qty').val('')
							$('#price').val('')
					}else{
						alert_toast("Product quantity is greater than available stock.",'danger')
					}
				}
			})
		
	})
	function calculate_total(){
		var total = 0;
		$('#list tbody').find('.item-row').each(function(){
			var _this = $(this).closest('tr')
		var amount = parseFloat(_this.find('[name="qty[]"]').val()) * parseFloat(_this.find('[name="price[]"]').val());
		amount = amount > 0 ? amount :0;
		_this.find('p.amount').html(parseFloat(amount).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
		total+=parseFloat(amount);
		})
		$('[name="tamount"]').val(total)
		$('#list .tamount').html(parseFloat(total).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
	}
	$('[name="amount_tendered"]').keyup(function(){
		var tendered = $(this).val();
		var tamount = $('[name="tamount"]').val();
		$('[name="change"]').val(parseFloat(tendered) - parseFloat(tamount))

	})
	$('#manage-sales').submit(function(e){
		e.preventDefault()
		start_load()
		if($("#list .item-row").length <= 0){
			alert_toast("Please insert atleast 1 item first.",'danger');
			end_load();
			return false;
		}
		$.ajax({
			url:'ajax.php?action=save_sales',
		    method: 'POST',
		    data: $(this).serialize(),
			success:function(resp){
				if(resp > 0){
					end_load()
					alert_toast("Data successfully submitted",'success')
					uni_modal('Print',"print_sales.php?id="+resp)
					$('#uni_modal').modal({backdrop:'static',keyboard:false})

				}
				
			}
		})
	})
</script>
<!-- Include Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
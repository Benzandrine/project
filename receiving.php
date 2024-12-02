<?php include 'db_connect.php' ?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<button class="col-md-2 float-right btn btn-primary btn-sm" id="new_receiving"><i class="fa fa-plus"></i> New Receiving</button>
		</div>
		<!-- Supplier List Modal -->
		<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="supplierModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="supplierModalLabel">Select Supplier</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<table class="table table-bordered">
							<thead>
								<th class="text-center">ID</th>
								<th class="text-center">Supplier Name</th>
								<th class="text-center">Select</th>
							</thead>
							<tbody>
							<?php 
								$suppliers = $conn->query("SELECT * FROM supplier_list order by supplier_name asc");
								while($row=$suppliers->fetch_assoc()):
							?>
								<tr>
									<td class="text-center"><?php echo $row['id'] ?></td>
									<td class=""><?php echo $row['supplier_name'] ?></td>
									<td class="text-center">
										<button class="btn btn-sm btn-success select_supplier" data-id="<?php echo $row['id'] ?>">Select</button>
									</td>
								</tr>
							<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- End of Supplier List Modal -->
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<th class="text-center">#</th>
								<th class="text-center">Date</th>
								<th class="text-center">Reference #</th>
								<th class="text-center">Supplier</th>
								<th class="text-center">Action</th>
							</thead>
							<tbody>
							<?php 
								$supplier = $conn->query("SELECT * FROM supplier_list order by supplier_name asc");
								while($row=$supplier->fetch_assoc()):
									$sup_arr[$row['id']] = $row['supplier_name'];
								endwhile;
								$i = 1;
								$receiving = $conn->query("SELECT * FROM receiving_list r order by date(date_added) desc");
								while($row=$receiving->fetch_assoc()):
							?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class=""><?php echo date("M d, Y",strtotime($row['date_added'])) ?></td>
									<td class=""><?php echo $row['ref_no'] ?></td>
									<td class=""><?php echo isset($sup_arr[$row['supplier_id']])? $sup_arr[$row['supplier_id']] :'N/A' ?></td>
									<td class="text-center">
										<a class="btn btn-sm btn-danger delete_receiving" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
										<a class="btn btn-sm btn-info" href="index.php?page=view_receiving&id=<?php echo $row['id'] ?>">View</a>
										<a class="btn btn-sm btn-success preview_pdf" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>"><i class="fa fa-print"></i> Print</a>
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

<!-- Modal for Preview -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfPreviewModalLabel">PDF Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="pdfPreviewBody">
                <!-- The preview content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadPdfBtn">Download PDF</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
    // Preview the PDF when the preview button is clicked
    $('.preview_pdf').click(function() {
        let receivingId = $(this).attr('data-id');

        // Fetch the content to be previewed (you can use AJAX to fetch the full HTML content of the receipt)
        $.ajax({
            url: 'generate_pdf.php', // Assuming this file generates the PDF content
            method: 'GET',
            data: { id: receivingId },
            success: function(response) {
                // Show the content in the modal
                $('#pdfPreviewBody').html(response);

                // Open the modal
                $('#pdfPreviewModal').modal('show');
                
                // Clear any previously generated pdf (this is just a precaution, you don't need to generate a pdf yet)
                $('#downloadPdfBtn').off('click'); // Unbind any previous click event
            }
        });
    });

    // Download the PDF when the "Download PDF" button is clicked
    $('#downloadPdfBtn').click(function() {
        // Re-fetch the content (since we are about to generate the PDF)
        let receivingId = $('.preview_pdf').attr('data-id');

        $.ajax({
            url: 'generate_pdf.php', // Assuming this file generates the PDF content
            method: 'GET',
            data: { id: receivingId },
            success: function(response) {
                // Initialize html2pdf for the modal content preview
                var element = document.getElementById('pdfPreviewBody');
                var opt = {
                    margin:       1,
                    filename:     'preview.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { dpi: 192, letterRendering: true },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };
                var pdf = new html2pdf(element, opt);

                // Download the PDF
                pdf.save();

                // Close the modal
                $('#pdfPreviewModal').modal('hide');
            }
        });
    });

    $('table').dataTable()
    $('#new_receiving').click(function(){
        $('#supplierModal').modal('show'); // Show the modal
    })
    $('.select_supplier').click(function(){
        let supplierId = $(this).attr('data-id');
        location.href = "index.php?page=manage_receiving&supplier_id=" + supplierId; // Redirect with supplier ID
    })
    $('.delete_receiving').click(function(){
        _conf("Are you sure to delete this data?","delete_receiving",[$(this).attr('data-id')])
    })
    function delete_receiving($id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_receiving',
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
</script>
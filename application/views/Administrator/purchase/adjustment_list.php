<div class="row">
	<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;">
		<form onsubmit="searchforRecord(event)">
			<div class="form-group">
				<label class="col-xs-2 control-label no-padding-right" for="searchtype"> Search Type </label>
				<div class="col-xs-4">
					<select class="chosen-select form-control" name="prod_id" id="prod_id" data-placeholder="Choose a Product...">
						<option value=""> </option>
						<option value="All"> All </option>
						<?php foreach ($products as $product) { ?>
							<option value="<?php echo $product->Product_SlNo; ?>"><?php echo $product->Product_Name; ?> - <?php echo $product->Product_Code; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-1">
					<input type="submit" class="btn btn-primary" value="Show Report" style="margin-top:0px;border:0px;height:28px;">
				</div>
			</div>
		</form>
	</div>

	<div class="col-xs-12 col-md-12 col-lg-12" style="margin-top:15px;display: none;" id="result">
		<table class="table table-bordered">
			<thead>
				<tr style="background:#7664a9;">
					<th style="text-align:center;color:white;">SlNo.</th>
					<th style="text-align:center;color:white;">Product Name</th>
					<th style="text-align:center;color:white;">Adjustment Quantity</th>
					<th style="text-align:center;color:white;">Unit</th>
					<th style="text-align:center;color:white;">Adjustment Type</th>
					<th style="text-align:center;color:white;">Description</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<th style='text-align:center;' colspan="6">Not Found Data</th>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	function searchforRecord(event) {
		event.preventDefault();
		let formdata = new FormData(event.target)
		$.ajax({
			url: "<?php echo base_url(); ?>SelectAdjustmentProduct",
			method: "POST",
			data: formdata,
			dataType: "JSON",
			processData: false,
			contentType: false,
			beforeSend: () => {
				$("#result table tbody").html("");
			},
			success: function(data) {
				if (data.length > 0) {
					$.each(data, (index, value) => {
						let row = `
							<tr align="center">
								<td>${++index}</td>
								<td>${value.productDetail}</td>
								<td>${value.AdjustmentDetails_AdjustmentQuantity}</td>
								<td>${value.Unit_Name}</td>
								<td>${value.adjustment_type}</td>
								<td>${value.Adjustment_Description}</td>
							</tr>
						`;
						$("#result table tbody").append(row);
					})
				} else {
					$("#result table tbody").html(`<tr><th style='text-align:center;' colspan="6">Not Found Data</th></tr>`);
				}

				$("#result").css({display: 'block'});
			}
		});
	}
</script>
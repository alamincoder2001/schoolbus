<style>
	.v-select {
		margin-bottom: 5px;
	}

	.v-select.open .dropdown-toggle {
		border-bottom: 1px solid #ccc;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	.v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	}

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		position: absolute;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}

	#customerPayment label {
		font-size: 13px;
	}

	#customerPayment select {
		border-radius: 3px;
		padding: 0;
	}

	#customerPayment .add-button {
		padding: 2.5px;
		width: 28px;
		background-color: #298db4;
		display: block;
		text-align: center;
		color: white;
	}

	#customerPayment .add-button:hover {
		background-color: #41add6;
		color: white;
	}
</style>
<div id="customerPayment">
	<div class="row" style="border-bottom: 1px solid #ccc;padding-bottom: 15px;margin: 15px 0;">
		<form @submit.prevent="saveCustomerPayment">
			<div class="col-md-6 col-md-offset-3">
				<div class="form-group">
					<label class="col-md-3 control-label">Select Excel file</label>
					<div class="col-md-7">
						<input type="file" style="padding: 3px;height: 30px;" class="form-control" v-model="excelfile" @change='fileChange(event)' required>
					</div>
					<div class="col-md-2">
						<input type="submit" class="btn btn-success btn-sm" style="padding: 2px 23px;" value="Save">
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="row">
		<div class="col-sm-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
		<div class="col-md-12">
			<div class="table-responsive">
				<datatable :columns="columns" :data="payments" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.CPayment_invoice }}</td>
							<td>{{ row.CPayment_date }}</td>
							<td>{{ row.Customer_Name }}</td>
							<td>{{ row.transaction_type }}</td>
							<td>{{ row.payment_by }}</td>
							<td>{{ row.CPayment_amount }}</td>
							<td>{{ row.CPayment_notes }}</td>
							<td>{{ row.CPayment_Addby }}</td>
							<td>
								<button type="button" class="button edit" @click="window.location = `/paymentAndReport/${row.CPayment_id}`">
									<i class="fa fa-file-o"></i>
								</button>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<button type="button" class="button edit" @click="editPayment(row)">
										<i class="fa fa-pencil"></i>
									</button>
									<button type="button" class="button" @click="deletePayment(row.CPayment_id)">
										<i class="fa fa-trash"></i>
									</button>
								<?php } ?>
							</td>
						</tr>
					</template>
				</datatable>
				<datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.3/xlsx.full.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#customerPayment',
		data() {
			return {
				excelfile: '',
				allData: [],
				payments: [],

				columns: [{
						label: 'Transaction Id',
						field: 'CPayment_invoice',
						align: 'center'
					},
					{
						label: 'Date',
						field: 'CPayment_date',
						align: 'center'
					},
					{
						label: 'Customer',
						field: 'Customer_Name',
						align: 'center'
					},
					{
						label: 'Transaction Type',
						field: 'transaction_type',
						align: 'center'
					},
					{
						label: 'Payment by',
						field: 'payment_by',
						align: 'center'
					},
					{
						label: 'Amount',
						field: 'CPayment_amount',
						align: 'center'
					},
					{
						label: 'Description',
						field: 'CPayment_notes',
						align: 'center'
					},
					{
						label: 'Saved By',
						field: 'CPayment_Addby',
						align: 'center'
					},
					{
						label: 'Action',
						align: 'center',
						filterable: false
					}
				],
				page: 1,
				per_page: 10,
				filter: ''
			}
		},

		methods: {

			async fileChange(evt) {
				// function importFile(evt) {
				var f = evt.target.files[0];

				if (f) {
					var r = new FileReader();
					r.onload = e => {
						var contents = processExcel(e.target.result);
						// console.log(contents)

						axios.post('/upload_customer_payment_excel', contents).then(res => {
							console.log(res);
						})

						// contents.forEach(ele => {
						// 	console.log(ele);
						// })

					}
					r.readAsBinaryString(f);
				} else {
					console.log("Failed to load file");
				}

				function processExcel(data) {
					var workbook = XLSX.read(data, {
						type: 'binary'
					});

					var firstSheet = workbook.SheetNames[0];
					var data = to_json(workbook);
					return data
				};

				function to_json(workbook) {
					var result = {};
					workbook.SheetNames.forEach(function(sheetName) {
						var roa = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {
							header: 1
						});
						if (roa.length) result[sheetName] = roa;
					});
					return JSON.stringify(result, 2, 2);
				};


			}




			// async fileChange(event) {

			// 	function ExcelToJSON() {
			// 		this.parseExcel = function(file) {
			// 			var reader = new FileReader();

			// 			reader.onload = function(e) {
			// 				var data = e.target.result;
			// 				var workbook = XLSX.read(data, {
			// 					type: 'binary'
			// 				});

			// 				workbook.SheetNames.forEach(function(sheetName) {
			// 					// Here is your object
			// 					var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
			// 					var json_object = JSON.stringify(XL_row_object);
			// 					console.log(json_object);
			// 				})
			// 			};

			// 			reader.onerror = function(ex) {
			// 				console.log(ex);
			// 			};

			// 			reader.readAsBinaryString(file);
			// 		};
			// 	}

			// 	var files = event.target.files; // FileList object
			// 	var xl2json = new ExcelToJSON();
			// 	this.payments = xl2json.parseExcel(files[0]);
			// },
		}
	})
</script>
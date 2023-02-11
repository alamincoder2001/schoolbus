<style>
    .v-select {
        margin-top: -2.5px;
        float: right;
        min-width: 180px;
        margin-left: 5px;
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

    #searchForm select {
        padding: 0;
        border-radius: 4px;
    }

    #searchForm .form-group {
        margin-right: 5px;
    }

    #searchForm * {
        font-size: 13px;
    }

    .record-table {
        width: 100%;
        border-collapse: collapse;
    }

    .record-table thead {
        background-color: #0097df;
        color: white;
    }

    .record-table th,
    .record-table td {
        padding: 3px;
        border: 1px solid #454545;
    }

    .record-table th {
        text-align: center;
    }
</style>
<div id="salesRecord">
    <div class="row" style="border-bottom: 1px solid #ccc;padding: 3px 0;">
        <div class="col-md-12">
            <form class="form-inline" id="searchForm" @submit.prevent="getSearchResult">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" v-model="searchType" @change="onChangeSearchType">
                        <option value="pending_report">Pending Report</option>
                        <option value="pending_order">Pending Order Item</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="date" class="form-control" v-model="dateFrom">
                </div>

                <div class="form-group">
                    <input type="date" class="form-control" v-model="dateTo">
                </div>

                <div class="form-group" style="margin-top: -5px;">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
    </div>

    <div class="row" style="margin-top:15px;display:none;" v-bind:style="{display: sales.length > 0 ? '' : 'none'}">
        <div class="col-md-12" style="margin-bottom: 10px;display:flex;justify-content: space-between;">
            <a href="" @click.prevent="print" style="margin: 0;"><i class="fa fa-print"></i> Print</a>
            <a v-if="searchType == 'pending_report'" :href="`${link}${dateFrom}/${dateTo}`" style="margin: 0px;background: #ff9b1fd9;padding: 3px;color: white;text-decoration: none;">Export Excel</a>
            <a v-else :href="`${'export_sale_pendingorderitem/'}${dateFrom}/${dateTo}`" style="margin: 0px;background: #ff9b1fd9;padding: 3px;color: white;text-decoration: none;">Export Excel</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">

                <table class="record-table" v-if="searchType != 'pending_order'" style="display:none" v-bind:style="{display: (searchTypesForRecord.includes(searchType)) ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Employee Name</th>
                            <th>Saved By</th>
                            <th>Sub Total</th>
                            <th>VAT</th>
                            <th>Discount</th>
                            <th>Transport Cost</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="sale in sales">
                            <tr :style="{background: sale.Status == 'p' ? 'rgb(255 251 220)' : ''}">
                                <td>{{ sale.SaleMaster_InvoiceNo }}</td>
                                <td>{{ sale.SaleMaster_SaleDate }}</td>
                                <td>{{ sale.Customer_Name }}</td>
                                <td>{{ sale.Employee_Name }}</td>
                                <td>{{ sale.AddBy }}</td>
                                <td style="text-align:right;">{{ sale.SaleMaster_SubTotalAmount }}</td>
                                <td style="text-align:right;">{{ sale.SaleMaster_TaxAmount }}</td>
                                <td style="text-align:right;">{{ sale.SaleMaster_TotalDiscountAmount }}</td>
                                <td style="text-align:right;">{{ sale.SaleMaster_Freight }}</td>
                                <td style="text-align:right;">{{ sale.SaleMaster_TotalSaleAmount }}</td>
                                <td style="text-align:right;">{{ sale.SaleMaster_PaidAmount }}</td>
                                <td style="text-align:right;">{{ sale.SaleMaster_DueAmount }}</td>
                                <td style="text-align:left;">{{ sale.SaleMaster_Description }}</td>
                                <td style="text-align:center;">
                                    <a href="" title="Sale Invoice" v-bind:href="`/sale_invoice_print/${sale.SaleMaster_SlNo}`" target="_blank"><i class="fa fa-file"></i></a>
                                    <a href="" title="Chalan" v-bind:href="`/chalan/${sale.SaleMaster_SlNo}`" target="_blank"><i class="fa fa-file-o"></i></a>
                                    <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                        <a v-if="sale.Status == 'p'" href="" title="Approve" @click.prevent="approveItem(sale.SaleMaster_SlNo)"><i style="font-size:18px" class="fa fa-check-square"></i></a>
                                        <a href="javascript:" title="Edit Sale" @click="checkReturnAndEdit(sale)"><i class="fa fa-edit"></i></a>
                                        <a href="" title="Delete Sale" @click.prevent="deleteSale(sale.SaleMaster_SlNo)"><i class="fa fa-trash"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:bold;">
                            <td colspan="5" style="text-align:right;">Total</td>
                            <td style="text-align:right;">
                                {{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_SubTotalAmount)}, 0) }}
                            </td>
                            <td style="text-align:right;">
                                {{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_TaxAmount)}, 0) }}
                            </td>
                            <td style="text-align:right;">
                                {{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_TotalDiscountAmount)}, 0) }}
                            </td>
                            <td style="text-align:right;">
                                {{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_Freight)}, 0) }}
                            </td>
                            <td style="text-align:right;">
                                {{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_TotalSaleAmount)}, 0) }}
                            </td>
                            <td style="text-align:right;">
                                {{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_PaidAmount)}, 0) }}
                            </td>
                            <td style="text-align:right;">
                                {{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_DueAmount)}, 0) }}
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <table class="record-table" v-if="searchType == 'pending_order'" style="display:none" v-bind:style="{display: (searchTypesForRecord.includes(searchType)) ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>SL No.</th>
                            <th>Item Name</th>
                            <th style="width: 15%;">Sum of total Pending Qty</th>
                            <th style="width: 15%;">Present Stock Qty</th>
                            <th style="width: 15%;">Purchase Required Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="sale in sales">
                            <tr style="background: rgb(255 251 220);font-weight:bold;">
                                <td></td>
                                <td colspan="4">{{ sale.ProductCategory_Name }}</td>
                            </tr>
                            <tr v-for="(item, index) in sale.products" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.Product_Name }}</td>
                                <td style="text-align: center;">{{ item.SaleDetails_TotalQuantity }}</td>
                                <td style="text-align: center;">{{ item.stock }}</td>
                                <td style="text-align: center;">{{item.stock - item.SaleDetails_TotalQuantity}}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#salesRecord',
        data() {
            return {
                link: 'export_sale_pendingrecord/',
                searchType: 'pending_report',
                dateFrom: moment().format('YYYY-MM-DD'),
                dateTo: moment().format('YYYY-MM-DD'),
                sales: [],
                searchTypesForRecord: ['pending_report', 'pending_order']
            }
        },
        methods: {
            checkReturnAndEdit(sale) {
                axios.get('/check_sale_return/' + sale.SaleMaster_InvoiceNo).then(res => {
                    if (res.data.found) {
                        alert('Unable to edit. Sale return found!');
                    } else {
                        if (sale.is_service == 'true') {
                            location.replace('/sales/service/' + sale.SaleMaster_SlNo);
                        } else {
                            location.replace('/sales/product/' + sale.SaleMaster_SlNo);
                        }
                    }
                })
            },

            onChangeSearchType() {
                this.sales = [];
            },

            getSearchResult() {
                this.getSalesRecord();
            },


            getSalesRecord() {
                let filter = {
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
                let url;
                if (this.searchType == "pending_report") {
                    url = '/get_sales';
                } else {
                    url = '/get_pending_order';
                }
                axios.post(url, filter)
                    .then(res => {
                        if (this.searchType == "pending_report") {
                            this.sales = res.data.sales.filter((p) => {
                                return p.Status == 'p';
                            });
                        } else {
                            let sales = res.data;
                            sales = _.chain(sales)
                                .groupBy('ProductCategory_Name')
                                .map(sale => {
                                    return {
                                        ProductCategory_Name: sale[0].ProductCategory_Name,
                                        products: _.chain(sale)
                                            .groupBy('Product_IDNo')
                                            .map(product => {
                                                return {
                                                    Product_Code             : product[0].Product_Code,
                                                    Product_Name             : product[0].Product_Name,
                                                    Unit_Name                : product[0].Unit_Name,
                                                    SaleDetails_TotalQuantity: _.sumBy(product, item => Number(item
                                                        .SaleDetails_TotalQuantity)),
                                                    stock                : product[0].stock,
                                                    purchase_required_qty: (product[0].stock - _.sumBy(product, item => Number(item
                                                        .SaleDetails_TotalQuantity)))
                                                }
                                            })
                                            .value()
                                    }
                                })
                                .value();
                            this.sales = sales;
                        }
                    })
            },
            deleteSale(saleId) {
                let deleteConf = confirm('Are you sure?');
                if (deleteConf == false) {
                    return;
                }
                axios.post('/delete_sales', {
                        saleId: saleId
                    })
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getSalesRecord();
                        }
                    })
            },
            approveItem(saleId) {
                let deleteConf = confirm('Are you sure to approve?');
                if (deleteConf == false) {
                    return;
                }
                axios.post('/approve_sales', {
                        saleId: saleId
                    })
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getSalesRecord();
                        }
                    })
            },
            async print() {
                let dateText = '';
                if (this.dateFrom != '' && this.dateTo != '') {
                    dateText =
                        `Statement from <strong>${this.dateFrom}</strong> to <strong>${this.dateTo}</strong>`;
                }
                let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>${this.searchType == "pending_report" ? "Pending Order Report":"Pending Order Item"}</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var reportWindow = window.open('', '',
                    `height=${screen.height}, width=${screen.width}`);
                reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                reportWindow.document.head.innerHTML += `
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
					</style>
				`;
                reportWindow.document.body.innerHTML += reportContent;

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>
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

    select {
        margin-bottom: 5px;
    }
</style>
<div id="adjustments">
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-8">
            <form class="form-horizontal" @submit.prevent="addAdjustment">
                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"> Code </label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-sm-5">
                        <input type="text" placeholder="Code" class="form-control" v-model="adjustment.Adjustment_InvoiceNo" required readonly />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"> Date </label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-sm-5">
                        <input type="date" placeholder="Date" class="form-control" v-model="adjustment.Adjustment_Date" required />
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label col-md-6 no-padding-right">Supplier Name</label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-md-5">
                        <v-select v-bind:options="suppliers" v-model="selectedSupplier" @input="onChangeSupplier" label="display_name"></v-select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"> Product </label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-sm-5">
                        <v-select v-bind:options="products" label="display_text" v-model="selectedProduct" placeholder="Select Product" v-on:input="productOnChange"></v-select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"> Adjustment Quantity </label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-sm-5">
                        <input type="number" placeholder="Quantity" class="form-control" v-model="adjustment.AdjustmentDetails_AdjustmentQuantity" required v-on:input="calculateTotal" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"> Adjustment Rate </label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-sm-5">
                        <input type="number" step="0.01" placeholder="Rate" class="form-control" v-model="adjustment.adjustment_rate" required v-on:input="calculateTotal" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"> Adjustment Amount </label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-sm-5">
                        <input type="number" placeholder="Amount" class="form-control" v-model="adjustment.adjustment_amount" required disabled />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"> Adjustment Type </label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-sm-5">
                        <select v-model="adjustment.adjustment_type" required style="width: 100%; border-radius: 3px;">
                            <option value="Less_Stock">Less Stock</option>
                            <option value="Add_Stock">Add Stock</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"> Description </label>
                    <label class="col-sm-1 control-label no-padding-right">:</label>
                    <div class="col-sm-5">
                        <textarea class="form-control" placeholder="Description" v-model="adjustment.Adjustment_Description"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label no-padding-right"></label>
                    <label class="col-sm-1 control-label no-padding-right"></label>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-sm btn-success">
                            Submit
                            <i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <h1 style="display: none;" v-bind:style="{display: productStock !== '' ? '' : 'none'}">Stock : {{productStock}}</h1>
        </div>
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
                <datatable :columns="columns" :data="adjustments" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.Adjustment_InvoiceNo }}</td>
                            <td>{{ row.Adjustment_Date }}</td>
                            <td>{{ row.Product_Code }}</td>
                            <td>{{ row.Product_Name }}</td>
                            <td>{{ row.adjustment_type }}</td>
                            <td>{{ row.AdjustmentDetails_AdjustmentQuantity }}</td>
                            <td>{{ row.adjustment_rate | decimal }}</td>
                            <td>{{ row.adjustment_amount | decimal }}</td>
                            <td>{{ row.Adjustment_Description }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <!-- <button type="button" class="button edit" @click="editAdjustment(row)">
                                    <i class="fa fa-pencil"></i>
                                </button> -->
                                    <button type="button" class="button" @click="deleteAdjustment(row)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#adjustments',
        data() {
            return {
                adjustment: {
                    Adjustment_SlNo: 0,
                    Adjustment_InvoiceNo: '<?php echo $adjustmentCode; ?>',
                    Adjustment_Date: moment().format('YYYY-MM-DD'),
                    Adjustment_Description: '',
                    Product_SlNo: '',
                    AdjustmentDetails_AdjustmentQuantity: '',
                    adjustment_type: 'Less_Stock',
                    adjustment_rate: '',
                    adjustment_amount: 0,
                },
                suppliers: [],
                selectedSupplier: {
                    Supplier_SlNo: '',
                    Supplier_Code: '',
                    Supplier_Name: '',
                    display_name: 'Select Supplier',
                    Supplier_Mobile: '',
                    Supplier_Address: '',
                    Supplier_Type: ''
                },
                products: [],
                selectedProduct: null,

                productStock: '',
                adjustments: [],
                columns: [{
                        label: 'Code',
                        field: 'Adjustment_InvoiceNo',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Date',
                        field: 'Adjustment_Date',
                        align: 'center'
                    },
                    {
                        label: 'Product Code',
                        field: 'Product_Code',
                        align: 'center'
                    },
                    {
                        label: 'Product Name',
                        field: 'Product_Name',
                        align: 'center'
                    },
                    {
                        label: 'Adjustment Type',
                        field: 'adjustment_type',
                        align: 'center'
                    },
                    {
                        label: 'Quantity',
                        field: 'AdjustmentDetails_AdjustmentQuantity',
                        align: 'center'
                    },
                    {
                        label: 'Adjustment Rate',
                        field: 'adjustment_rate',
                        align: 'center'
                    },
                    {
                        label: 'Adjustment Amount',
                        field: 'adjustment_amount',
                        align: 'center'
                    },
                    {
                        label: 'Description',
                        field: 'Adjustment_Description',
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
        filters: {
            decimal(value) {
                return value == null ? '0.00' : parseFloat(value).toFixed(2);
            }
        },
        created() {
            this.getSuppliers();
            // this.getProducts();
            this.getAdjustments();
        },
        methods: {

            async productOnChange() {
                if ((this.selectedProduct.Product_SlNo != '' || this.selectedProduct.Product_SlNo != 0)) {
                    this.adjustment.adjustment_rate = this.selectedProduct.Product_Purchase_Rate;

                    let adjustment_amount = parseFloat(this.adjustment.adjustment_rate) * parseFloat(this.adjustment.AdjustmentDetails_AdjustmentQuantity);
                    this.adjustment.adjustment_amount = isNaN(adjustment_amount) ? 0 : adjustment_amount;

                    this.productStock = await axios.post('/get_product_stock', {
                        productId: this.selectedProduct.Product_SlNo
                    }).then(res => {
                        return res.data;
                    })
                }
            },
            getSuppliers() {
                axios.get('/get_suppliers').then(res => {
                    this.suppliers = res.data;
                })
            },
            getProducts() {
                let filter = {
                    supplierId: this.selectedSupplier.Supplier_SlNo,
                    isService: 'false'
                }
                axios.post('/get_products', filter).then(res => {
                    this.products = res.data;
                })
            },
            onChangeSupplier() {
                if (this.selectedSupplier.Supplier_SlNo != '') {
                    this.getProducts();

                }
            },
            addAdjustment() {
                if (this.selectedSupplier.Supplier_SlNo == '') {
                    alert('Select Supplier');
                    return;
                }
                if (this.selectedProduct == null) {
                    alert('Select product');
                    return;
                }
                if (this.adjustment.AdjustmentDetails_AdjustmentQuantity > this.productStock) {
                    alert('Stock unavailable');
                    return;
                }
                this.adjustment.Product_SlNo = this.selectedProduct.Product_SlNo;
                this.adjustment.supplier_id = this.selectedSupplier.Supplier_SlNo;

                let url = '/add_adjustment';
                if (this.adjustment.Adjustment_SlNo != 0) {
                    url = '/update_adjustment'
                }
                axios.post(url, this.adjustment).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.resetForm();
                        this.adjustment.Adjustment_InvoiceNo = r.newCode;
                        this.getAdjustments();
                    }
                })
            },

            editAdjustment(adjustment) {
                let keys = Object.keys(this.adjustment);
                keys.forEach(key => this.adjustment[key] = adjustment[key]);

                this.selectedProduct = {
                    Product_SlNo: adjustment.Product_SlNo,
                    display_text: `${adjustment.Product_Name} - ${adjustment.Product_Code}`,
                    Product_Purchase_Rate: adjustment.adjustment_rate
                }

            },

            calculateTotal() {
                let adjustment_amount = parseFloat(this.adjustment.adjustment_rate) * parseFloat(this.adjustment.AdjustmentDetails_AdjustmentQuantity);
                this.adjustment.adjustment_amount = isNaN(adjustment_amount) ? 0 : adjustment_amount;
            },

            deleteAdjustment(row) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }
                let formdata = {
                    adjustmentId: row.Adjustment_SlNo,
                    adjustment_type: row.adjustment_type
                }
                axios.post('/delete_adjustment', formdata).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getAdjustments();
                    }
                })
            },

            getAdjustments() {
                axios.get('/get_adjustments').then(res => {
                    this.adjustments = res.data;
                })
            },

            resetForm() {
                this.adjustment.Adjustment_SlNo = '';
                this.adjustment.Adjustment_Description = '';
                this.adjustment.Product_SlNo = '';
                this.adjustment.AdjustmentDetails_AdjustmentQuantity = '';
                this.adjustment.adjustment_rate = '';
                this.adjustment.adjustment_type = '';
                this.adjustment.adjustment_amount = 0;
                this.selectedProduct = null;
                this.productStock = '';
            }
        }
    })
</script>
<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
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
</style>
<div id="stock">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">

            <div class="form-group" style="margin-top:10px;">
                <label class="col-sm-1 control-label no-padding-right"> Select Type </label>
                <div class="col-sm-2">
                    <v-select v-bind:options="searchTypes" v-model="selectedSearchType" label="text" v-on:input="onChangeSearchType"></v-select>
                </div>
            </div>
            <div class="form-group" style="margin-top:10px;">
                <label class="col-sm-1 no-padding-right" style="width: 65px;"> Supplier </label>
                <div class="col-sm-2">
                    <v-select v-bind:options="suppliers" v-model="selectedSupplier" v-on:input="onChangeSupplier" label="display_name" placeholder="Select Supplier">
                    </v-select>
                </div>
            </div>

            <div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'category'">
                <div class="col-sm-2">
                    <v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name">
                    </v-select>
                </div>
            </div>


            <!-- <div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'supplier'">
                <div class="col-sm-2" style="margin-left:15px;">
                    <v-select v-bind:options="suppliers" v-model="selectedSupplier" label="Supplier_Name">
                    </v-select>
                </div>
            </div> -->

            <div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'product'">
                <label class="col-sm-1 control-label no-padding-right" style="width: 65px;"> Product </label>
                <div class="col-sm-2">
                    <v-select v-bind:options="products" v-model="selectedProduct" label="display_text"></v-select>
                </div>
            </div>

            <div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'brand'">
                <div class="col-sm-2">
                    <v-select v-bind:options="brands" v-model="selectedBrand" label="brand_name"></v-select>
                </div>
            </div>

            <div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value != 'current'">
                <div class="col-sm-1 no-padding">
                    <input type="date" style="width: 110px;" class="form-control" v-model="date">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2">
                    <input type="button" class="btn btn-primary" value="Show Report" v-on:click="getStock" style="border: 0px; margin: 0 0 0 10px;padding: 3px 7px;border-radius: 4px;">
                </div>
            </div>
        </div>
    </div>
    <div class="row" v-if="searchType != null" style="display:none" v-bind:style="{display: searchType == null ? 'none' : ''}">
        <div class="col-md-12" style="margin-bottom: 10px;display:flex;justify-content: space-between;">
            <a href="" v-on:click.prevent="print" style="margin: 0;"><i class="fa fa-print"></i> Print</a>
            <a v-if="searchType == 'current' || searchType == 'total' || searchType == ''" :href="`export_stock_record/${date}/${searchType == ''?'total':searchType}`" style="margin: 0px;background: #ff9b1fd9;padding: 3px;color: white;text-decoration: none;">Export Excel</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive" id="stockContent">

                <table class="table table-bordered" v-if="searchType == 'current'" style="display:none" v-bind:style="{display: searchType == 'current' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Product Id</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Current Quantity</th>
                            <th>Rate</th>
                            <th>Stock Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in stock">
                            <td>{{ product.Product_Code }}</td>
                            <td>{{ product.Product_Name }}</td>
                            <td>{{ product.ProductCategory_Name }}</td>
                            <td>{{ product.current_quantity }} {{ product.Unit_Name }}</td>
                            <td>{{ product.Product_Purchase_Rate | decimal }}</td>
                            <td>{{ product.stock_value | decimal }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align:right;">Total Stock Value</th>
                            <th>{{ totalStockValue | decimal }}</th>
                        </tr>
                    </tfoot>
                </table>

                <table class="table table-bordered" v-if="searchType != 'current' && searchType != 'supplier'  && searchType != null" style="display:none;" v-bind:style="{display: searchType != 'current' && searchType != null ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Product Id</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Purchased Quantity</th>
                            <th>Purchase Returned Quantity</th>
                            <th>Damaged Quantity</th>
                            <th>Sold Quantity</th>
                            <th>Sales Returned Quantity</th>
                            <th>Transferred In Quantity</th>
                            <th>Transferred Out Quantity</th>
                            <th>Adjustment Add</th>
                            <th>Adjustment Less</th>
                            <th>Current Quantity</th>
                            <th>Rate</th>
                            <th>Stock Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in stock">
                            <td>{{ product.Product_Code }}</td>
                            <td>{{ product.Product_Name }}</td>
                            <td>{{ product.ProductCategory_Name }}</td>
                            <td>{{ product.purchased_quantity }}</td>
                            <td>{{ product.purchase_returned_quantity }}</td>
                            <td>{{ product.damaged_quantity }}</td>
                            <td>{{ product.sold_quantity }}</td>
                            <td>{{ product.sales_returned_quantity }}</td>
                            <td>{{ product.transferred_to_quantity}}</td>
                            <td>{{ product.transferred_from_quantity}}</td>
                            <td>{{ product.adjustment_add_qty}}</td>
                            <td>{{ product.adjustment_less_qty}}</td>
                            <td>{{ product.current_quantity }} {{ product.Unit_Name }}</td>
                            <td>{{ product.Product_Purchase_Rate | decimal }}</td>
                            <td>{{ product.stock_value | decimal }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="14" style="text-align:right;">Total Stock Value</th>
                            <th>{{ totalStockValue | decimal }}</th>
                        </tr>
                    </tfoot>
                </table>

                <table class="table table-bordered" v-if="searchType == 'supplier'" style="display:none" v-bind:style="{display: searchType == 'supplier' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Product Id</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Purchase Quantity</th>
                            <th>Purchase Return Quantity</th>
                            <th>Current Quantity</th>
                            <th>Rate</th>
                            <th>Stock Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in supplierStock">
                            <td>{{ product.Product_Code }}</td>
                            <td>{{ product.Product_Name }}</td>
                            <td>{{ product.ProductCategory_Name }}</td>
                            <td>{{ product.purchase_qty }}</td>
                            <td>{{ product.return_qty }}</td>
                            <td>{{ product.current_qty }} {{ product.Unit_Name }}</td>
                            <td>{{ product.Product_Purchase_Rate | decimal }}</td>
                            <td>{{ product.stock_value | decimal }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" style="text-align:right;">Total Stock Value</th>
                            <th>{{ totalSupplierValue | decimal }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#stock',
        data() {
            return {
                searchTypes: [{
                        text: 'Current Stock',
                        value: 'current'
                    },
                    {
                        text: 'Total Stock',
                        value: 'total'
                    },
                    {
                        text: 'Category Wise Stock',
                        value: 'category'
                    },
                    // {
                    //     text: 'Supplier Wise Stock',
                    //     value: 'supplier'
                    // },
                    {
                        text: 'Product Wise Stock',
                        value: 'product'
                    },
                    //{text: 'Brand Wise Stock', value: 'brand'}
                ],
                selectedSearchType: {
                    text: 'select',
                    value: ''
                },
                searchType: null,
                date: moment().format('YYYY-MM-DD'),
                categories: [],
                selectedCategory: null,
                products: [],
                selectedSupplier: null,
                suppliers: [],
                selectedProduct: null,
                brands: [],
                selectedBrand: null,
                selectionText: '',

                stock: [],
                supplierStock: [],
                totalStockValue: 0.00,
                totalSupplierValue: 0.00
            }
        },
        filters: {
            decimal(value) {
                return value == null ? '0.00' : parseFloat(value).toFixed(2);
            }
        },
        created() {
            this.getSuppliers();
        },
        methods: {
            onChangeSupplier() {
                this.selectedSearchType = {
                    text: 'select',
                    value: ''
                }
            },
            getStock() {

                let parameters = {};

                if (this.selectedSupplier == null) {
                    // alert('Select a supplier');
                    // return;
                } else if (this.selectedSupplier.Supplier_SlNo != '') {
                    parameters.supllierId = this.selectedSupplier.Supplier_SlNo;

                }
                if (this.selectedSearchType.value == '') {
                    alert('Select a Search Type')
                    return
                }
                this.searchType = this.selectedSearchType.value;
                let url = '';

                if (this.searchType == 'current') {
                    url = '/get_current_stock';
                } else {
                    url = '/get_total_stock';
                    parameters.date = this.date;
                }

                this.selectionText = "";

                if (this.searchType == 'category' && this.selectedCategory == null) {
                    alert('Select a category');
                    return;
                } else if (this.searchType == 'category' && this.selectedCategory != null) {
                    parameters.categoryId = this.selectedCategory.ProductCategory_SlNo;
                    this.selectionText = "Category: " + this.selectedCategory.ProductCategory_Name;
                }

                if (this.searchType == 'supplier' && this.selectedSupplier == null) {
                    alert('Select a supplier');
                    return;
                } else if (this.searchType == 'supplier' && this.selectedSupplier != null) {
                    parameters.supllierId = this.selectedSupplier.Supplier_SlNo;
                    this.selectionText = "Supplier: " + this.selectedSupplier.Supplier_Name;
                }

                if (this.searchType == 'product' && this.selectedProduct == null) {
                    alert('Select a product');
                    return;
                } else if (this.searchType == 'product' && this.selectedProduct != null) {
                    parameters.productId = this.selectedProduct.Product_SlNo;
                    this.selectionText = "product: " + this.selectedProduct.display_text;
                }

                if (this.searchType == 'brand' && this.selectedBrand == null) {
                    alert('Select a brand');
                    return;
                } else if (this.searchType == 'brand' && this.selectedBrand != null) {
                    parameters.brandId = this.selectedBrand.brand_SiNo;
                    this.selectionText = "Brand: " + this.selectedBrand.brand_name;
                }


                axios.post(url, parameters).then(res => {
                    if (this.searchType == 'current') {
                        this.stock = res.data.stock.filter((pro) => pro.current_quantity != 0);
                    } else if (this.searchType == 'supplier') {
                        this.supplierStock = res.data.supplierStock;
                    } else {
                        this.stock = res.data.stock;
                    }
                    this.totalStockValue = res.data.totalValue;
                    this.totalSupplierValue = res.data.totalSupplierValue;
                })
            },
            onChangeSearchType() {
                if (this.selectedSearchType.value == 'category' && this.categories.length == 0) {
                    this.getCategories();
                } else if (this.selectedSearchType.value == 'brand' && this.brands.length == 0) {
                    this.getBrands();
                } else if (this.selectedSearchType.value == 'supplier' && this.suppliers.length == 0) {
                    this.getSuppliers();
                } else if (this.selectedSearchType.value == 'product' && this.products.length == 0) {
                    this.getProducts();
                }
            },
            getCategories() {
                axios.get('/get_categories').then(res => {
                    this.categories = res.data;
                })
            },
            getProducts() {
                if (this.selectedSupplier == null) {
                    alert('Select a Supplier')
                    return
                }
                let filter = {
                    supplierId: this.selectedSupplier.Supplier_SlNo,
                    isService: 'false'
                }
                axios.post('/get_products', filter).then(res => {
                    this.products = res.data;
                })
            },

            getSuppliers() {
                axios.post('/get_suppliers', {
                    isService: 'false'
                }).then(res => {
                    this.suppliers = res.data;
                })
            },
            getBrands() {
                axios.get('/get_brands').then(res => {
                    this.brands = res.data;
                })
            },
            async print() {
                let reportContent = `
					<div class="container-fluid">
						<h4 style="text-align:center">${this.selectedSearchType.text} Report</h4 style="text-align:center">
						<h6 style="text-align:center">${this.selectionText}</h6>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#stockContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var reportWindow = window.open('', 'PRINT',
                    `height=${screen.height}, width=${screen.width}, left=0, top=0`);
                reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                reportWindow.document.body.innerHTML += reportContent;

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>
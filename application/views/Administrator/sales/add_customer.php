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

#customers label {
    font-size: 13px;
}

#customers select {
    border-radius: 3px;
}

#customers .add-button {
    padding: 2.5px;
    width: 28px;
    background-color: #298db4;
    display: block;
    text-align: center;
    color: white;
}

#customers .add-button:hover {
    background-color: #41add6;
    color: white;
}

#customers input[type="file"] {
    display: none;
}

#customers .custom-file-upload {
    border: 1px solid #ccc;
    display: inline-block;
    padding: 5px 12px;
    cursor: pointer;
    margin-top: 5px;
    background-color: #298db4;
    border: none;
    color: white;
}

#customers .custom-file-upload:hover {
    background-color: #41add6;
}

#customerImage {
    height: 100%;
}
</style>
<div id="customers">
    <form @submit.prevent="saveCustomer">
        <div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom:15px;">
            <div class="col-md-5">
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Customer Id:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_Code" required readonly>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Employee Id:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Employee_ID">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Customer Name:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_Name" required>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Reference Name:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.owner_name">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Department:</label>
                    <div class="col-md-7">
                        <select class="form-control" v-if="departments.length == 0"></select>
                        <v-select v-bind:options="departments" v-model="selectedDepartment" label="Department_Name"
                            v-if="departments.length > 0"></v-select>
                    </div>

                    <div class="col-md-1" style="padding: 0;">
                        <a href="<?= base_url('depertment') ?>" class="btn btn-xs btn-danger"
                            style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank"
                            title="Add New Department"><i class="fa fa-plus" aria-hidden="true"
                                style="margin-top: 5px;"></i></a>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Job Type:</label>
                    <div class="col-md-7">
                        <select class="form-control" v-if="jobTypes.length == 0"></select>
                        <v-select v-bind:options="jobTypes" v-model="selectedJobType" label="Job_Type"
                            v-if="jobTypes.length > 0"></v-select>
                    </div>

                    <div class="col-md-1" style="padding: 0;">
                        <a href="<?= base_url('job_type') ?>" class="btn btn-xs btn-danger"
                            style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank"
                            title="Add New JobType"><i class="fa fa-plus" aria-hidden="true"
                                style="margin-top: 5px;"></i></a>
                    </div>
                </div>

                <div class="form-group clearfix">

                    <label class="control-label col-md-4">Job Location:</label>

                    <div class="col-md-7">
                        <select class="form-control" v-if="jobLocations.length == 0"></select>
                        <v-select v-bind:options="jobLocations" v-model="selectedJobLocation" label="Job_Location"
                            v-if="jobLocations.length > 0"></v-select>
                    </div>

                    <div class="col-md-1" style="padding: 0;">
                        <a href="<?= base_url('job_location') ?>" class="btn btn-xs btn-danger"
                            style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank"
                            title="Add New JobType"><i class="fa fa-plus" aria-hidden="true"
                                style="margin-top: 5px;"></i></a>
                    </div>
                </div>

                <!-- <div class="form-group clearfix">
                    <label class="control-label col-md-4">Job Location:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.job_location">
                    </div>
                </div> -->

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Active status:</label>
                    <div class="col-md-7">
                        <v-select :options="['Active', 'Deactive']" v-model="customer.active_status"></v-select>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Joining Date:</label>
                    <div class="col-md-7">
                        <input type="date" class="form-control" v-model="customer.joining_date">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">NID </label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.nid">
                    </div>
                </div>

                <!-- <div class="form-group clearfix">
                    <label class="control-label col-md-4">Area:</label>
                    <div class="col-md-7">
                        <select class="form-control" v-if="districts.length == 0"></select>
                        <v-select v-bind:options="districts" v-model="selectedDistrict" label="District_Name"
                            v-if="districts.length > 0"></v-select>
                    </div>
                    <div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/area" target="_blank"
                            class="add-button"><i class="fa fa-plus"></i></a></div>
                </div> -->
            </div>

            <div class="col-md-5">

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Gross Salary </label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.gross_salary">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Opening Due </label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.opening_due">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Address:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_Address">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Mobile:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_Mobile" required>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Office Phone:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_OfficePhone">
                    </div>
                </div>

                <!-- <div class="form-group clearfix">
                    <label class="control-label col-md-4">Previous Due:</label>
                    <div class="col-md-7">
                        <input type="number" class="form-control" v-model="customer.previous_due" required>
                    </div>
                </div> -->

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Credit Limit:</label>
                    <div class="col-md-7">
                        <input type="number" class="form-control" v-model="customer.Customer_Credit_Limit" required>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Customer Type:</label>
                    <div class="col-md-2">
                        <input type="radio" id="Retail" name="customerType" value="retail"
                            v-model="customer.Customer_Type">
                        <label for="Retail">Retail</label>
                    </div>
                    <div class="col-md-3">
                        <input type="radio" id="Wholesale" name="customerType" value="wholesale"
                            v-model="customer.Customer_Type">
                        <label for="Wholesale">Wholesale</label>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <div class="col-md-7 col-md-offset-4 text-right">
                        <input type="submit" class="btn btn-success btn-sm" value="Save">
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-center;">
                <div class="form-group clearfix">
                    <div style="width: 100px;height:100px;border: 1px solid #ccc;overflow:hidden;">
                        <img id="customerImage" v-if="imageUrl == '' || imageUrl == null" src="/assets/no_image.gif">
                        <img id="customerImage" v-if="imageUrl != '' && imageUrl != null" v-bind:src="imageUrl">
                    </div>
                    <div style="text-align:center;">
                        <label class="custom-file-upload">
                            <input type="file" @change="previewImage" />
                            Select Image
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-sm-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="customers" :filter-by="filter" style="margin-bottom: 5px;">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.AddTime | dateOnly('DD-MM-YYYY') }}</td>
                            <td>{{ row.Customer_Code }}</td>
                            <td>{{ row.Employee_ID }}</td>
                            <td>{{ row.Customer_Name }}</td>
                            <td>{{ row.Department_Name }}</td>
                            <td>{{ row.owner_name }}</td>
                            <td>{{ row.Job_Type }}</td>
                            <td>{{ row.Job_Location }}</td>
                            <td>{{ row.Customer_Mobile }}</td>
                            <td>{{ row.Customer_Type }}</td>
                            <td>{{ row.Customer_Credit_Limit }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                <button type="button" class="button edit" @click="editCustomer(row)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button type="button" class="button" @click="deleteCustomer(row.Customer_SlNo)">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;">
                </datatable-pager>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
Vue.component('v-select', VueSelect.VueSelect);
new Vue({
    el: '#customers',
    data() {
        return {
            customer: {
                Customer_SlNo: 0,
                Customer_Code: '<?php echo $customerCode; ?>',
                Customer_Name: '',
                Customer_Type: 'retail',
                Customer_Phone: '',
                Employee_ID: '',
                department_id: '',
                job_type_id: '',
                job_location_id: '',
                active_status: '',
                joining_date: '',
                nid: '',
                opening_due: '',
                gross_salary: '',
                Customer_Mobile: '',
                Customer_Email: '',
                Customer_OfficePhone: '',
                Customer_Address: '',
                owner_name: '',
                //area_ID: '',
                Customer_Credit_Limit: 0,
                // previous_due: 0
            },
            customers: [],
            districts: [],
            jobTypes: [],
            jobLocations: [],
            selectedDistrict: null,
            selectedJobType: null,
            selectedDepartment: null,
            selectedJobLocation: null,
            departments: [],
            imageUrl: '',
            selectedFile: null,

            columns: [{
                    label: 'Added Date',
                    field: 'AddTime',
                    align: 'center',
                    filterable: false
                },
                {
                    label: 'Customer Id',
                    field: 'Customer_Code',
                    align: 'center',
                    filterable: false
                },
                {
                    label: 'Employee Id',
                    field: 'Employee_ID',
                    align: 'center',
                    filterable: false
                },
                {
                    label: 'Customer Name',
                    field: 'Customer_Name',
                    align: 'center'
                },
                {
                    label: 'Department',
                    field: 'Department_Name',
                    align: 'center'
                },
                {
                    label: 'Reference Name',
                    field: 'owner_name',
                    align: 'center'
                },
                {
                    label: 'Job Type',
                    field: 'Job_Type',
                    align: 'center'
                },
                {
                    label: 'Job Location',
                    field: 'Job_Location',
                    align: 'center'
                },
                {
                    label: 'Mobile No',
                    field: 'Customer_Mobile',
                    align: 'center'
                },
                {
                    label: 'Customer Type',
                    field: 'Customer_Type',
                    align: 'center'
                },
                {
                    label: 'Credit Limit',
                    field: 'Customer_Credit_Limit',
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
        dateOnly(datetime, format) {
            return moment(datetime).format(format);
        }
    },
    created() {
        this.getDistricts();
        this.getCustomers();
        this.getDepartments();
        this.getJobTypes();
        this.getJobLocations();
    },
    methods: {
        getDistricts() {
            axios.get('/get_districts').then(res => {
                this.districts = res.data;
            })
        },

        getDepartments() {
            axios.get('/get_department').then(res => {
                this.departments = res.data;
            })
        },
        getJobTypes() {
            axios.get('/get_job_type').then(res => {
                this.jobTypes = res.data;
            })
        },
        getJobLocations() {
            axios.get('/get_job_location').then(res => {
                this.jobLocations = res.data;
            })
        },
        getCustomers() {
            axios.get('/get_customers').then(res => {
                this.customers = res.data;
            })
        },
        previewImage() {
            if (event.target.files.length > 0) {
                this.selectedFile = event.target.files[0];
                this.imageUrl = URL.createObjectURL(this.selectedFile);
            } else {
                this.selectedFile = null;
                this.imageUrl = null;
            }
        },

        saveCustomer() {

            // if (this.selectedDistrict == null) {
            //     alert('Select area');
            //     return;
            // }

            //this.customer.area_ID = this.selectedDistrict.District_SlNo;

            let url = '/add_customer';

            if (this.customer.Customer_SlNo != 0) {
                url = '/update_customer';
            }

            this.customer.department_id = this.selectedDepartment.Department_SlNo;
            this.customer.job_type_id = this.selectedJobType.jobType_SlNo;
            this.customer.job_location_id = this.selectedJobLocation.jobLocation_SlNo;
            // console.log(this.customer);
            // return;
            let fd = new FormData();
            fd.append('image', this.selectedFile);
            fd.append('data', JSON.stringify(this.customer));

            axios.post(url, fd, {
                onUploadProgress: upe => {
                    let progress = Math.round(upe.loaded / upe.total * 100);
                    console.log(progress);
                }
            }).then(res => {
                let r = res.data;
                alert(r.message);
                if (r.success) {
                    this.resetForm();
                    this.customer.Customer_Code = r.customerCode;
                    this.getCustomers();
                }
            })
        },
        editCustomer(customer) {
            let keys = Object.keys(this.customer);
            keys.forEach(key => {
                this.customer[key] = customer[key];
            })

            this.selectedDistrict = {
                //District_SlNo: customer.area_ID,
                District_Name: customer.District_Name
            }

            this.selectedDepartment = {
                //District_SlNo: customer.area_ID,
                Department_Name: customer.Department_Name
            }

            if (customer.image_name == null || customer.image_name == '') {
                this.imageUrl = null;
            } else {
                this.imageUrl = '/uploads/customers/' + customer.image_name;
            }
        },
        deleteCustomer(customerId) {
            let deleteConfirm = confirm('Are you sure?');
            if (deleteConfirm == false) {
                return;
            }
            axios.post('/delete_customer', {
                customerId: customerId
            }).then(res => {
                let r = res.data;
                alert(r.message);
                if (r.success) {
                    this.getCustomers();
                }
            })
        },
        resetForm() {
            let keys = Object.keys(this.customer);
            keys = keys.filter(key => key != "Customer_Type");
            keys.forEach(key => {
                if (typeof(this.customer[key]) == 'string') {
                    this.customer[key] = '';
                } else if (typeof(this.customer[key]) == 'number') {
                    this.customer[key] = 0;
                }
            })
            this.imageUrl = '';
            this.selectedFile = null;
            this.selectedDepartment = '';
        }
    }
})
</script>
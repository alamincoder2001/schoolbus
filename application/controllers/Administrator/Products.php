<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->model('Billing_model');
    }
    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product";
        $data['productCode'] = $this->mt->generateProductCode();
        $data['content'] = $this->load->view('Administrator/products/add_product', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function fanceybox_unit()
    {
        $this->load->view('Administrator/products/fanceybox_unit');
    }
    public function insert_unit()
    {
        $mail = $this->input->post('add_unit');
        $query = $this->db->query("SELECT Unit_Name from tbl_unit where Unit_Name = '$mail'");

        if ($query->num_rows() > 0) {
            $data['exists'] = "This Name is Already Exists";
            $this->load->view('Administrator/ajax/fanceybox_product_unit', $data);
        } else {
            $data = array(
                "Unit_Name"          => $this->input->post('add_unit', TRUE),
                "AddBy"                  => $this->session->userdata("FullName"),
                "AddTime"                => date("Y-m-d H:i:s")
            );
            $this->mt->save_data('tbl_unit', $data);
            $this->load->view('Administrator/ajax/fanceybox_product_unit');
        }
    }

    public function addProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $productObj = json_decode($this->input->raw_input_stream);

            $productNameCount = $this->db->query("select * from tbl_product where Product_Name = ?", $productObj->Product_Name)->num_rows();
            if ($productNameCount > 0) {
                $res = ['success' => false, 'message' => 'Product name already exists'];
                echo json_encode($res);
                exit;
            }

            $productCodeCount = $this->db->query("select * from tbl_product where Product_Code = ?", $productObj->Product_Code)->num_rows();
            if ($productCodeCount > 0) {
                $res = ['success' => false, 'message' => 'Product code already exists'];
                echo json_encode($res);
                exit;
            }

            $product = (array)$productObj;
            $product['is_service'] = $productObj->is_service == true ? 'true' : 'false';
            $product['status'] = 'a';
            $product['AddBy'] = $this->session->userdata("FullName");
            $product['AddTime'] = date('Y-m-d H:i:s');
            $product['Product_branchid'] = $this->brunch;

            $this->db->insert('tbl_product', $product);

            $res = ['success' => true, 'message' => 'Product added successfully', 'productId' => $this->mt->generateProductCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    public function chk_product_code()
    {
        $Pid = $this->input->post('Pid');
        $data['duplicate'] = array();

        $query = $this->db->query("SELECT * FROM tbl_product WHERE Product_Code='$Pid'");
        if ($query->num_rows() > 0) {
            $data['duplicate'] = 'yes';
        }
        $this->load->view('Administrator/ajax/product', $data['duplicate']);
    }
    public function product_edit()
    {
        $data['title'] = "Update Product";
        $id = $this->input->post('edit');
        $data['allproduct'] =  $this->Billing_model->select_all_Product();
        $data['selected'] = $this->Billing_model->get_product_by_id($id);
        $this->load->view('Administrator/edit/product', $data);
    }
    public function updateProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $productObj = json_decode($this->input->raw_input_stream);

            $productNameCount = $this->db->query("select * from tbl_product where Product_Name = ? and Product_SlNo != ?", [$productObj->Product_Name, $productObj->Product_SlNo])->num_rows();
            if ($productNameCount > 0) {
                $res = ['success' => false, 'message' => 'Product name already exists'];
                echo json_encode($res);
                exit;
            }

            $productCodeCount = $this->db->query("select * from tbl_product where Product_Code = ? and Product_SlNo != ?", [$productObj->Product_Code, $productObj->Product_SlNo])->num_rows();
            if ($productCodeCount > 0) {
                $res = ['success' => false, 'message' => 'Product code already exists'];
                echo json_encode($res);
                exit;
            }

            $product = (array)$productObj;
            unset($product['Product_SlNo']);
            $product['is_service'] = $productObj->is_service == true ? 'true' : 'false';
            $product['UpdateBy'] = $this->session->userdata("FullName");
            $product['UpdateTime'] = date('Y-m-d H:i:s');

            $this->db->where('Product_SlNo', $productObj->Product_SlNo)->update('tbl_product', $product);

            $res = ['success' => true, 'message' => 'Product updated successfully', 'productId' => $this->mt->generateProductCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    public function deleteProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $this->db->set(['status' => 'd'])->where('Product_SlNo', $data->productId)->update('tbl_product');

            $res = ['success' => true, 'message' => 'Product deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function activeProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $productId = $this->input->post('productId');
            $this->db->query("update tbl_product set status = 'a' where Product_SlNo = ?", $productId);
            $res = ['success' => true, 'message' => 'Product activated'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getProducts()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and p.supplier_id = '$data->supplierId'";
        }
        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and p.ProductCategory_ID = '$data->categoryId'";
        }

        if (isset($data->isService) && $data->isService != null && $data->isService != '') {
            $clauses .= " and p.is_service = '$data->isService'";
        }

        $products = $this->db->query("
            select
                p.*,
                s.Supplier_Name,
                s.Supplier_Code,
                concat(p.Product_Name, ' - ', p.Product_Code) as display_text,
                concat(s.Supplier_Code, ' - ', s.Supplier_Name) as supplier_text,
                pc.ProductCategory_Name,
                br.brand_name,
                u.Unit_Name
            from tbl_product p
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            left join tbl_brand br on br.brand_SiNo = p.brand
            left join tbl_supplier s on s.Supplier_SlNo = p.supplier_id
            left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
            where p.status = 'a'
            $clauses
            order by p.Product_SlNo desc
        ")->result();

        echo json_encode($products);
    }

    public function getProductStock()
    {
        $inputs = json_decode($this->input->raw_input_stream);
        $stock = $this->mt->productStock($inputs->productId);
        echo $stock;
    }

    public function getCurrentStock()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->stockType) && $data->stockType == 'low') {
            $clauses .= " and current_quantity <= Product_ReOrederLevel";
        }
        $supplierClause = "";
        if (isset($data->supllierId) && $data->supllierId != '') {
            $supplierClause .= " and p.supplier_id = '$data->supllierId' ";
        }

        $stock = $this->mt->currentStock($clauses, $supplierClause);
        $res['stock'] = $stock;
        $res['totalValue'] = array_sum(
            array_map(function ($product) {
                return $product->stock_value;
            }, $stock)
        );

        echo json_encode($res);
    }

    public function getTotalStock()
    {

        $data = json_decode($this->input->raw_input_stream);

        $branchId = $this->session->userdata('BRANCHid');
        $clauses = "";
        if (isset($data->categoryId) && $data->categoryId != null) {
            $clauses .= " and p.ProductCategory_ID = '$data->categoryId'";
        }

        if (isset($data->productId) && $data->productId != null) {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->brandId) && $data->brandId != null) {
            $clauses .= " and p.brand = '$data->brandId'";
        }

        $supplierClause = "";
        if (isset($data->supllierId) && $data->supllierId != '') {
            $supplierClause .= " and p.supplier_id = '$data->supllierId' ";
        }

        $stock = $this->db->query("
            select
                p.*,
                pc.ProductCategory_Name,
                b.brand_name,
                u.Unit_Name,
                (select ifnull(sum(pd.PurchaseDetails_TotalQuantity), 0) 
                    from tbl_purchasedetails pd 
                    join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
                    where pd.Product_IDNo = p.Product_SlNo
                    and pd.PurchaseDetails_branchID = '$branchId'
                    and pd.Status = 'a'
                    " . (isset($data->date) && $data->date != null ? " and pm.PurchaseMaster_OrderDate <= '$data->date'" : "") . "
                ) as purchased_quantity,
                        
                (select ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) 
                    from tbl_purchasereturndetails prd 
                    join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                    where prd.PurchaseReturnDetailsProduct_SlNo = p.Product_SlNo
                    and prd.PurchaseReturnDetails_brachid = '$branchId'
                    " . (isset($data->date) && $data->date != null ? " and pr.PurchaseReturn_ReturnDate <= '$data->date'" : "") . "
                ) as purchase_returned_quantity,
                        
                (select ifnull(sum(sd.SaleDetails_TotalQuantity), 0) 
                    from tbl_saledetails sd
                    join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
                    where sd.Product_IDNo = p.Product_SlNo
                    and sd.SaleDetails_BranchId  = '$branchId'
                    and sd.Status = 'a'
                    " . (isset($data->date) && $data->date != null ? " and sm.SaleMaster_SaleDate <= '$data->date'" : "") . "
                ) as sold_quantity,
                        
                (select ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0)
                    from tbl_salereturndetails srd 
                    join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                    where srd.SaleReturnDetailsProduct_SlNo = p.Product_SlNo
                    and srd.SaleReturnDetails_brunchID = '$branchId'
                    " . (isset($data->date) && $data->date != null ? " and sr.SaleReturn_ReturnDate <= '$data->date'" : "") . "
                ) as sales_returned_quantity,
                        
                (select ifnull(sum(dmd.DamageDetails_DamageQuantity), 0) 
                    from tbl_damagedetails dmd
                    join tbl_damage dm on dm.Damage_SlNo = dmd.Damage_SlNo
                    where dmd.Product_SlNo = p.Product_SlNo
                    and dmd.status = 'a'
                    and dm.Damage_brunchid = '$branchId'
                    " . (isset($data->date) && $data->date != null ? " and dm.Damage_Date <= '$data->date'" : "") . "
                ) as damaged_quantity,
            
                (select ifnull(sum(trd.quantity), 0)
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = p.Product_SlNo
                    and tm.transfer_from = '$branchId'
                    " . (isset($data->date) && $data->date != null ? " and tm.transfer_date <= '$data->date'" : "") . "
                ) as transferred_from_quantity,

                (select ifnull(sum(trd.quantity), 0)
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = p.Product_SlNo
                    and tm.transfer_to = '$branchId'
                    " . (isset($data->date) && $data->date != null ? " and tm.transfer_date <= '$data->date'" : "") . "
                ) as transferred_to_quantity,

                (select ifnull(sum(adjd.AdjustmentDetails_AdjustmentQuantity), 0)
                    from tbl_adjustmentdetails adjd
                    left join tbl_adjustment adj on adj.Adjustment_SlNo = adjd.Adjustment_SlNo
                    where adjd.Product_SlNo = p.Product_SlNo
                    and adj.adjustment_type = 'Add Stock'
                    and adjd.status != 'd'
                    " . (isset($data->date) && $data->date != null ? " and adj.Adjustment_Date <= '$data->date'" : "") . "
                ) as adjustment_add_qty,

                (select ifnull(sum(adjd.AdjustmentDetails_AdjustmentQuantity), 0)
                    from tbl_adjustmentdetails adjd
                    left join tbl_adjustment adj on adj.Adjustment_SlNo = adjd.Adjustment_SlNo
                    where adjd.Product_SlNo = p.Product_SlNo
                    and adj.adjustment_type = 'Less Stock'
                    and adjd.status != 'd'
                    " . (isset($data->date) && $data->date != null ? " and adj.Adjustment_Date <= '$data->date'" : "") . "
                ) as adjustment_less_qty,
                        
                (select (purchased_quantity + sales_returned_quantity + transferred_to_quantity + adjustment_add_qty) - (sold_quantity + purchase_returned_quantity + damaged_quantity + transferred_from_quantity + adjustment_less_qty)) as current_quantity,
                (select p.Product_Purchase_Rate * current_quantity) as stock_value
            from tbl_product p
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            left join tbl_brand b on b.brand_SiNo = p.brand
            left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
            where p.status = 'a' and p.is_service = 'false' $clauses $supplierClause
        ")->result();

        $res['stock'] = $stock;
        $res['totalValue'] = array_sum(
            array_map(function ($product) {
                return $product->stock_value;
            }, $stock)
        );

        echo json_encode($res);
    }


    public function getSupplierWiseStock()
    {

        $data = json_decode($this->input->raw_input_stream);

        $branchId = $this->session->userdata('BRANCHid');

        $supplierStock = $this->db->query("
        SELECT
        p.Product_SlNo,
        p.Product_Code,
        p.Product_Name,
        p.Product_Purchase_Rate,
        pc.ProductCategory_Name,
        u.Unit_Name,
        (SELECT IFNULL( SUM(pd.PurchaseDetails_TotalQuantity),0)
        FROM tbl_purchasedetails pd
        LEFT JOIN tbl_purchasemaster pmp ON pmp.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
        WHERE pd.Status = 'a' AND pmp.Supplier_SlNo = pm.Supplier_SlNo AND pd.Product_IDNo = p.Product_SlNo
    ) AS purchase_qty,
    (
        SELECT IFNULL( SUM( prd.PurchaseReturnDetails_ReturnQuantity),0)
        FROM tbl_purchasereturndetails prd
        LEFT JOIN tbl_purchasereturn pr ON pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
        LEFT JOIN tbl_purchasemaster pmp ON pmp.PurchaseMaster_InvoiceNo = pr.PurchaseMaster_InvoiceNo
        WHERE prd.Status = 'a' AND pmp.Supplier_SlNo = pm.Supplier_SlNo AND prd.PurchaseReturnDetailsProduct_SlNo = p.Product_SlNo
    ) AS return_qty,

    (SELECT purchase_qty - return_qty) AS current_qty,

    (select p.Product_Purchase_Rate * current_qty) as stock_value
    
    FROM tbl_purchasedetails pd
    LEFT JOIN tbl_product p ON p.Product_SlNo = pd.Product_IDNo
    LEFT JOIN tbl_purchasemaster pm ON pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
    left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
    left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
    WHERE pd.Status = 'a' 
    AND pm.Supplier_SlNo = ?
    GROUP BY p.Product_SlNo", $data->supllierId)->result();

        $res['supplierStock'] = $supplierStock;

        $res['totalSupplierValue'] = array_sum(
            array_map(function ($product) {
                return $product->stock_value;
            }, $supplierStock)
        );

        echo json_encode($res);
    }

    public function fanceybox_category()
    {
        $this->load->view('Administrator/products/fanceybox_category');
    }
    public function insert_fanceybox_category()
    {
        $mail = $this->input->post('add_Category');
        $query = $this->db->query("SELECT ProductCategory_Name from tbl_productcategory where ProductCategory_Name = '$mail'");

        if ($query->num_rows() > 0) {
            $data['exists'] = "This Name is Already Exists";
            $this->load->view('Administrator/ajax/fanceybox_product_cat', $data);
        } else {
            $data = array(
                "ProductCategory_Name"                  => $this->input->post('add_Category', TRUE),
                "ProductCategory_Description"           => $this->input->post('catdescrip', TRUE),
                "AddBy"                                 => $this->session->userdata("FullName"),
                "AddTime"                               => date("Y-m-d H:i:s")
            );
            $this->mt->save_data('tbl_productcategory', $data);
            $this->load->view('Administrator/ajax/fanceybox_product_cat');
        }
    }

    public function current_stock()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Current Stock";
        $data['categories'] = $this->Other_model->branch_wise_category();
        $data['brands'] = $this->Other_model->branch_wise_brand();
        $data['products'] = $this->Product_model->products_by_brunch();
        $data['content'] = $this->load->view('Administrator/stock/current_stock', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function stockAvailable()
    {
        $data['title'] = "Stock Available";
        $branchID = $this->session->userdata("BRANCHid");
        $sql = "SELECT tbl_purchaseinventory.*,tbl_product.*,tbl_purchasedetails.* FROM tbl_purchaseinventory left join tbl_product on tbl_product.Product_SlNo = tbl_purchaseinventory.purchProduct_IDNo left join tbl_purchasedetails on tbl_purchasedetails.Product_IDNo = tbl_product.Product_SlNo WHERE tbl_purchaseinventory.PurchaseInventory_brunchid = '$branchID' group by tbl_purchasedetails.Product_IDNo";

        $result = $this->db->query($sql);
        $data['record'] = $result->result();
        $data['branchID'] =  $this->session->userdata("BRANCHid");
        $data['content'] = $this->load->view('Administrator/stock/stock_available', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function total_stock()
    {
        $data['title'] = "Total Stock";
        $data['content'] = $this->load->view('Administrator/stock/total_stock', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function searchproduct()
    {
        $data['Searchkey'] = $this->input->post('Searchkey');
        $this->load->view('Administrator/ajax/search_product', $data);
    }

    public function branch_stock()
    {
        $data['title'] = "Branch Stock";
        $data['content'] = $this->load->view('Administrator/stock/branch_stock', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function branch_stock_search()
    {
        $data['Branch_ID'] = $BranchID = $this->input->post('BranchID');
        $data['Branch_category'] = $category = $this->input->post('Categorys');
        $this->session->set_userdata($data);
        if ($category != 'All') {
            $this->db->SELECT("
                tbl_product.*, 
                tbl_productcategory.*,
                tbl_unit.*,
                tbl_color.*,
                tbl_brand.* 
                FROM tbl_product 
                left join tbl_productcategory on tbl_productcategory.ProductCategory_SlNo= tbl_product.ProductCategory_ID 
                left join tbl_unit on tbl_unit.Unit_SlNo=tbl_product.Unit_ID  
                LEFT JOIN tbl_color ON tbl_color.color_SiNo=tbl_product.color 
                LEFT JOIN tbl_brand ON tbl_brand.brand_SiNo=tbl_product.brand 
                where tbl_product.ProductCategory_ID = '$category' 
                AND tbl_product.Product_branchid = '$BranchID'
            ");
            $query = $this->db->get();
            $result = $query->result();
            $data['product'] = $result;
            $data['show'] = 1;
        } else {
            $this->db->SELECT('*');
            $this->db->from('tbl_productcategory');
            $this->db->where('category_branchid', $BranchID);
            $query = $this->db->get();
            $category = $query->result();

            foreach ($category as $vcategory) {
                $categoryid = $vcategory->ProductCategory_SlNo;
                $this->db->SELECT("
                        tbl_product.*, 
                        tbl_productcategory.*,
                        tbl_unit.*,
                        tbl_color.*,
                        tbl_brand.* 
                    FROM tbl_product 
                    left join tbl_productcategory on tbl_productcategory.ProductCategory_SlNo= tbl_product.ProductCategory_ID 
                    left join tbl_unit on tbl_unit.Unit_SlNo=tbl_product.Unit_ID  
                    LEFT JOIN tbl_color ON tbl_color.color_SiNo=tbl_product.color 
                    LEFT JOIN tbl_brand ON tbl_brand.brand_SiNo=tbl_product.brand 
                    where tbl_product.ProductCategory_ID = '$categoryid' 
                    AND tbl_product.Product_branchid = '$BranchID'
                ");
                $query = $this->db->get();
                $productCat[] = $query->result();
                //$data['productCat'] = $query->result();
            }

            $data['category'] = $category;
            $data['productCat'] = @$productCat;
            $data['show'] = 0;
        }
        $this->load->view('Administrator/stock/branch_stock_search', $data);
    }

    public function search_stock()
    {
        $Store = $data['Store'] = $this->input->post('Store');
        $Category = $data['Category'] = $this->input->post('Category');
        $Product =  $data['Product'] = $this->input->post('Product');
        $Supplier =  $data['Supplier']  = $this->input->post('Supplier');
        $brand =  $data['brand']  = $this->input->post('brand');
        $branchID = $data['branchID'] = $this->session->userdata("BRANCHid");
        //		 echo $brand; die();

        if ($Store == 'Total' || $Store == 'Current') :
            $data['sql'] = $this->db->query("SELECT tbl_purchaseinventory.*,tbl_product.*,tbl_purchasedetails.* FROM tbl_purchaseinventory left join tbl_product on tbl_product.Product_SlNo = tbl_purchaseinventory.purchProduct_IDNo left join tbl_purchasedetails on tbl_purchasedetails.Product_IDNo = tbl_product.Product_SlNo left join sr_transferdetails on sr_transferdetails.Product_IDNo = tbl_product.Product_SlNo WHERE tbl_product.status='a' AND tbl_purchaseinventory.PurchaseInventory_brunchid = '$branchID' or sr_transferdetails.Brunch_to = '$branchID' group by tbl_purchasedetails.Product_IDNo")->result();

        elseif ($Store == 'Category') :
            $data['sql'] = $this->db->query("SELECT tbl_purchaseinventory.*,tbl_product.*,tbl_purchasedetails.* FROM tbl_purchaseinventory left join tbl_product on tbl_product.Product_SlNo = tbl_purchaseinventory.purchProduct_IDNo left join tbl_purchasedetails on tbl_purchasedetails.Product_IDNo = tbl_product.Product_SlNo left join sr_transferdetails on sr_transferdetails.Product_IDNo = tbl_product.Product_SlNo WHERE tbl_product.status='a' AND tbl_product.ProductCategory_ID='$Category' AND  tbl_purchaseinventory.PurchaseInventory_brunchid = '$branchID' or sr_transferdetails.Brunch_to = '$branchID' group by tbl_purchasedetails.Product_IDNo")->result();

        elseif ($Store == 'Product') :
            $data['sql'] = $this->db->query("SELECT tbl_purchaseinventory.*,tbl_product.*,tbl_purchasedetails.* FROM tbl_purchaseinventory left join tbl_product on tbl_product.Product_SlNo = tbl_purchaseinventory.purchProduct_IDNo left join tbl_purchasedetails on tbl_purchasedetails.Product_IDNo = tbl_product.Product_SlNo left join sr_transferdetails on sr_transferdetails.Product_IDNo = tbl_product.Product_SlNo WHERE tbl_product.status='a' AND tbl_product.Product_SlNo='$Product' AND tbl_purchaseinventory.PurchaseInventory_brunchid = '$branchID' or sr_transferdetails.Brunch_to = '$branchID' group by tbl_purchasedetails.Product_IDNo")->result();

        elseif ($Store == 'Supplier') :
            $data['sql'] = $this->db->query("SELECT tbl_purchaseinventory.*,tbl_product.*,tbl_purchasedetails.* FROM tbl_purchaseinventory left join tbl_product on tbl_product.Product_SlNo = tbl_purchaseinventory.purchProduct_IDNo left join tbl_purchasedetails on tbl_purchasedetails.Product_IDNo = tbl_product.Product_SlNo left join sr_transferdetails on sr_transferdetails.Product_IDNo = tbl_product.Product_SlNo WHERE tbl_product.status='a' AND tbl_purchasedetails.Supplier_IDNo = '$Supplier' AND tbl_purchaseinventory.PurchaseInventory_brunchid = '$branchID' or sr_transferdetails.Brunch_to = '$branchID' group by tbl_purchasedetails.Product_IDNo")->result();
        elseif ($Store == 'Brand') :
            $ddd = $data['sql'] = $this->db->query("SELECT tbl_purchaseinventory.*,tbl_product.*,tbl_purchasedetails.* FROM tbl_purchaseinventory left join tbl_product on tbl_product.Product_SlNo = tbl_purchaseinventory.purchProduct_IDNo left join tbl_purchasedetails on tbl_purchasedetails.Product_IDNo = tbl_product.Product_SlNo left join sr_transferdetails on sr_transferdetails.Product_IDNo = tbl_product.Product_SlNo WHERE tbl_product.status='a' AND tbl_product.brand='$brand' AND  tbl_purchaseinventory.PurchaseInventory_brunchid = '$branchID' or sr_transferdetails.Brunch_to = '$branchID' group by tbl_purchasedetails.Product_IDNo")->result();
        endif;


        $this->session->set_userdata($data);
        $this->load->view('Administrator/stock/search_stock', $data);
    }


    public function fanceybox_warehouse()
    {
        $this->load->view('Administrator/products/fanceybox_warehouse');
    }
    public function insert_fanceybox_Warehouse()
    {
        $mail = $this->input->post('add_Category');
        $query = $this->db->query("SELECT warehouse_name from tbl_warehouse where warehouse_name = '$mail'");

        if ($query->num_rows() > 0) {
            $data['exists'] = "This Name is Already Exists";
            $this->load->view('Administrator/ajax/fanceybox_Warehouse', $data);
        } else {
            $data = array(
                "warehouse_name"    => $this->input->post('add_Category', TRUE)

            );
            $this->mt->save_data('tbl_warehouse', $data);
            $this->load->view('Administrator/ajax/fanceybox_Warehouse');
        }
    }

    /*  public function selectProduct(){
		$data['title']  = 'Product';
        $pCategory = $this->input->post('pCategory');
        $brand = $this->input->post('brand');
        $BRANCHid = $this->session->userdata("BRANCHid");
        $data['sproduct'] = $this->Billing_model->selectProduct($pCategory,$brand,$BRANCHid);
	    $data['content'] = $this->load->view('Administrator/products/add_product', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
	 */
    public function selectProduct()
    {
        $data['title']  = 'Product';
        $brand = $this->input->post('brand');
        $pCategory = $this->input->post('pCategory');
        $BRANCHid = $this->session->userdata("BRANCHid");
        if ($brand == 'All' and $pCategory != 'All') {
            $data['sproduct'] =  $this->Billing_model->select_Product_by_category($pCategory, $BRANCHid);
        } else if ($brand == 'All' and $pCategory == 'All') {
            $data['allproduct'] =  $this->Billing_model->select_all_Product();
        } else if ($brand != 'All' and $pCategory == 'no') {
            $data['sproduct'] = $this->Billing_model->select_Product_by_brand($brand, $BRANCHid);
        } else {
            $data['sproduct'] = $this->Billing_model->selectProduct($pCategory, $brand, $BRANCHid);
        }

        $data['content'] = $this->load->view('Administrator/products/add_product', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function productlist()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title']  = 'Product';
        $data['allproduct'] =  $this->Billing_model->select_all_Product_list();

        $this->load->view('Administrator/products/productList', $data);
        //$this->load->view('Administrator/index', $data);
    }

    public function product_name()
    {
        $data['allproduct'] = $allproduct =  $this->Billing_model->get_product_name();
        // print_r($allproduct); exit();
        $this->load->view('Administrator/products/product_name', $data);
    }

    public function barcodeGenerateFancybox($Product_SlNo)
    {
        $data['Product_SlNo'] = $Product_SlNo;
        $this->load->view('Administrator/products/barcode_fancybox', $data);
    }

    public function barcodeGenerate($Product_SlNo)
    {
        $data['product'] = $this->Billing_model->select_Product_by_id($Product_SlNo);
        $this->load->view('Administrator/products/barcode/barcode', $data);
    }

    function barcode($kode)
    {

        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        Zend_Barcode::render('code128', 'image', array('text' => $kode), array());
    }

    public function view_all_product()
    {
        $data['title']  = 'Product';
        $data['allproduct'] =  $allproduct = $this->Billing_model->select_Product_without_limit();

?>
        <br />
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>Product ID</th>
                        <th>Categoty Name</th>
                        <th>Product Name</th>
                        <th class="hidden-480">Brand</th>

                        <th>Color</th>
                        <!--<th class="hidden-480">Purchase Rate</th>
					<th class="hidden-480">Sell Rate</th>--->

                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    foreach ($allproduct as $vallproduct) {
                    ?>
                        <tr>
                            <td class="center">
                                <label class="pos-rel">
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>

                            <td>
                                <a href="#"><?php echo $vallproduct->Product_Code; ?></a>
                            </td>
                            <td><?php echo $vallproduct->ProductCategory_Name; ?></td>
                            <td class="hidden-480"><?php echo $vallproduct->Product_Name; ?></td>
                            <td><?php echo $vallproduct->brand_name; ?></td>

                            <td class="hidden-480">
                                <span class="label label-sm label-info arrowed arrowed-righ">
                                    <?php echo $vallproduct->color_name; ?>
                                </span>
                            </td>
                            <!--<td class="hidden-480"><?php echo $vallproduct->Product_Purchase_Rate; ?></td>
								<td class="hidden-480"><?php echo $vallproduct->Product_SellingPrice; ?></td>-->

                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <span class="blue" onclick="Edit_product(<?php echo $vallproduct->Product_SlNo; ?>)" style="cursor:pointer;">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </span>

                                    <a class="green" href="" onclick="deleted(<?php echo $vallproduct->Product_SlNo; ?>)">
                                        <i class="ace-icon fa fa-trash bigger-130 text-danger"></i>
                                    </a>

                                    <a class="black" href="<?php echo base_url(); ?>Administrator/Products/barcodeGenerate/<?php echo $vallproduct->Product_SlNo; ?>" target="_blank">
                                        <i class="ace-icon fa fa-barcode bigger-130"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
<?php
        //echo "<pre>";print_r($data['allproduct']);exit;
        //$this->load->view('Administrator/products/all_product', $data, TRUE);
        //$this->load->view('Administrator/index', $data);
    }

    public function productLedger()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title']  = 'Product Ledger';

        $data['content'] = $this->load->view('Administrator/products/product_ledger', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function getProductLedger()
    {
        $data = json_decode($this->input->raw_input_stream);
        $result = $this->db->query("
            select
                'a' as sequence,
                pd.PurchaseDetails_SlNo as id,
                pm.PurchaseMaster_OrderDate as date,
                concat('Purchase - ', pm.PurchaseMaster_InvoiceNo, ' - ', s.Supplier_Name) as description,
                pd.PurchaseDetails_Rate as rate,
                pd.PurchaseDetails_TotalQuantity as in_quantity,
                0 as out_quantity
            from tbl_purchasedetails pd
            join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
            join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pd.Status = 'a'
            and pd.Product_IDNo = " . $data->productId . "
            and pd.PurchaseDetails_branchID = " . $this->brunch . "
            
            UNION
            select 
                'b' as sequence,
                sd.SaleDetails_SlNo as id,
                sm.SaleMaster_SaleDate as date,
                concat('Sale - ', sm.SaleMaster_InvoiceNo, ' - ', c.Customer_Name) as description,
                sd.SaleDetails_Rate as rate,
                0 as in_quantity,
                sd.SaleDetails_TotalQuantity as out_quantity
            from tbl_saledetails sd
            join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
            join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sd.Status = 'a'
            and sd.Product_IDNo = " . $data->productId . "
            and sd.SaleDetails_BranchId = " . $this->brunch . "
            
            UNION
            select 
                'c' as sequence,
                prd.PurchaseReturnDetails_SlNo as id,
                pr.PurchaseReturn_ReturnDate as date,
                concat('Purchase Return - ', pr.PurchaseMaster_InvoiceNo, ' - ', s.Supplier_Name) as description,
                (prd.PurchaseReturnDetails_ReturnAmount / prd.PurchaseReturnDetails_ReturnQuantity) as rate,
                0 as in_quantity,
                prd.PurchaseReturnDetails_ReturnQuantity as out_quantity
            from tbl_purchasereturndetails prd
            join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
            join tbl_supplier s on s.Supplier_SlNo = pr.Supplier_IDdNo
            where prd.Status = 'a'
            and prd.PurchaseReturnDetailsProduct_SlNo = " . $data->productId . "
            and prd.PurchaseReturnDetails_brachid = " . $this->brunch . "
            
            UNION
            select
                'd' as sequence, 
                srd.SaleReturnDetails_SlNo as id,
                sr.SaleReturn_ReturnDate as date,
                concat('Sale Return - ', sr.SaleMaster_InvoiceNo, ' - ', c.Customer_Name) as description,
                (srd.SaleReturnDetails_ReturnAmount / srd.SaleReturnDetails_ReturnQuantity) as rate,
                srd.SaleReturnDetails_ReturnQuantity as in_quantity,
                0 as out_quantity
            from tbl_salereturndetails srd
            join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
            join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
            join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where srd.Status = 'a'
            and srd.SaleReturnDetailsProduct_SlNo = " . $data->productId . "
            and srd.SaleReturnDetails_brunchID = " . $this->brunch . "
            
            UNION
            select
                'e' as sequence, 
                trd.transferdetails_id as id,
                tm.transfer_date as date,
                concat('Transferred From: ', b.Brunch_name, ' - ', tm.note) as description,
                0 as rate,
                trd.quantity as in_quantity,
                0 as out_quantity
            from tbl_transferdetails trd
            join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
            join tbl_brunch b on b.brunch_id = tm.transfer_from
            where trd.product_id = " . $data->productId . "
            and tm.transfer_to = " . $this->brunch . "
            
            UNION
            select 
                'f' as sequence,
                trd.transferdetails_id as id,
                tm.transfer_date as date,
                concat('Transferred To: ', b.Brunch_name, ' - ', tm.note) as description,
                0 as rate,
                0 as in_quantity,
                trd.quantity as out_quantity
            from tbl_transferdetails trd
            join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
            join tbl_brunch b on b.brunch_id = tm.transfer_to
            where trd.product_id = " . $data->productId . "
            and tm.transfer_from = " . $this->brunch . "
            
            UNION
            select 
                'g' as sequence,
                dmd.DamageDetails_SlNo as id,
                d.Damage_Date as date,
                concat('Damaged - ', d.Damage_Description) as description,
                0 as rate,
                0 as in_quantity,
                dmd.DamageDetails_DamageQuantity as out_quantity
            from tbl_damagedetails dmd
            join tbl_damage d on d.Damage_SlNo = dmd.Damage_SlNo
            where dmd.Product_SlNo = " . $data->productId . "
            and d.Damage_brunchid = " . $this->brunch . "
            
            UNION
            select 
                'h' as sequence,
                adjd.AdjustmentDetails_SlNo as id,
                adj.Adjustment_Date as date,
                'Adjustment Add' as description,
                0 as rate,
                adjd.AdjustmentDetails_AdjustmentQuantity as in_quantity,
                0 as out_quantity
            from tbl_adjustmentdetails adjd
            left join tbl_adjustment adj on adj.Adjustment_SlNo = adjd.Adjustment_SlNo
            where adjd.Product_SlNo = " . $data->productId . "
            and adj.Adjustment_brunchid = " . $this->brunch . "
            and adj.adjustment_type = 'Add Stock'
            and adjd.status != 'd'

            UNION
            select 
                'i' as sequence,
                adjd.AdjustmentDetails_SlNo as id,
                adj.Adjustment_Date as date,
                'Adjustment Less' as description,
                0 as rate,
                0 as in_quantity,
                adjd.AdjustmentDetails_AdjustmentQuantity as out_quantity
            from tbl_adjustmentdetails adjd
            left join tbl_adjustment adj on adj.Adjustment_SlNo = adjd.Adjustment_SlNo
            where adjd.Product_SlNo = " . $data->productId . "
            and adj.Adjustment_brunchid = " . $this->brunch . "
            and adj.adjustment_type = 'Less Stock'
            and adjd.status != 'd'

            order by date, sequence, id
        ")->result();

        $ledger = array_map(function ($key, $row) use ($result) {
            $row->stock = $key == 0 ? $row->in_quantity - $row->out_quantity : ($result[$key - 1]->stock + ($row->in_quantity - $row->out_quantity));
            return $row;
        }, array_keys($result), $result);

        $previousRows = array_filter($ledger, function ($row) use ($data) {
            return $row->date < $data->dateFrom;
        });

        $previousStock = empty($previousRows) ? 0 : end($previousRows)->stock;

        $ledger = array_filter($ledger, function ($row) use ($data) {
            return $row->date >= $data->dateFrom && $row->date <= $data->dateTo;
        });

        echo json_encode(['ledger' => $ledger, 'previousStock' => $previousStock]);
    }

    public function exportExcelStockRecord($date, $type)
    {
        $branchId = $this->brunch;
        if ($type == 'total') {
            $stock = $this->db->query("
                select
                    p.*,
                    pc.ProductCategory_Name,
                    b.brand_name,
                    u.Unit_Name,
                    (select ifnull(sum(pd.PurchaseDetails_TotalQuantity), 0) 
                        from tbl_purchasedetails pd 
                        join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
                        where pd.Product_IDNo = p.Product_SlNo
                        and pd.PurchaseDetails_branchID = '$branchId'
                        and pd.Status = 'a'
                        " . (isset($date) && $date != null ? " and pm.PurchaseMaster_OrderDate <= '$date'" : "") . "
                    ) as purchased_quantity,
                            
                    (select ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) 
                        from tbl_purchasereturndetails prd 
                        join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                        where prd.PurchaseReturnDetailsProduct_SlNo = p.Product_SlNo
                        and prd.PurchaseReturnDetails_brachid = '$branchId'
                        " . (isset($date) && $date != null ? " and pr.PurchaseReturn_ReturnDate <= '$date'" : "") . "
                    ) as purchase_returned_quantity,
                            
                    (select ifnull(sum(sd.SaleDetails_TotalQuantity), 0) 
                        from tbl_saledetails sd
                        join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
                        where sd.Product_IDNo = p.Product_SlNo
                        and sd.SaleDetails_BranchId  = '$branchId'
                        and sd.Status = 'a'
                        " . (isset($date) && $date != null ? " and sm.SaleMaster_SaleDate <= '$date'" : "") . "
                    ) as sold_quantity,
                            
                    (select ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0)
                        from tbl_salereturndetails srd 
                        join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                        where srd.SaleReturnDetailsProduct_SlNo = p.Product_SlNo
                        and srd.SaleReturnDetails_brunchID = '$branchId'
                        " . (isset($date) && $date != null ? " and sr.SaleReturn_ReturnDate <= '$date'" : "") . "
                    ) as sales_returned_quantity,
                            
                    (select ifnull(sum(dmd.DamageDetails_DamageQuantity), 0) 
                        from tbl_damagedetails dmd
                        join tbl_damage dm on dm.Damage_SlNo = dmd.Damage_SlNo
                        where dmd.Product_SlNo = p.Product_SlNo
                        and dmd.status = 'a'
                        and dm.Damage_brunchid = '$branchId'
                        " . (isset($date) && $date != null ? " and dm.Damage_Date <= '$date'" : "") . "
                    ) as damaged_quantity,
                
                    (select ifnull(sum(trd.quantity), 0)
                        from tbl_transferdetails trd
                        join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                        where trd.product_id = p.Product_SlNo
                        and tm.transfer_from = '$branchId'
                        " . (isset($date) && $date != null ? " and tm.transfer_date <= '$date'" : "") . "
                    ) as transferred_from_quantity,

                    (select ifnull(sum(trd.quantity), 0)
                        from tbl_transferdetails trd
                        join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                        where trd.product_id = p.Product_SlNo
                        and tm.transfer_to = '$branchId'
                        " . (isset($date) && $date != null ? " and tm.transfer_date <= '$date'" : "") . "
                    ) as transferred_to_quantity,

                    (select ifnull(sum(adjd.AdjustmentDetails_AdjustmentQuantity), 0)
                        from tbl_adjustmentdetails adjd
                        left join tbl_adjustment adj on adj.Adjustment_SlNo = adjd.Adjustment_SlNo
                        where adjd.Product_SlNo = p.Product_SlNo
                        and adj.adjustment_type = 'Add Stock'
                        and adjd.status != 'd'
                        " . (isset($date) && $date != null ? " and adj.Adjustment_Date <= '$date'" : "") . "
                    ) as adjustment_add_qty,

                    (select ifnull(sum(adjd.AdjustmentDetails_AdjustmentQuantity), 0)
                        from tbl_adjustmentdetails adjd
                        left join tbl_adjustment adj on adj.Adjustment_SlNo = adjd.Adjustment_SlNo
                        where adjd.Product_SlNo = p.Product_SlNo
                        and adj.adjustment_type = 'Less Stock'
                        and adjd.status != 'd'
                        " . (isset($date) && $date != null ? " and adj.Adjustment_Date <= '$date'" : "") . "
                    ) as adjustment_less_qty,
                            
                    (select (purchased_quantity + sales_returned_quantity + transferred_to_quantity + adjustment_add_qty) - (sold_quantity + purchase_returned_quantity + damaged_quantity + transferred_from_quantity + adjustment_less_qty)) as current_quantity,
                    (select p.Product_Purchase_Rate * current_quantity) as stock_value
                from tbl_product p
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                left join tbl_brand b on b.brand_SiNo = p.brand
                left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where p.status = 'a' and p.is_service = 'false'
            ")->result();

            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Product Id');
            $sheet->setCellValue('B1', 'Product Name');
            $sheet->setCellValue('C1', 'Category');
            $sheet->setCellValue('D1', 'Purchased Quantity');
            $sheet->setCellValue('E1', 'Purchase Returned Quantity');
            $sheet->setCellValue('F1', 'Damaged Quantity');
            $sheet->setCellValue('G1', 'Sold Quantity');
            $sheet->setCellValue('H1', 'Sales Returned Quantity');
            $sheet->setCellValue('I1', 'Transferred In Quantity');
            $sheet->setCellValue('J1', 'Transferred Out Quantity');
            $sheet->setCellValue('K1', 'Adjustment Add');
            $sheet->setCellValue('L1', 'Adjustment Less');
            $sheet->setCellValue('M1', 'Current Quantity');
            $sheet->setCellValue('N1', 'Rate');
            $sheet->setCellValue('O1', 'Stock Value');

            $rows = 2;

            foreach ($stock as  $val) {
                $sheet->setCellValue('A' . $rows, $val->Product_Code);
                $sheet->setCellValue('B' . $rows, $val->Product_Name);
                $sheet->setCellValue('C' . $rows, $val->ProductCategory_Name);
                $sheet->setCellValue('D' . $rows, $val->purchased_quantity);
                $sheet->setCellValue('E' . $rows, $val->purchase_returned_quantity);
                $sheet->setCellValue('F' . $rows, $val->damaged_quantity);
                $sheet->setCellValue('G' . $rows, $val->sold_quantity);
                $sheet->setCellValue('H' . $rows, $val->sales_returned_quantity);
                $sheet->setCellValue('I' . $rows, $val->transferred_to_quantity);
                $sheet->setCellValue('J' . $rows, $val->transferred_from_quantity);
                $sheet->setCellValue('K' . $rows, $val->adjustment_add_qty);
                $sheet->setCellValue('L' . $rows, $val->adjustment_less_qty);
                $sheet->setCellValue('M' . $rows, $val->current_quantity);
                $sheet->setCellValue('N' . $rows, $val->Product_Purchase_Rate);
                $sheet->setCellValue('O' . $rows, $val->stock_value);
                $rows++;
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . 'TotalStockRecord.xlsx');
            $writer->save('php://output');
        } else {
            $stock = $this->mt->currentStock();

            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Product Id');
            $sheet->setCellValue('B1', 'Product Name');
            $sheet->setCellValue('C1', 'Category');
            $sheet->setCellValue('D1', 'Current Quantity');
            $sheet->setCellValue('E1', 'Rate');
            $sheet->setCellValue('F1', 'Stock Value');

            $rows = 2;

            foreach ($stock as  $val) {
                $sheet->setCellValue('A' . $rows, $val->Product_Code);
                $sheet->setCellValue('B' . $rows, $val->Product_Name);
                $sheet->setCellValue('C' . $rows, $val->ProductCategory_Name);
                $sheet->setCellValue('D' . $rows, $val->current_quantity);
                $sheet->setCellValue('E' . $rows, $val->Product_Purchase_Rate);
                $sheet->setCellValue('F' . $rows, $val->stock_value);
                $rows++;
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . 'CurrentStockRecord.xlsx');
            $writer->save('php://output');
        }
    }
}

<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class AuthModel extends CI_Model{

    public function Save($table,$data)
    {
        $this->db->insert($table,$data);
        return $this->db->insert_id();
    
    }
    function getCompanyDetails($table,$company_name){
        $query=$this->db->query("select sk_organization_id  from $table where organization_name='$company_name' and organization_status='1'");
        $result=$query->result();
        return $result;
    }
    public function validate_email($table,$email){
        $sql = "SELECT * FROM $table where email='$email'";
            $binds = array($email);
            $query = $this->db->query($sql, $binds);
            if ($query->num_rows() > 0)
            {
                return $query->result();
            }
            else
                return false;
    }

    public function NewCheckUser($email,$password,$table) {

        $query=$this->db->query("select * from $table where email='$email' && user_password='$password'");
        $result=$query->result();
        return $result;
       
        
        }
    public function getRoleDetails($table,$role_id){
        $query=$this->db->query("select role_name from $table where sk_role_id ='$role_id' and role_status='1'");
        $result=$query->result();
        return $result;
    }
    function validate_industry($data){
        $query=$this->db->query("select sk_industry_id from sup_industry where industry='$data' and industry_status='1'");
        $result=$query->result();
        return $result;
    }
    function validate_state($data){
       $query= $this->db->query("select sk_state_id from sup_geo_state where state='$data' and state_status='1'");
        $result=$query->result();
        return $result;
    }
    function validate_country($data){
       $query= $this->db->query("select sk_country_id from sup_geo_country where country='$data' and country_status='1'");
        $result=$query->result();
        return $result;
    }
    function validate_date_format($data){
       $query= $this->db->query("select sk_dateformat_id from sup_dateformat where dateformat='$data' and dateformat_status='1'");
        $result=$query->result();
        return $result;
    }
    function validate_time_zone($data){
        $query=$this->db->query("select sk_timezone_id from sup_timezone where timezone='$data' and timezone_status='1'");
        $result=$query->result();
        return $result;
    }
    function validate_report_basis($data){
       $query= $this->db->query("select sk_report_basis_id from sup_report_basis where report_basis='$data' and report_basis_status='1'");
        $result=$query->result();
        return $result;
    }
    function validate_financial_year($data){
        $query= $this->db->query("select sk_financial_year_id from sup_financial_year where financial_year='$data' and financial_year_status='1'");
        $result=$query->result();
        return $result;
    }
    // function updateOrganization($data,$org_id){
    //     $this->db->where('sk_organization_id ',$org_id);
    //     $this->db->update('mst_organization',$data);
    //    return $this->db->affected_rows();
    // }
    function get_customer_details($client_id){
        $query= $this->db->query("select * from mst_organization_client where sk_client_id='$client_id' and client_status='1'");
        $result=$query->result();
        return $result;
    }
    function get_customer_address($client_id){
        $query= $this->db->query("select * from mst_organization_client_address where client_id='$client_id' and address_status='1'");
        $result=$query->result();
        return $result;
    }
    function get_customer_contact($client_id){
        $query= $this->db->query("select * from mst_organization_client_contact where client_id='$client_id' and contact_status='1'");
        $result=$query->result();
        return $result;
    }
    function get_customer_other_details($client_id){
        $query= $this->db->query("select * from mst_organization_client_other_details where client_id='$client_id' and detail_status='1'");
        $result=$query->result();
        return $result;
    }
    function updateData($table,$data,$client_id){
        $this->db->where($client_id);
        $this->db->update($table,$data);
       return $this->db->affected_rows();
    }
    function getallCustomers($user_type){
        $query= $this->db->query("select sk_client_id,primary_contact,company_name,email,work_phone,mst_organization_client_other_details.opening_balance from mst_organization_client join mst_organization_client_other_details on mst_organization_client.sk_client_id=mst_organization_client_other_details.client_id where user_type='$user_type'");
        $result=$query->result();
        return $result;
    }
    function getbilldetailsByOrganizationId($organization_id){
        $query= $this->db->query("select *,mst_organization_client.`primary_contact` from txn_bill_sales join mst_organization_client on txn_bill_sales.client_id= mst_organization_client.sk_client_id where txn_bill_sales.organization_id='$organization_id'");
        $result=$query->result();
        return $result;
    }
    
    function getbilldetailsByclientId($client_id){
        $query= $this->db->query("select *,mst_organization_client.primary_contact from txn_bill_sales join mst_organization_client on txn_bill_sales.client_id= mst_organization_client.sk_client_id where txn_bill_sales.sk_bill_id='$client_id'");
        $result=$query->result();
        return $result;
    }
    
    function get_cust_bill_details($bill_id){
        $query= $this->db->query("select * from txn_bill_sales_detail where bill_id='$bill_id'");
        $result=$query->result();
        return $result;
    }
    function getitemsbyorg($org){
        $query= $this->db->query("select * from mst_items where organization_id='$org'");
        $result=$query->result();
        return $result;
    }
    function getorgdetails($user_id){
        $query= $this->db->query("select mst_organization_user.email,`sk_organization_id`,`organization_name`,`organization_logo`,sup_industry.industry,sup_industry.sk_industry_id,sup_timezone.sk_timezone_id,sup_dateformat.sk_dateformat_id,sup_geo_state.sk_state_id,sup_geo_country.sk_country_id,sup_report_basis.sk_report_basis_id,sup_financial_year.sk_financial_year_id, sup_geo_country.country,`address_1`,`address_2`,`city`,sup_geo_state.state,`postalcode`,mst_organization.`mobile`,`fax`,`website`,`billing_address_1`,`billing_address_2`,sup_financial_year.financial_year,sup_report_basis.report_basis,sup_timezone.timezone,sup_dateformat.dateformat,`date_format_seperator`,`organization_status` from mst_organization join mst_organization_user on mst_organization_user.organization_id=mst_organization.sk_organization_id join sup_geo_state on mst_organization.state_id=sup_geo_state.sk_state_id join sup_geo_country on mst_organization.country_id=sup_geo_country.sk_country_id join sup_timezone on mst_organization.timezone_id=sup_timezone.sk_timezone_id join sup_dateformat on mst_organization.date_format=sup_dateformat.sk_dateformat_id join sup_industry on mst_organization.industry_id=sup_industry.sk_industry_id join sup_financial_year on mst_organization.financial_year=sup_financial_year.sk_financial_year_id join sup_report_basis on mst_organization.report_basis=sup_report_basis.sk_report_basis_id where mst_organization_user.sk_user_id='$user_id'");
        $result=$query->result();
        return $result;
    }
    function getdetails($table){
        $query= $this->db->query("select * from $table");
        $result=$query->result();
        return $result;
    }
    function getUserEmail($user_id){
        $query= $this->db->query("select * from mst_organization_user where sk_user_id='$user_id'");
        $result=$query->result();
        return $result;
    }
    function getitemDetails($item_id){
        $query= $this->db->query("select * from mst_items where sk_item_id='$item_id'");
        $result=$query->result();
        return $result;
    }
    function getitem($organization_id){
        $query= $this->db->query("select * from mst_items where organization_id='$organization_id'");
        $result=$query->result();
        return $result;
    }
}
?>
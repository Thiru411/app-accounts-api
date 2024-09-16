<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class Auth extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		 $this->load->library("session");
		 $this->load->library('common');
		 $this->load->helper("url");
		//$this->load->helper("jwt_helper");
		$this->load->model("AuthModel");
		$this->load->helper('date');
	}

public function index() {
	echo "hello";
}

/***********************signup Api*************************/

public function Signup() {
    $ret=array();
	$params = array();
	$data=array();
	$access_token=false;
	$row=$this->input->request_headers(); 
	if(isset($row['accessToken']))
	{
			$access_token=$row['accessToken'];
	}
	if($access_token){
			try {
					if($access_token==globalAccessToken) {
	$params = json_decode(@file_get_contents('php://input'),TRUE);

	if(isset($params)) {
		$full_Name = ""; $email = ""; $mobile = ""; $password="";$company_name="";
		if(isset($params['full_Name'])) { $full_Name = $params['full_Name'];}
		if(isset($params['email'])) { $email = $params['email'];}
		if(isset($params['mobile'])) { $mobile = $params['mobile'];}
		if(isset($params['password'])) { $password = $params['password'];}
		if(isset($params['company_name'])) { $company_name = $params['company_name'];}

try{

		if($full_Name != "" && $email != "" && $mobile != "" && $password != "" && $company_name != "") {
			$companyId = $this->AuthModel->getCompanyDetails('mst_organization',$company_name);
		if(!empty($companyId)){
		$company_Id=$companyId[0]->sk_organization_id;
			//date_default_timezone_set("Asia/kolkata");
			//echo date("h:i:sa");
			$data = array(
				'full_name'=>$full_Name,
				'email'=>$email,
				'mobile'=>$mobile,
				'user_password'=>md5($password),
				'organization_id'=>$company_Id,
				'role_id'=>1,
				'user_status'=>1,
				'record_create'=>date("Y-m-d") ,
				'record_create_time'=>date("h:i:sa")
			);

		$validate_data=$this->AuthModel->validate_email('mst_organization_user',$email);
		if(empty($validate_data)){
			$SaveData = $this->AuthModel->Save('mst_organization_user',$data);

			if($SaveData >0) {
				$SaveData=$this->common->encryption($SaveData);
				$output=array("UserAccessToken"=>$SaveData);
				$ret=$this->common->response(200,true,'User Registration Successfull',$output);
			} else {

				$ret=$this->common->response(200,false,'User Registration Unsuccessfull',$data);

			}
		}
				else{
					$ret=$this->common->response(200,false,'User already Registered',array());
				}
			} 
			else{
				$ret=$this->common->response(200,false,'Please Enter Correct Company Name',array());
			}
		}	else {
				$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
				}
			}catch (Exception $e) {
					$ret=$this->common->response(200,false,'somthing went wrong',array());
					}
				} 

			else {
				$ret=$this->common->response(200,false,'please Give Input',array());
			}
		}
		else {
			$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
		}
	}
		catch (Exception $e) {
			$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
			}
			}
			else {
				$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
			}
		echo json_encode($ret);
		}

		/********************************signinApi **********************/
		public function signin() {
			$params = array();
			$ret=array();
			$data=array();
			$access_token=false;
			$row=$this->input->request_headers(); 
			if(isset($row['accessToken']))
			{
					$access_token=$row['accessToken'];
			}
			if($access_token){
				try {
						if($access_token==globalAccessToken) {
							$params = json_decode(@file_get_contents('php://input'),TRUE);
		
										if(isset($params)) {
										$email = ""; $password="";
										if(isset($params['email'])) { $email = $params['email'];}
										if(isset($params['password'])) { $password = $params['password'];}
									try{
											if($email != "" && $password != "") {
											$CheckUser = $this->AuthModel->NewCheckUser($email,md5($password),'mst_organization_user');
											if(!empty($CheckUser)) {
											
												$sk_user_id = $CheckUser[0]->sk_user_id;
												//$first_name=$CheckUser[0]->full_name;
												//$email=$CheckUser[0]->email;
												$role_id=$CheckUser[0]->role_id;
												$roleDetails=$this->AuthModel->getRoleDetails('sup_role',$role_id);
												$roleName=$roleDetails[0]->role_name;
												$organization_id=$CheckUser[0]->organization_id;
												$encrypted_id=$this->common->encryption($sk_user_id);
												$Output=array("userAccessToken"=>$encrypted_id,"organization_id"=>$organization_id,"Roll_Id"=>$role_id,"Roll_name"=>$roleName);
												$ret=$this->common->response(200,true,'login Success',$Output);
											}
											else {
												$ret=$this->common->response(200,false,'Please check your email and password',array());
											}
											}else {
												$ret=$this->common->response(200,false,'Please check your input like key and value',array());
											}
										}catch(exception $e){
											$ret=$this->common->response(200,false,'Please Enter email and password',array());
										}
									}else{
										$ret=$this->common->response(200,false,'Please Enter Inputs',array());
									}
								}else {
									$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
								}
							}catch (Exception $e) {
								$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
							}
						}else {
							$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
						}
					echo json_encode($ret);
		}

	/**************************organization Update api************************************/


	public function organization_update(){
		$ret=array();
		$params = array();
		$data=array();
		$access_token=false;
		$row=$this->input->request_headers(); 
		if(isset($row['accessToken']))
		{
				$access_token=$row['accessToken'];
		}
		if($access_token){
				try {
						if($access_token==globalAccessToken) {
		$params = json_decode(@file_get_contents('php://input'),TRUE);
							//var_dump($params);
		if(isset($params)) {
			$organization_id="";$organization_name = ""; $logo=""; $industry = ""; $business_loc = ""; $sub_address1=""; $sub_address2="";
			$district="";$state="";  $pin_code=""; $mobile=""; $fax=""; $web_site="";$billing_address1="";$billing_address2="";$primary_contact="";
			$fiscal_year="";$report_basis="";$time_zone="";$date_format="";$company_id="";$tax_id="";$date_format_seperator="";$country_id="";
			if(isset($params['organization_id'])) {$organization_id = $params['organization_id'];}
			if(isset($params['organization_name'])) {$organization_name = $params['organization_name'];}
			if(isset($params['organization_logo'])) { $logo = $params['organization_logo'];}
			if(isset($params['industry_id'])) { $industry = $params['industry_id'];}
			//if(isset($params['business_loc'])) { $business_loc = $params['business_loc'];}
			if(isset($params['address_1'])) { $sub_address1 = $params['address_1'];}
			if(isset($params['address_2'])) { $sub_address2 = $params['address_2'];}
			if(isset($params['city'])) { $district = $params['city'];}
			if(isset($params['state_id'])) { $state = $params['state_id'];}
			if(isset($params['postalcode'])) { $pin_code = $params['postalcode'];}
			if(isset($params['mobile'])) { $mobile = $params['mobile'];}
			if(isset($params['fax'])) { $fax = $params['fax'];}
			if(isset($params['website'])) { $web_site = $params['website'];}
			//if(isset($params['billing_address1'])) { $billing_address1 = $params['billing_address1'];}
			//if(isset($params['billing_address2'])) { $billing_address2 = $params['billing_address2'];}
			//if(isset($params['primary_contact'])) { $primary_contact = $params['primary_contact'];}
			if(isset($params['financial_year'])) { $fiscal_year = $params['financial_year'];}
			if(isset($params['report_basis'])) { $report_basis = $params['report_basis'];}
			if(isset($params['timezone_id'])) { $time_zone = $params['timezone_id'];}
			if(isset($params['date_format_id'])) { $date_format = $params['date_format_id'];}
			if(isset($params['date_format_seperator'])) { $date_format_seperator = $params['date_format_seperator'];}
			if(isset($params['country_id'])) { $country_id = $params['country_id'];}
			//if(isset($params['tax_type'])) { $tax_type = $params['tax_type'];}
			//if(isset($params['tax_id'])) { $tax_id = $params['tax_id'];}
	try{
		//var_dump($params);exit();
			if($organization_id!="") {
				//var_dump($params);exit();
				$organizationdata = array(
					"organization_name"=>$organization_name,
					"organization_logo"=>$logo,
					"industry_id"=>$industry,
					"country_id"=>$country_id,
					"address_1"=>$sub_address1,
					"address_2"=>$sub_address2,
					"city"=>$district,
					"state_id"=>$state,
					"postalcode"=>$pin_code,
					"mobile"=>$mobile,
					"fax"=>$fax,
					// "website"=>$web_site,
					// "billing_address_1"=>$billing_address1,
					// "billing_address_2"=>$billing_address2,
					// "sender_email"=>$primary_contact,
					"financial_year"=>$fiscal_year,
					"report_basis"=>$report_basis,
					"timezone_id"=>$time_zone,
					"date_format"=>$date_format,
					"date_format_seperator"=>$date_format_seperator,
					"organization_status"=>1
					//"subscription_id"=>1
					//"subscription_end_date"=>
				);
				//var_dump($organizationdata);
				$organization_id=array("sk_organization_id"=>$organization_id);
				$UpdateResult=$this->AuthModel->updateData("mst_organization",$organizationdata,$organization_id);
			//	var_dump($UpdateResult);
				if(!empty($UpdateResult)){
					$ret=$this->common->response(200,true,'successfully Updated',array());
				}
				else{
					$ret=$this->common->response(200,false,'no changes made in row',array());
				}
			}	else {
					$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
					}
				}catch (Exception $e) {
						$ret=$this->common->response(200,false,'somthing went wrong',array());
						}
					} 
	
				else {
					$ret=$this->common->response(200,false,'please Give Input',array());
				}
			}
			else {
				$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
			}
		}
			catch (Exception $e) {
				$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
				}
				}
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			echo json_encode($ret);
	}
	
	/*****************************add customer detrails api**************************/
	public function add_custumer(){
		$ret=array();
		$params = array();
		$data=array();
		$access_token=false;
		$row=$this->input->request_headers(); 
		if(isset($row['accessToken']))
		{
				$access_token=$row['accessToken'];
		}
		if($access_token){
				try {
						if($access_token==globalAccessToken) {
		$params = json_decode(@file_get_contents('php://input'),TRUE);
	
		if(isset($params)) {
			$organization_id="";$customer_type = "";$sky_number="";$desigantion="";$department="";$portal_lang="";
			$facebook="";$twitter="";$last_name=""; $primary_contact_salutation = ""; $full_name = ""; $company_name="";$customer_display_name="";
			$customer_email=""; $customer_work_phone="";$customer_mobile="";$customer_website="";$gst_treatment="";
			$source_of_supply="";$currency="";$opening_balance="";$tds="";$address_type="";$country_name="";$address_1="";
			$address_2="";$city="";$state="";$postalcode="";$phone="";$fax="";$designation="";$user_type="";
			if(isset($params['organization_id'])) { $organization_id = $params['organization_id'];}

			if(isset($params['customer_type'])) { $customer_type = $params['customer_type'];}
			if(isset($params['primary_contact_salutation'])) { $primary_contact_salutation = $params['primary_contact_salutation'];}
			if(isset($params['full_name'])) { $full_name = $params['full_name'];}
			if(isset($params['company_name'])) { $company_name = $params['company_name'];}
			if(isset($params['last_name'])) { $last_name = $params['last_name'];}
			if(isset($params['customer_display_name'])) { $customer_display_name = $params['customer_display_name'];}
			if(isset($params['customer_email'])) { $customer_email = $params['customer_email'];}
			if(isset($params['customer_work_phone'])) { $customer_work_phone = $params['customer_work_phone'];}
			if(isset($params['customer_mobile'])) { $customer_mobile = $params['customer_mobile'];}
			if(isset($params['customer_website'])) { $customer_website = $params['customer_website'];}
			if(isset($params['gst_treatment'])) { $gst_treatment = $params['gst_treatment'];}
			if(isset($params['source_of_supply'])) { $source_of_supply = $params['source_of_supply'];}
			if(isset($params['currency'])) { $currency = $params['currency'];}
			if(isset($params['opening_balance'])) { $opening_balance = $params['opening_balance'];}
			if(isset($params['tds'])) { $tds = $params['tds'];}
			if(isset($params['payment_terms'])) { $payment_terms = $params['payment_terms'];}
			if(isset($params['address_type'])) { $address_type = $params['address_type'];}
			if(isset($params['country_name'])) { $country_name = $params['country_name'];}
			if(isset($params['address_1'])) { $address_1 = $params['address_1'];}
			if(isset($params['address_2'])) { $address_2 = $params['address_2'];}
			if(isset($params['city'])) { $city = $params['city'];}
			if(isset($params['state'])) { $state = $params['state'];}
			if(isset($params['postalcode'])) { $postalcode = $params['postalcode'];}
			if(isset($params['phone'])) { $phone = $params['phone'];}
			if(isset($params['fax'])) { $fax = $params['fax'];}
			if(isset($params['sky_number'])) { $sky_number = $params['sky_number'];}
			if(isset($params['department'])) { $department = $params['department'];}
			if(isset($params['designation'])) { $designation = $params['designation'];}
			if(isset($params['portal_lang'])) { $portal_lang = $params['portal_lang'];}
			if(isset($params['facebook'])) { $facebook = $params['facebook'];}
			if(isset($params['twitter'])) { $twitter = $params['twitter'];}
			if(isset($params['portal_access'])) { $portal_access = $params['portal_access'];}
			if(isset($params['user_type'])) { $user_type = $params['user_type'];}

			//if(isset($params['user_type'])) { $user_type = $params['user_type'];}

//var_dump($params);

//echo $user_type;exit();
		
	try{
	
			if($organization_id!="" && $customer_type != "" && $primary_contact_salutation != "" && $full_name != "" && $company_name != "" && $customer_display_name != ""
			   && $customer_email!="" && $customer_work_phone!="" && $customer_mobile!="" && $customer_website!="" &&
			   $gst_treatment!="" && $source_of_supply!="" && $currency!="" && $opening_balance!="" && $tds!=""
			   && $address_type!="" && $country_name!="" && $address_1!="" && $address_2!="" &&  $city!="" && $state!=""
			   && $postalcode!="" && $phone!="" && $fax!=""&& $user_type!="" ) {
				   
				//$companyId = $this->AuthModel->getCompanyDetails('mst_organization',$company_name);
				// $country=$this->AuthModel->validate_country($country_name);
				// $country_id=$country[0]->sk_country_id;
				date_default_timezone_set("Asia/kolkata");
			//$company_Id=$companyId[0]->sk_organization_id;
				$client_data = array(
					'organization_id'=>$organization_id,
					'client_type'=>$customer_type,
					'primary_contact_ext'=>$primary_contact_salutation,
					'primary_contact'=>$full_name,
					'last_name'=>$last_name,
					'company_name'=>$company_name,
					'dispaly_name'=>$customer_display_name,
					'email'=>$customer_email,
					'work_phone'=>$customer_work_phone,
					'work_mobile'=>$customer_mobile,
					'website'=>$customer_website,
					'skype'=>$sky_number,
					'department'=>$department,
					'designation'=>$designation,
					'client_status'=>1,
					'user_type'=>$user_type,
					'record_create_date'=>date("Y-m-d") ,
					'record_create_time'=>date("h:i:sa")
				);
				//var_dump($client_data);exit();
				$SaveData = $this->AuthModel->Save('mst_organization_client',$client_data);
				//var_dump($SaveData);
				if($SaveData >0) {
				$client_address=array(
						'client_id'=>$SaveData,
						'organization_id'=>$organization_id,
						'address_type'=>$address_type,
						'country_id'=>$country_name,
						'address_1'=>$address_1,
						'address_2'=>$address_2,
						'city'=>$city,
						'state'=>$state,
						'postalcode'=>$postalcode,
						'phone'=>$phone,
						'fax'=>$fax,
						'address_status'=>1,
						'record_create_date'=>date("Y-m-d") ,
						'record_create_time'=>date("h:i:sa")
				);
				$client_contact = array(
					'client_id'=>$SaveData,
					'organization_id'=>$organization_id,
					'salutation'=>$primary_contact_salutation,
					'full_name'=>$full_name,
					'email'=>$customer_email,
					'phone'=>$customer_work_phone,
					'mobile'=>$customer_mobile,
					'contact_status'=>1,
					'record_create_date'=>date("Y-m-d") ,
					'record_create_time'=>date("h:i:sa")
				);
				// /var_dump($client_address);
				$other_details=array(
					'client_id'=>$SaveData,
					'organization_id'=>$organization_id,
					'gst_treatment'=>$gst_treatment,
					'source_of_supply'=>$source_of_supply,
					'currency'=>$currency,
					'opening_balance'=>$opening_balance,
					'tds'=>$tds,
					'portal_lang'=>$portal_lang,
					'facebook'=>$facebook,
					'twitter'=>$twitter,
					'detail_status'=>1,
					'port_access'=>$portal_access,
					'payment_terms'=>$payment_terms,
					'record_create_date'=>date("Y-m-d") ,
					'record_create_time'=>date("h:i:sa")	
				);	
				$SaveAdreess = $this->AuthModel->Save('mst_organization_client_address',$client_address);
				$SaveOtherDetails = $this->AuthModel->Save('mst_organization_client_contact',$client_contact);
				$SaveOtherDetails = $this->AuthModel->Save('mst_organization_client_other_details',$other_details);
				
					$ret=$this->common->response(200,true,'Customer Added Successfull',array());
				} else {
	
					$ret=$this->common->response(200,false,'fail to add customer',$data);
	
				}
			}	else {
					$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
					}
				}catch (Exception $e) {
						$ret=$this->common->response(200,false,'somthing went wrong',array());
						}
					} 
	
				else {
					$ret=$this->common->response(200,false,'please Give Input',array());
				}
			}
			else {
				$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
			}
		}
			catch (Exception $e) {
				$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
				}
				}
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			echo json_encode($ret);
		}	
		
		/******************view of customer details***********************************/
		
		public function customer_details(){
		$params = array();
		$ret=array();
		$data=array();
		$access_token=false;
		$row=$this->input->request_headers(); 
		if(isset($row['accessToken']))
		{
				$access_token=$row['accessToken'];
		}
		if($access_token){
			try {
					if($access_token==globalAccessToken) {
		$params = json_decode(@file_get_contents('php://input'),TRUE);
	
		if(isset($params)) {
		$client_id="";
		if(isset($params['client_id'])) { $client_id = $params['client_id'];}
		try{
		if($client_id!="") {
			$customer_details = $this->AuthModel->get_customer_details($client_id);
		if(!empty($customer_details)){
			$client_result_id=$customer_details[0]->sk_client_id;
			$customer_address=$this->AuthModel->get_customer_address($client_result_id);
			$customer_contact=$this->AuthModel->get_customer_contact($client_result_id);
			$customer_other_details=$this->AuthModel->get_customer_other_details($client_result_id);
			$output=array("Client_details"=>$customer_details,"Customer_address"=>$customer_address,"Customer_contact"=>$customer_contact,"customer_other_details"=>$customer_other_details);
		$ret=$this->common->response(200,true,'login Success',$output);
		}
		else {
			$ret=$this->common->response(200,false,'please check client Id',array());
		}
		} else {
			$ret=$this->common->response(200,false,'Please check your input like key and value',array());
			}
			}
		catch(exception $e){
			$ret=$this->common->response(200,false,'Please Enter email and password',array());
				}
		}
		else{
			$ret=$this->common->response(200,false,'Please Enter Inputs',array());
		}
	}
		else {
		$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
		}
	}
	catch (Exception $e) {
		$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
		}
		}
		else {
			$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
		}
		echo json_encode($ret);
	}	
		
	public function all_customer_details(){
		$params = array();
		$ret=array();
		$data=array();
		$access_token=false;
		$row=$this->input->request_headers(); 
		if(isset($row['accessToken']))
		{
				$access_token=$row['accessToken'];
		}
		if($access_token){
			try {
					if($access_token==globalAccessToken) {
		$params = json_decode(@file_get_contents('php://input'),TRUE);
		if(isset($params)) {
			$user_type="";
			if(isset($params['user_type'])) { $user_type = $params['user_type'];}
			$cust_details=$this->AuthModel->getallCustomers($user_type);
			
					
				if(!empty($cust_details)){
						$output=array("Client_details"=>$cust_details);
						$ret=$this->common->response(200,true,'successfully retrieved data',$output);
				}
				else {
						$ret=$this->common->response(200,false,'No data found',array());
				}
			}
		
		else {
				$ret=$this->common->response(200,false,'No data found',array());
		}
	}
	
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			}
					catch (Exception $e) {
							$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
						}
					}
					else {
							$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
						}
						echo json_encode($ret);
	}	
		
		
		
		
		
	/*******************************update customer details*****************************/
		
	public function customer_update(){
		$ret=array();
		$params = array();
		$data=array();
		$access_token=false;
		$row=$this->input->request_headers(); 
		if(isset($row['accessToken']))
		{
				$access_token=$row['accessToken'];
		}
		if($access_token){
				try {
						if($access_token==globalAccessToken) {
		$params = json_decode(@file_get_contents('php://input'),TRUE);
	
		if(isset($params)) {
			$client_id="";$customer_type = ""; $primary_contact_salutation = ""; $full_name = ""; $company_name="";$customer_display_name="";
			$customer_email=""; $customer_work_phone="";$customer_mobile="";$customer_website="";$gst_treatment="";
			$source_of_supply="";$currency="";$opening_balance="";$tds="";$address_type="";$country_name="";$address_1="";
			$address_2="";$city="";$state="";$postalcode="";$phone="";$fax="";
			if(isset($params['client_id'])) { $client_id = $params['client_id'];}
			if(isset($params['customer_type'])) { $customer_type = $params['customer_type'];}
			if(isset($params['primary_contact_salutation'])) { $primary_contact_salutation = $params['primary_contact_salutation'];}
			if(isset($params['full_name'])) { $full_name = $params['full_name'];}
			if(isset($params['company_name'])) { $company_name = $params['company_name'];}
			if(isset($params['customer_display_name'])) { $customer_display_name = $params['customer_display_name'];}
			if(isset($params['customer_email'])) { $customer_email = $params['customer_email'];}
			if(isset($params['customer_work_phone'])) { $customer_work_phone = $params['customer_work_phone'];}
			if(isset($params['customer_mobile'])) { $customer_mobile = $params['customer_mobile'];}
			if(isset($params['customer_website'])) { $customer_website = $params['customer_website'];}
			if(isset($params['gst_treatment'])) { $gst_treatment = $params['gst_treatment'];}
			if(isset($params['source_of_supply'])) { $source_of_supply = $params['source_of_supply'];}
			if(isset($params['currency'])) { $currency = $params['currency'];}
			if(isset($params['opening_balance'])) { $opening_balance = $params['opening_balance'];}
			if(isset($params['tds'])) { $tds = $params['tds'];}
			if(isset($params['address_type'])) { $address_type = $params['address_type'];}
			if(isset($params['country_name'])) { $country_name = $params['country_name'];}
			if(isset($params['address_1'])) { $address_1 = $params['address_1'];}
			if(isset($params['address_2'])) { $address_2 = $params['address_2'];}
			if(isset($params['city'])) { $city = $params['city'];}
			if(isset($params['state'])) { $state = $params['state'];}
			if(isset($params['postalcode'])) { $postalcode = $params['postalcode'];}
			if(isset($params['phone'])) { $phone = $params['phone'];}
			if(isset($params['fax'])) { $fax = $params['fax'];}
			//var_dump($params);
	try{
	
			if($client_id!="" && $customer_type != "" && $primary_contact_salutation != "" && $full_name != "" && $company_name != "" && $customer_display_name != ""
			   && $customer_email!="" && $customer_work_phone!="" && $customer_mobile!="" && $customer_website!="" &&
			   $gst_treatment!="" && $source_of_supply!="" && $currency!="" && $opening_balance!="" && $tds!=""
			   && $address_type!="" && $country_name!="" && $address_1!="" && $address_2!="" &&  $city!="" && $state!=""
			   && $postalcode!="" && $phone!="" && $fax!="" ) {
				   
				$companyId = $this->AuthModel->getCompanyDetails('mst_organization',$company_name);
				$country=$this->AuthModel->validate_country($country_name);
				$country_id=$country[0]->sk_country_id;
			if(!empty($companyId)){
			$company_Id=$companyId[0]->sk_organization_id;
			//date_default_timezone_set("Asia/kolkata");
				$client_data = array(
					'organization_id'=>$company_Id,
					'client_type'=>$customer_type,
					'primary_contact_ext'=>$primary_contact_salutation,
					'primary_contact'=>$full_name,
					'company_name'=>$company_name,
					'dispaly_name'=>$customer_display_name,
					'email'=>$customer_email,
					'work_phone'=>$customer_work_phone,
					'work_mobile'=>$customer_mobile,
					'website'=>$customer_website,
					'client_status'=>1,
					'record_create_date'=>date("Y-m-d") ,
					'record_create_time'=>date("h:i:sa")
				);
				$client_id_data=array('sk_client_id'=>$client_id);
				$SaveData = $this->AuthModel->updateData('mst_organization_client',$client_data,$client_id_data);
				$client_address=array(
						'organization_id'=>$company_Id,
						'address_type'=>$address_type,
						'country_id'=>$country_id,
						'address_1'=>$address_1,
						'address_2'=>$address_2,
						'city'=>$city,
						'state'=>$state,
						'postalcode'=>$postalcode,
						'phone'=>$phone,
						'fax'=>$fax,
						'address_status'=>1,
						'record_create_date'=>date("Y-m-d") ,
						'record_create_time'=>date("h:i:sa")
				);
				$client_contact = array(
					'organization_id'=>$company_Id,
					'salutation'=>$primary_contact_salutation,
					'full_name'=>$full_name,
					'email'=>$customer_email,
					'phone'=>$customer_work_phone,
					'mobile'=>$customer_mobile,
					'contact_status'=>1,
					'record_create_date'=>date("Y-m-d") ,
					'record_create_time'=>date("h:i:sa")
				);
				// /var_dump($client_address);
				$other_details=array(
					'organization_id'=>$company_Id,
					'gst_treatment'=>$gst_treatment,
					'source_of_supply'=>$source_of_supply,
					'currency'=>$currency,
					'opening_balance'=>$opening_balance,
					'tds'=>$tds,
					'detail_status'=>1,
					'detail_status'=>1,
					'record_create_date'=>date("Y-m-d") ,
					'record_create_time'=>date("h:i:sa")	
				);	
				$client_id_result=array('client_id'=>$client_id);
				$SaveAdreess = $this->AuthModel->updateData('mst_organization_client_address',$client_address,$client_id_result);
				$SaveOtherDetails = $this->AuthModel->updateData('mst_organization_client_contact',$client_contact,$client_id_result);
				$SaveOtherDetails = $this->AuthModel->updateData('mst_organization_client_other_details',$other_details,$client_id_result);
				if($SaveData>0 || $SaveAdreess>0 || $SaveOtherDetails>0 ||$SaveOtherDetails>0){
					$ret=$this->common->response(200,true,'Customer Details Updated Succeessfully',array());
				} else {
	
					$ret=$this->common->response(200,false,'fail to add customer',array());
	
				}
				} 
				else{
					$ret=$this->common->response(200,false,'Please Enter Correct Company Name',array());
				}
			}	else {
					$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
					}
				}catch (Exception $e) {
						$ret=$this->common->response(200,false,'somthing went wrong',array());
						}
					} 
	
				else {
					$ret=$this->common->response(200,false,'please Give Input',array());
				}
			}
			else {
				$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
			}
		}
			catch (Exception $e) {
				$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
				}
				}
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			echo json_encode($ret);
		}
	/*******************************invoice api***********************/
		public function customer_invoice(){
			$ret=array();
			$params = array();
			$data=array();
			$access_token=false;
			$row=$this->input->request_headers(); 
			if(isset($row['accessToken']))
			{
					$access_token=$row['accessToken'];
			}
			if($access_token){
					try {
							if($access_token==globalAccessToken) {
			$params = json_decode(@file_get_contents('php://input'),TRUE);
			$organization_id="";
			if(isset($params)) {
				if(isset($params['organization_id'])) { $organization_id = $params['organization_id'];}
				//var_dump($params);
		try{
		
				if($organization_id!="") {
					   
					$getdetails=$this->AuthModel->getbilldetailsByOrganizationId($organization_id);
					$data=array('bill_details'=>$getdetails);
					if(!empty($getdetails)){
						$ret=$this->common->response(200,true,'Customer Details Updated Succeessfully',$data);
					} else {
		
						$ret=$this->common->response(200,false,'fail to add customer',array());
		
					}
				} 
						else {
						$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
						}
					}catch (Exception $e) {
							$ret=$this->common->response(200,false,'somthing went wrong',array());
							}
						} 
		
					else {
						$ret=$this->common->response(200,false,'please Give Input',array());
					}
				}
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			}
				catch (Exception $e) {
					$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
					}
					}
					else {
						$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
				echo json_encode($ret);
			}	
	
	/***************************get Invoice by client id *****************************/
	public function getdetailsbyclientid(){
		$params = array();
					$ret=array();
					$data=array();
					$access_token=false;
					$row=$this->input->request_headers(); 
					if(isset($row['accessToken']))
					{
							$access_token=$row['accessToken'];
					}
					if($access_token){
						try {
								if($access_token==globalAccessToken) {
					$params = json_decode(@file_get_contents('php://input'),TRUE);

					if(isset($params)) {
					$client_id="";
					
					if(isset($params['client_id'])) { $client_id = $params['client_id'];}
					try{
						//echo $client_id;					
						if($client_id!="") {
						$customer_details = $this->AuthModel->getbilldetailsByclientId($client_id);
						//var_dump($customer_details);
						
					if(!empty($customer_details)){
						$client_result_id=$customer_details[0]->sk_client_id;
						$bill_id=$customer_details[0]->sk_bill_id;
						$cust_bill_sales_details=$this->AuthModel->get_cust_bill_details($bill_id);
						$customer_address=$this->AuthModel->get_customer_address($client_result_id);
						$customer_contact=$this->AuthModel->get_customer_contact($client_result_id);
						$customer_other_details=$this->AuthModel->get_customer_other_details($client_result_id);
						$output=array("Client_bill_details"=>$customer_details,"customer_address"=>$customer_address,"customer_contact"=>$customer_contact,"customer_other_details"=>$customer_other_details,"cust_bill_sales_details"=>$cust_bill_sales_details);
						$ret=$this->common->response(200,true,'login Success',$output);
					}
					else {
						$ret=$this->common->response(200,false,'please check client Id',array());
					}
					} else {
						$ret=$this->common->response(200,false,'Please check your input like key and value',array());
						}
						}
					catch(exception $e){
						$ret=$this->common->response(200,false,'Please Enter email and password',array());
							}
					}
					else{
						$ret=$this->common->response(200,false,'Please Enter Inputs',array());
					}
				}
					else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
				}
				catch (Exception $e) {
					$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
					}
					}
					else {
						$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
					echo json_encode($ret);
		}    
	
	/***************************add new invoice*********************************/   
	public function add_invoice(){

	$ret=array();
	$params = array();
	$data=array();
	$access_token=false;
	$row=$this->input->request_headers(); 
	if(isset($row['accessToken']))
	{
			$access_token=$row['accessToken'];
	}
	if($access_token){
			try {
					if($access_token==globalAccessToken) {
	$params = json_decode(@file_get_contents('php://input'),TRUE);
	$client_id = ""; $invoice="";$order_number="";$invoice_date="";$Salespersone="";
	$Subject="";$sub_total="";$discount="";$discount_type="";$discount_amount="";$adjustment="";
	$total_tax="";$grand_total="";$customer_note="";$terms_conditions="";$attachment="";$bill_template="";
	$bill_status="";$payment_status=""; $item_id="";$quantity="";$rate="";
	$amount=""; $sales_detail_status=""; $organization_id="";$due_date="";$hsn="";
//var_dump($params);
	if(isset($params)) {
		if(isset($params['organization_id'])) { $organization_id = $params['organization_id'];}
		if(isset($params['client_id'])) { $client_id = $params['client_id'];}
		if(isset($params['invoice'])) { $invoice = $params['invoice'];}
		if(isset($params['order_number'])) { $order_number = $params['order_number'];}
		if(isset($params['invoice_date'])) { $invoice_date = $params['invoice_date'];}
		if(isset($params['Salespersone'])) { $Salespersone = $params['Salespersone'];}
		if(isset($params['due_date'])) { $due_date = $params['due_date'];}
		if(isset($params['Subject'])) { $Subject = $params['Subject'];}
		if(isset($params['sub_total'])) { $sub_total = $params['sub_total'];}
		if(isset($params['discount'])) { $discount = $params['discount'];}
		if(isset($params['discount_type'])) { $discount_type = $params['discount_type'];}
		if(isset($params['discount_amount'])) { $discount_amount = $params['discount_amount'];}
		if(isset($params['adjustment'])) { $adjustment = $params['adjustment'];}
		if(isset($params['total_tax'])) { $total_tax = $params['total_tax'];}
		if(isset($params['grand_total'])) { $grand_total = $params['grand_total'];}
		if(isset($params['customer_note'])) { $customer_note = $params['customer_note'];}
		if(isset($params['terms_conditions'])) { $terms_conditions = $params['terms_conditions'];}
		if(isset($params['attachment'])) { $attachment = $params['attachment'];}
		if(isset($params['bill_template'])) { $bill_template = $params['bill_template'];}
		if(isset($params['bill_status'])) { $bill_status = $params['bill_status'];}
		//if(isset($params['payment_status'])) { $payment_status = $params['payment_status'];}
		if(isset($params['item_id'])) { $item_id = $params['item_id'];}
		if(isset($params['item_description'])) { $item_description = $params['item_description'];}
		if(isset($params['hsn'])) { $hsn = $params['hsn'];}
		if(isset($params['quantity'])) { $quantity = $params['quantity'];}
		if(isset($params['rate'])) { $rate = $params['rate'];}
		if(isset($params['tax_group'])) { $tax_group = $params['tax_group'];}
		if(isset($params['amount'])) { $amount = $params['amount'];}
		if(isset($params['sales_detail_status'])) { $sales_detail_status = $params['sales_detail_status'];}

try{



		if($client_id!="" && $invoice!="" && $order_number!="" && $invoice_date!="" && $Salespersone!="" && $due_date!=""
		   && $Subject!="" && $sub_total!="" && $discount!="" && $discount_type!="" &&
		   $discount_amount!="" && $adjustment!="" && $total_tax!="" && $grand_total!="" && $customer_note!=""
		   && $terms_conditions!="" && $attachment!="" && $bill_template!="" && $organization_id!="" && $bill_status!="" &&
		    $item_id!="" &&  $quantity!="" && $rate!="" && 
		   $amount!="") {
			  //echo $payment_status; 
			
			$data = array(
				'organization_id'=>$organization_id,
				'client_id'=>$client_id,
				'invoice_no'=>$invoice,
				'order_no'=>$order_number,
				'invoice_date'=>$invoice_date,
				//'Salespersone'=>$Salespersone,
				'due_date'=>$due_date,
				'Subject'=>$Subject,
				'sub_total'=>$sub_total,
				'discount'=>$discount,
				'discount_type'=>$discount_type,
				'discount_amount'=>$discount_amount,
				'adjustment'=>$adjustment,
				'total_tax'=>$total_tax,
				'grand_total'=>$grand_total,
				'customer_note'=>$customer_note,
				'terms_conditions'=>$terms_conditions,
				'attachment'=>$attachment,
				'bill_template'=>$bill_template,
				'bill_status'=>$bill_status,
				'payment_status'=>$payment_status,
				'record_create_date'=>date("Y-m-d") ,
				'record_create_time'=>date("h:i:sa")
			);
			//var_dump($data);exit();
			$SaveData = $this->AuthModel->Save('txn_bill_sales',$data);
			//var_dump($SaveData);
			if($SaveData >0) {
				$data1=array(
				'client_id'=>$client_id,
				'organization_id'=>$organization_id,
                 'bill_id'=>$SaveData,
				'item_id'=>$item_id,
				'item_description'=>$item_description,
				'hsn'=>$hsn,
				'qty'=>$quantity,
				'rate'=>$rate,
				'discount'=>$discount,
				'tax_group'=>$tax_group,
				'amount'=>$amount,
				'sales_detail_status'=>1,
				'record_create_date'=>date("Y-m-d"),
				'record_create_time'=>date("h:i:sa")
			);
			//var_dump($data);exit();
			//$SaveData = $this->AuthModel->Save('txn_bill_sales',$data);
			$SaveData= $this->AuthModel->Save('txn_bill_sales_detail',$data1);
			//var_dump($SaveData);exit();



				$ret=$this->common->response(200,true,'Customer Added Successfull',$SaveData);
			} else {

				$ret=$this->common->response(200,false,'fail to add customer',$data);

			}
			
		}	else {
				$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
				}
			}catch (Exception $e) {
					$ret=$this->common->response(200,false,'somthing went wrong',array());
					}
				} 

			else {
				$ret=$this->common->response(200,false,'please Give Input',array());
			}
		}
		else {
			$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
		}
	}
		catch (Exception $e) {
			$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
			}
		}
			else {
				$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
			}
		echo json_encode($ret);

		}
	public function all_items(){
		$ret=array();
			$params = array();
			$data=array();
			$access_token=false;
			$row=$this->input->request_headers(); 
			if(isset($row['accessToken']))
			{
					$access_token=$row['accessToken'];
			}
			if($access_token){
					try {
							if($access_token==globalAccessToken) {
			$params = json_decode(@file_get_contents('php://input'),TRUE);
			$organization_id="";
			if(isset($params)) {
				if(isset($params['oragnization_id'])) { $organization_id = $params['oragnization_id'];}
				//var_dump($params);
		try{
		
				if($organization_id!="") {
					   
					$getdetails=$this->AuthModel->getitemsbyorg($organization_id);
					$data=array('item_details'=>$getdetails);
					if(!empty($getdetails)){
						$ret=$this->common->response(200,true,'Customer Details Updated Succeessfully',$data);
					} else {
		
						$ret=$this->common->response(200,false,'fail to add customer',array());
		
					}
				} 
						else {
						$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
						}
					}catch (Exception $e) {
							$ret=$this->common->response(200,false,'somthing went wrong',array());
							}
						} 
		
					else {
						$ret=$this->common->response(200,false,'please Give Input',array());
					}
				}
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			}
				catch (Exception $e) {
					$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
					}
					}
					else {
						$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
				echo json_encode($ret);
			
	}
	
	
	/*****************************get organization details****************************/
	
	
	
	
	
	public function getAllDetails(){
		$params = array();
					$ret=array();
					$data=array();
					$access_token=false;
					$row=$this->input->request_headers(); 
					if(isset($row['accessToken']))
					{
							$access_token=$row['accessToken'];
					}
					if($access_token){
						try {
								if($access_token==globalAccessToken) {
					$params = json_decode(@file_get_contents('php://input'),TRUE);

						if(isset($params)) {
							$user_id="";
									
							if(isset($params['user_id'])) { $user_id = $params['user_id'];}
									
									try{
							$decrypted_user_id=$this->common->decryption($user_id);			
					 if($decrypted_user_id!="") {
						$user_details=$this->AuthModel->getUserEmail($decrypted_user_id);
						$industry = $this->AuthModel->getdetails("sup_industry");
						$countries = $this->AuthModel->getdetails("sup_geo_country");
						$states = $this->AuthModel->getdetails("sup_geo_state");
						$timezone = $this->AuthModel->getdetails("sup_timezone");
						$dateformat = $this->AuthModel->getdetails("sup_dateformat");
						$fiscal = $this->AuthModel->getdetails("sup_financial_year");
						$reportBasis = $this->AuthModel->getdetails("sup_report_basis");
						//var_dump($customer_details);
						$output=array("industry"=>$industry,
									  "user_details"=>$user_details,
									  "countries"=>$countries,
									  "states"=>$states,
									  "timezone"=>$timezone,
									  "dateformat"=>$dateformat,
									  "fiscal"=>$fiscal,
									  "reportBasis"=>$reportBasis
									);
						$ret=$this->common->response(200,true,'successfully',$output);
						}
						else{
							$ret=$this->common->response(200,false,'invalid user_id',array());
						}
							}catch(Exception $e){
							$ret=$this->common->response(200,false,'please give user_id',array());
						}
					}
							else {
							$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
							}
						}
					}
						catch (Exception $e) {
							$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
							}
							}
							else {
								$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
							}
							echo json_encode($ret);
		
			} 
	/***************************add organization******************/
	public function add_organization(){
		$ret=array();
		$params = array();
		$data=array();
		$access_token=false;
		$row=$this->input->request_headers(); 
		if(isset($row['accessToken']))
		{
				$access_token=$row['accessToken'];
		}
		if($access_token){
				try {
						if($access_token==globalAccessToken) {
		$params = json_decode(@file_get_contents('php://input'),TRUE);
							//var_dump($params);
		if(isset($params)) {
			$organization_logo="" && $organization_name = "";$industry_id = "";$address_1="";$address_2="";$city=""; $mobile=""; $fax="";$website="";$sender_email="";
			 $postalcode="";$financial_year="";$report_basis="";$date_format="";$date_format_seperator="";$timezone_id="";$country_id="";$state_id="";

			 if(isset($params['organization_logo'])) { $organization_logo = $params['organization_logo'];}
			if(isset($params['organization_name'])) { $organization_name = $params['organization_name'];}
		
			if(isset($params['industry_id'])) { $industry_id = $params['industry_id'];}
			if(isset($params['address_1'])) { $address_1 = $params['address_1'];}
			if(isset($params['address_2'])) { $address_2 = $params['address_2'];}
		
			if(isset($params['city'])) { $city = $params['city'];}
			if(isset($params['mobile'])) { $mobile = $params['mobile'];}
			if(isset($params['fax'])) { $fax = $params['fax'];}
			if(isset($params['website'])) { $website = $params['website'];}
			//if(isset($params['sender_email'])) { $sender_email = $params['sender_email'];}
			if(isset($params['postalcode'])) { $postalcode = $params['postalcode'];}
			if(isset($params['financial_year'])) { $financial_year = $params['financial_year'];}
			if(isset($params['report_basis'])) { $report_basis = $params['report_basis'];}
			if(isset($params['date_format'])) { $date_format = $params['date_format'];}
			if(isset($params['date_format_seperator'])) { $date_format_seperator = $params['date_format_seperator'];}
			if(isset($params['timezone_id'])) { $timezone_id = $params['timezone_id'];}
			if(isset($params['country_id'])) { $country_id = $params['country_id'];}
			if(isset($params['state_id'])) { $state_id = $params['state_id'];}
		
	
	try{
	
			if( $organization_logo!="" && $organization_name != "" && $industry_id != "" && $address_1!="" && $address_2!=""&& $city!=""&& $mobile!="" &&
			 $fax!="" && $website!="" && $postalcode!="" && $financial_year!=""&& $report_basis!=""&&
			  $date_format!="" && $date_format_seperator!=""&& $timezone_id!="" && $country_id!="" && $state_id!="") {
				




				$organizationdata = array(
					"organization_logo"=>$organization_logo,
					"organization_name"=>$organization_name,
					"industry_id"=>$industry_id,
					"address_1"=>$address_1,
					"address_2"=>$address_2,
					"city"=>$city,
					'mobile	'=>$mobile,
					"fax"=>$fax,
					"website"=>$website,
					"sender_email"=>$sender_email,
					"postalcode"=>$postalcode,
					"financial_year"=>$financial_year,
					"report_basis"=>$report_basis,
					"date_format"=>$date_format,
					"timezone_id"=>$timezone_id,
					"country_id"=>$country_id,
					"state_id"=>$state_id
				);
				//var_dump($organizationdata);
				$UpdateResult=$this->AuthModel->save('mst_organization',$organizationdata);
				if($UpdateResult==TRUE){
					$ret=$this->common->response(200,true,'successfully added',array());
				}
				else{
					$ret=$this->common->response(200,false,'no changes made in row',array());
				}
			}	else {
					$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
					}
				}catch (Exception $e) {
						$ret=$this->common->response(200,false,'somthing went wrong',array());
						}
					} 
	
				else {
					$ret=$this->common->response(200,false,'please Give Input',array());
				}
			}
			else {
				$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
			}
		}
			catch (Exception $e) {
				$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
				}
				}
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			echo json_encode($ret);
	}
	
	
	public function get_org_details(){
		$params = array();
					$ret=array();
					$data=array();
					$access_token=false;
					$row=$this->input->request_headers(); 
					if(isset($row['accessToken']))
					{
							$access_token=$row['accessToken'];
					}
					if($access_token){
						try {
								if($access_token==globalAccessToken) {
					$params = json_decode(@file_get_contents('php://input'),TRUE);

					if(isset($params)) {
					$user_id="";
					
					if(isset($params['user_id'])) { $user_id = $params['user_id'];}
					try{
						//$user_id;					
						if($user_id!="") {
							$decrypted_user_id=$this->common->decryption($user_id);
						$org_details = $this->AuthModel->getorgdetails($decrypted_user_id);
						//var_dump($org_details);
						
					if(!empty($org_details)){
						$output=array("org_details"=>$org_details);
						$ret=$this->common->response(200,true,'login Success',$output);
					}
					else {
						$ret=$this->common->response(200,false,'please check user Id',array());
					}
					} else {
						$ret=$this->common->response(200,false,'Please check your input like key and value',array());
						}
						}
					catch(exception $e){
						$ret=$this->common->response(200,false,'Please Enter email and password',array());
							}
					}
					else{
						$ret=$this->common->response(200,false,'Please Enter Inputs',array());
					}
				}
					else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
				}
				catch (Exception $e) {
					$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
					}
					}
					else {
						$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
					echo json_encode($ret);
		}    
	/*************************************get particular item details**************************/
	public function getItemDetails(){
		$params = array();
					$ret=array();
					$data=array();
					$access_token=false;
					$row=$this->input->request_headers(); 
					if(isset($row['accessToken']))
					{
							$access_token=$row['accessToken'];
					}
					if($access_token){
						try {
								if($access_token==globalAccessToken) {
					$params = json_decode(@file_get_contents('php://input'),TRUE);

					if(isset($params)) {
					$item_id="";
					
					if(isset($params['item_id'])) { $item_id = $params['item_id'];}
					try{
						//$user_id;					
						if($item_id!="") {
						$getitemDetails = $this->AuthModel->getitemDetails($item_id);
						//var_dump($org_details);
						
					if(!empty($getitemDetails)){
						$output=array("item_details"=>$getitemDetails);
						$ret=$this->common->response(200,true,'login Success',$output);
					}
					else {
						$ret=$this->common->response(200,false,'please check user Id',array());
					}
					} else {
						$ret=$this->common->response(200,false,'Please check your input like key and value',array());
						}
						}
					catch(exception $e){
						$ret=$this->common->response(200,false,'Please Enter email and password',array());
							}
					}
					else{
						$ret=$this->common->response(200,false,'Please Enter Inputs',array());
					}
				}
					else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
				}
				catch (Exception $e) {
					$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
					}
					}
					else {
						$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
					echo json_encode($ret);
		}    
	/****************************get items by organization Id**********************/
	public function getItemDetailsByorgId(){
		$ret=array();
			$params = array();
			$data=array();
			$access_token=false;
			$row=$this->input->request_headers(); 
			if(isset($row['accessToken']))
			{
					$access_token=$row['accessToken'];
			}
			if($access_token){
					try {
							if($access_token==globalAccessToken) {
			$params = json_decode(@file_get_contents('php://input'),TRUE);
			$organization_id="";
			if(isset($params)) {
				if(isset($params['oragnization_id'])) { $organization_id = $params['oragnization_id'];}
				//var_dump($params);
		try{
		
				if($organization_id!="") {
					   
					$getdetails=$this->AuthModel->getitemsbyorg($organization_id);
					//var_dump($getdetails);
					$data=array('item_details'=>$getdetails);
					if(!empty($getdetails)){
						$ret=$this->common->response(200,true,'Customer Details  Succeessfully',$data);
					} else {
		
						$ret=$this->common->response(200,false,'fail to add customer',array());
		
					}
				} 
						else {
						$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
						}
					}catch (Exception $e) {
							$ret=$this->common->response(200,false,'somthing went wrong',array());
							}
						} 
		
					else {
						$ret=$this->common->response(200,false,'please Give Input',array());
					}
				}
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			}
				catch (Exception $e) {
					$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
					}
					}
					else {
						$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
					}
				echo json_encode($ret);
			
	}
	
	
	/*****************************item Update************************/
	public function item_update(){
		$ret=array();
		$params = array();
		$data=array();
		$access_token=false;
		$row=$this->input->request_headers(); 
		if(isset($row['accessToken']))
		{
				$access_token=$row['accessToken'];
		}
		if($access_token){
				try {
						if($access_token==globalAccessToken) {
		$params = json_decode(@file_get_contents('php://input'),TRUE);
							//var_dump($params);
		if(isset($params)) {
			$sk_item_id ="";$item_name="";$description="";$unit="";$rate="";$type="";$item_type="";
			$selling_price="";$sales_account="";$cost_price="";$purchese_account="";$purchese_discription="";
			$sales_description="";
	
			if(isset($params['sk_item_id '])) { $sk_item_id  = $params['sk_item_id '];}
			if(isset($params['item_name'])) { $item_name = $params['item_name'];}
			if(isset($params['description'])) { $description = $params['description'];}
			if(isset($params['unit'])) { $unit = $params['unit'];}
			if(isset($params['rate'])) { $rate = $params['rate'];}
			if(isset($params['type'])) { $type = $params['type'];}
			if(isset($params['item_type'])) { $item_type = $params['item_type'];}
			if(isset($params['selling_price'])) { $selling_price = $params['selling_price'];}
			if(isset($params['sales_account'])) { $sales_account = $params['sales_account'];}
			if(isset($params['cost_price'])) { $cost_price = $params['cost_price'];}
			if(isset($params['purchase_account'])) { $purchese_account = $params['purchase_account'];}
			if(isset($params['purchase_description'])) { $purchese_description = $params['purchase_description'];}
			if(isset($params['sales_description'])) { $sales_description = $params['sales_description'];}


	try{
	
			if($sk_item_id !="" && $item_name!="" && $description!="" && $unit!="" && $rate!="" && $type!="" && $item_type!="" && $selling_price!="" && $sales_account!=""
			&& $cost_price!="" && $purchase_account!="" && $purchase_description!="" && $sales_description!="") {
			
		
				$itemdata= array(
					"item_name"=>$item_name,
					"description"=>$description,
					"unit"=>$unit,
					"rate"=>$rate,
					"type"=>$type,
					"item_type"=>$item_type,
					"selling_price"=>$selling_price,
					"sales_account"=>$sales_account,
					"cost_price"=>$cost_price,
					"purchase_account"=>$purchese_account,
					"purchase_description"=>$purchese_discription,
					"sales_description"=>$sales_description
					
				);
				$item_id=array("sk_item_id"=>$sk_item_id);
				$UpdateResult=$this->AuthModel->updateData("mst_items",$itemdata,$item_id );
				if($UpdateResult==TRUE){
					$ret=$this->common->response(200,true,'successfully Updated',array());
				}
				else{
					$ret=$this->common->response(200,false,'no changes made in row',array());
				}
			}	else {
					$ret=$this->common->response(200,false,'Please Check Input Key and Value',array());
					}
				}catch (Exception $e) {
						$ret=$this->common->response(200,false,'somthing went wrong',array());
						}
					} 
	
				else {
					$ret=$this->common->response(200,false,'please Give Input',array());
				}
			}
			else {
				$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
			}
		}
			catch (Exception $e) {
				$ret=$this->common->response(200,false,'Invalid Access Token or something went wrong',array());
				}
				}
				else {
					$ret=$this->common->response(200,false,'Invalid Access Token - please check access token both key and value',array());
				}
			echo json_encode($ret);
	}
}
?>
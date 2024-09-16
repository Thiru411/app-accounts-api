
<?php
class Common {
	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->library("session");
		$this->CI->load->library('email');
        $this->CI->load->helper('jwt_helper');
	}

    // method to give the response for rest api's
    public function response($scode,$flag,$msg,$resp){
        header("HTTP/1.1 $scode $flag", $flag, $scode);
        $ret = array(
                'status' => $flag,
                'message' => $msg,
                'data' => $resp

        );
        return $ret;
    }

    //method to generate cipher or token using JWT encode
    public function encryption($plainText){
        return JWT::encode($plainText,pkey);
    }
    public function decryption($ciperText){
        return JWT::decode($ciperText,pkey);
    }
}
?>
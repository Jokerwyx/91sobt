<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ��Ŀ����ģ��
 */
class search_model extends CI_Model{
	 //==============================================
	 //��ѯBT
	 public function search($id){
		$data = $this->db->where(array('aid'=>$id))->get('article')->result_array();
		return $data;
	 }
	 //==============================================
	 //����hash
	 public function addcurlhash($hash,$info)
	 {
	 	//SQL���  
		$sql = "INSERT INTO `dht`.`hash_info` (`id`, `hash`, `info`) VALUES (NULL, \'$hash\', \'$info\');";
        //ִ��SQL   
        echo $sql;
        exit;
        $result = $this->db->query($sql);  
        //�ر����ݿ�  
        $this->db->close();  
	 }


}
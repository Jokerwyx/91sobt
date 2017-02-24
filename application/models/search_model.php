<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 栏目管理模型
 */
class search_model extends CI_Model{
	 //==============================================
	 //查询BT
	 public function search($id){
		$data = $this->db->where(array('aid'=>$id))->get('article')->result_array();
		return $data;
	 }
	 //==============================================
	 //存入hash
	 public function addcurlhash($hash,$info)
	 {
	 	//SQL语句  
		$sql = "INSERT INTO `dht`.`hash_info` (`id`, `hash`, `info`) VALUES (NULL, \'$hash\', \'$info\');";
        //执行SQL   
        echo $sql;
        exit;
        $result = $this->db->query($sql);  
        //关闭数据库  
        $this->db->close();  
	 }


}
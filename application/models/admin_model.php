<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台用户管理模型
 */
class Admin_model extends CI_Model{
	//===========================================
	//系统设置添加
	public function system($system){
		$this->db->insert('system', $system);
	}
	/**
	 * 查询后台用户数据
	 */
	public function check($username){
		// $this->db->where(array('username'=>$username))->get('admin')->result_array();
		$data = $this->db->get_where('admin', array('username'=>$username))->result_array();
		return $data;
	}
	//============================================
	//系统设置
	public function windows(){
		$data = $this->db->get('system')->result_array();
		return $data;

	}

	/**
	 * 修改密码
	 */
	public function change($uid, $data){
		$this->db->update('admin', $data, array('uid'=>$uid));
	}

	
}
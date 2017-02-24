<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 搜索管理管理模型
 */
class Category_model extends CI_Model{
	//===========================================
	//构造函数  
    function MQuery() {  
        parent::Model();  
        //连接数据库  
        $this->load->database();  
    } 
	//===========================================
	//数据冗余
	public function clearcachehashinfo(){
		$data=$this->db->affected_rows();
		$this->db->delete('hash_info', array('info'=>'error'));
		return $data;
	}
	//===========================================
	//提交查询
	public function search($vedio,$page_link){
		$sql = "select * from `search_ssbc` where id in($vedio) order by id asc limit $page_link,10";
        //执行SQL   
        $res = $this->db->query($sql);  
        //关闭数据库  
        $data = $res->result();
        $this->db->close();  
        //返回值  
        return $data; 
	}

	/**
	 * 添加
	*/
	public function add($data){
		$this->db->insert('category', $data);
	}
	/*
	*添加bt
	*/
	public function addbt($btinfo){
		$this->db->insert('hash_info', $btinfo);
	}
	/**
	 * 查看栏目
	 */
	public function check(){
		$data = $this->db->get('category')->result_array();
		return $data;
	}


	/**
	 * 查询对应栏目
	 */
	public function check_cate($cid){
		$data = $this->db->where(array('cid'=>$cid))->get('category')->result_array();
		return $data;
	}

	/**
	 * 修改栏目
	 */
	public function update_cate($cid, $data){
		$this->db->update('category', $data, array('cid'=>$cid));
	}


	/**
	 * 删除栏目
	 */
	public function del($cid){
		$this->db->delete('category', array('cid'=>$cid));
	}


	/**
	 * 调取导航栏栏目
	 */
	public function limit_category($limit){
		$data = $this->db->limit($limit)->get('category')->result_array();
		return $data;
	}















}
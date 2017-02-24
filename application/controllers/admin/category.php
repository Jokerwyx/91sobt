<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends MY_Controller{
	//===================================================
	//构造函数
	public function __construct(){
		parent::__construct();
		$this->load->model('category_model', 'cate');
	}

	//===================================================
	//系统设置
	public function system(){
		$this->load->model('Admin_model', 'joker');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('site_domain', '网站域名', 'required|min_length[20]|max_length[100]');
		$status=$this->form_validation->run();
		if($status){
			echo "success";
		}
		else{
			$this->load->helper("form");
			$this->load->view("admin/system");

		}
		$system = array(
				'site_name'		=> $this->input->post('site_name'),
				'site_domain'	=> $this->input->post('site_domain'),
				'site_title'	=> $this->input->post('site_title'),
				'site_keywords'	=> $this->input->post('site_keywords'),
				'site_type'	    => $this->input->post('site_dis'),
				'site_xinyu'	=> $this->input->post('site_xinyu'),
				'site_beian'	=> $this->input->post('site_beian'),
				'site_qq'	    => $this->input->post('site_qq'),
				'site_weibo'	=> $this->input->post('site_weibo'),
				'site_count'	=> $this->input->post('site_count'),
				'site_keymax'	=> $this->input->post('site_keymax'),
				'site_open'		=> $this->input->post('site_open')
				);
		$res=$this->joker->system($system);
		if(!$res){
				success('admin/admin/system', '添加成功');
		}
		else{

				echo "error";
		}


	}
	/**
	 * 查看栏目
	 */
	public function index(){
		$data['category'] = $this->cate->check();
		$this->load->view('admin/cate.html', $data);
	}
	/**
	 * 添加栏目
	 */
	public function add_cate(){
		// $this->output->enable_profiler(TRUE);
		$this->load->helper('form');
		$this->load->view('admin/add_cate.html');
	}

	//====================================================
	//添加动作
	public function add(){
		$this->load->library('form_validation');
		$status = $this->form_validation->run('cate');

		if($status){
			// echo "数据库操作";
			// echo $_POST['abc'];die;
			// var_dump($this->input->post('abc'));die;

			$data = array(
				'cname'	=> $this->input->post('cname')
				);

			$this->cate->add($data);
			success('admin/category/index', '添加成功');
		} else {
			$this->load->helper('form');
			$this->load->view('admin/add_cate.html');
		}
	}
	public function addbt(){
			$btinfo = array(
				'hash'	=> $this->input->post('bthash'),
				'info'	=> $this->input->post('btinfo')
				);
			$this->cate->addbt($btinfo);
			success('admin/category/index', '添加成功');
	
	}
		

	/**
	 * 编辑
	 */
	public function edit_cate(){
		$cid = $this->uri->segment(4);
		// echo $cid;die;

		$data['category'] = $this->cate->check_cate($cid);

		$this->load->helper('form');
		$this->load->view('admin/edit_cate.html', $data);
	}


	/**
	 * 编辑动作
	 */
	public function edit(){
		$this->load->library('form_validation');
		$status = $this->form_validation->run('cate');

		if($status){

			$cid = $this->input->post('cid');
			$cname = $this->input->post('cname');

			$data = array(
				'cname'	=> $cname
				);

			$data['category'] = $this->cate->update_cate($cid, $data);
			success('admin/category/index', '修改成功');
		} else {
			$this->load->helper('form');
			$this->load->view('admin/edit_cate.html');
		}
	}

	/**
	 * 删除栏目
	 */
	public function del(){
		$cid = $this->uri->segment(4);
		$this->cate->del($cid);
		success('admin/category/index', '删除成功');
	}





}
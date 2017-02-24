<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Article extends MY_Controller{

	//===================================================
	//繁体转简体C
	public function ftojp(){
		$ftinfo = $this->input->post('ftinfo');  
		$jtinfo = $this->input->post('jtinfo'); 
        //调用model  
        $this->load->model('article_model','ftoj'); 
        //向model中的ftoj传值  
        $result = $this->ftoj->ftoj($ftinfo,$jtinfo);  
        if($result){
			echo "change success.";
        }
        else{

        	 echo "change failed.";
        }
	}
	public function index(){
		//后台设置后缀为空，否则分页出错
		$this->config->set_item('url_suffix', '');
		//载入分页类
		$this->load->library('pagination');
		$perPage = 3;

		//配置项设置
		$config['base_url'] = site_url('admin/article/index');
		$config['total_rows'] = $this->db->count_all_results('article');
		$config['per_page'] = $perPage;
		$config['uri_segment'] = 4;
		$config['first_link'] = '第一页';
		$config['prev_link'] = '上一页';
		$config['next_link'] = '下一页';
		$config['last_link'] = '最后一页';

		$this->pagination->initialize($config);

		$data['links'] = $this->pagination->create_links();
		// p($data);die;
		$offset = $this->uri->segment(4);
		$this->db->limit($perPage, $offset);


		$this->load->model('article_model', 'art');
		$data['article'] = $this->art->article_category();

		// p($data);die;
		$this->load->view('admin/check_article.html', $data);
	}
	//===================================================
	//BT信息模板显示
	public function send_article(){
		$this->load->model('category_model', 'cate');
		$data['category'] = $this->cate->check();
		$this->load->helper('form');
		$this->load->view('admin/article.html', $data);
	}
	//===================================================
	//繁体转简体
	public function ftoj(){
		$this->load->view('admin/ftoj.html');
	}
	public function addbt()
	{
		$this->load->view('admin/addbt.html');
	}
	public function addurl()
	{
		$this->load->view('admin/addurl.html');
	}
	public function getinfo(){
		$this->config->set_item('url_suffix', '');
		//载入分页类
		$this->load->library('pagination');
		$perPage = 12;
		//配置项设置
		$config['base_url'] = site_url('admin/article/getinfo');
		$config['total_rows'] = $this->db->count_all_results('hash_info');
		$config['per_page'] = $perPage;
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<p>';     
  		$config['full_tag_close'] = '</p>';
		$config['first_link'] = '第一页';
		$config['prev_link'] = '上一页';
		$config['next_link'] = '下一页';
		$config['last_link'] = '最后一页';
		$this->pagination->initialize($config);
		$data['links'] = $this->pagination->create_links();
		$offset = $this->uri->segment(4);
		$this->db->limit($perPage, $offset);
		$this->load->model('article_model', 'art');
		$data['article'] = $this->art->article_category();
		$this->load->view('admin/getinfo.html', $data);

	}
	//=================================================
	//清除缓存
	public function clearcache(){		
		$this->load->view('admin/clearcache.html',$msg);
	}
	//==================================================
	//BT文件缓存
	public function clearcachefile(){	
		global $msg;
		$this->delFileUnderDir();
		$msg .= "<hr>清楚所有缓存完成";
		echo $msg;
	}
	//==================================================
	//hash_info冗余数据
	public function clearcachehashinfo(){
		$this->load->model('category_model', 'cate');	
		$this->cate->clearcachehashinfo();
		$msg .= "<hr>清楚mysql.hash_info完成";
		echo $this->db->last_query();
		echo $data;
		echo $msg;
	}

	//==================================================
	//cahe文件缓存
	public function clearcachecurl(){		
		global $msg;		
		$this->delFileUnderDircache();
		$msg .= "<hr>清楚cahe文件缓存完成";
		echo $msg;
	}

	//==================================================
	//循环BT目录下的所有文件
	private function delFileUnderDir( $dirName="./torrent/" ){
		global $msg;
		if ( $handle = opendir( "$dirName" ) ) {
			while ( false !== ( $item = readdir( $handle ) ) ) {
				if ( $item != "." && $item != ".." ) {
					if ( is_dir( "$dirName/$item" ) ) {
						$this->delFileUnderDir( "$dirName/$item" );
					} else {
						if(unlink( "$dirName/$item")){$msg .= "成功删除文件：$dirName/$item<br />\n";}
					}
				}
			}
		   closedir( $handle );
		}
	}
	//==================================================
	//循环cache目录下的所有文件
	private function delFileUnderDircache( $dirName="./cache/" ){
		global $msg;
		if ( $handle = opendir( "$dirName" ) ) {
			while ( false !== ( $item = readdir( $handle ) ) ) {
				if ( $item != "." && $item != ".." ) {
					if ( is_dir( "$dirName/$item" ) ) {
						$this->delFileUnderDir( "$dirName/$item" );
					} else {
						if(unlink( "$dirName/$item")){$msg .= "成功删除文件：$dirName/$item<br />\n";}
					}
				}
			}
		   closedir( $handle );
		}
	}
	/**
	 * 编辑文章
	 */
	public function edit_article(){
		$this->load->helper('form');
		$this->load->view('admin/edit_article.html');
	}

	/**
	 * 编辑动作
	 */
	public function edit(){
		$this->load->library('form_validation');
		$status = $this->form_validation->run('article');

		if($status){
			echo '数据库操作';
		} else {
			$this->load->helper('form');
			$this->load->view('admin/edit_article.html');
		}
	}





}
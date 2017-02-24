<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 默认前台控制器
 */
class Home extends CI_Controller{
	public $category;
	public $title;
	public function __construct(){
		parent::__construct();
		header("content-type:text/html;charset=utf-8");
		//$this->load->model('article_model', 'art');
		$this->load->model('category_model', 'cate');
		$this->category = $this->cate->limit_category(4);
		require APPPATH.'/libraries/phpfastcache.php';
      	require APPPATH.'/libraries/httpsqs_client.php';
		//$this->title = $this->art->title(10);
	}
	//===================================================
	//默认首页显示方法
	public function index(){
		$cache = phpFastCache("files", array("path"=>"cache"));
    	$url = 'http://www.wandafilm.com/homePage.do?m=getOnShowListJSON';
   		$content = $cache->get('Popularkeywords');
	        if (!$content) {
	            $html = file_get_contents($url);
	            $cont = json_decode(iconv("gb2312", "utf-8//IGNORE",$html));
	            foreach ($cont as $key => $value) {
	                foreach ($value as $value) {
	                    $content[] = $value->filmName;
	                }
	            }
	            $cache->set('Popularkeywords',$content, 864000);
	        }
	    $content['info']=$content;
	    $this->load->helper('url');
		$this->load->view('index/head',$content);
		$this->load->view('index/search');
		$this->load->view('index/footer');
	}
	//===================================================
	//分类页显示
	public function content(){
		$this->load->view('index/info.html');

	}
	//===================================================
	//分类页显示
	public function vo(){
		$this->load->view('index/vo.html');

	}
	//===================================================
	//分类页显示
	public function category(){
		$data['category'] = $this->category;
		$data['title'] = $this->title;
		$cid = $this->uri->segment(2);
		$data['article'] = $this->art->category_article($cid);
		$this->load->view('index/category.html', $data);
	}
	/**
	 * 文章阅读页显示
	 */
	public function article(){
		$aid = $this->uri->segment(2);

		$data['category'] = $this->category;

		$data['title'] = $this->title;

		$data['article'] = $this->art->aid_article($aid);

		// p($data);

		$this->load->view('index/article.html', $data);
	}
	public function search(){
		$id=$_POST['keywords'];
		$data['info']= $this->cate->search($id);
		$this->load->helper('form');
		$this->load->view('index/info.html',$data);
	
	}


















}
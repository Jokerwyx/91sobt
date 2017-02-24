<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
*2014年10月26日 14:57:56
*author:joker
*version:1.0
*主搜索方法
*/
class search extends CI_Controller {

	function __construct()
	 {
	  parent::__construct();
	  header("content-type:text/html;charset=utf-8");
	  require APPPATH.'/libraries/sphinxapi.php';
	  require APPPATH.'/libraries/phpfastcache.php';
      require APPPATH.'/libraries/httpsqs_client.php';
      require APPPATH.'/libraries/Simple_html_dom.php';
	  $this->load->model('category_model', 'cate');

	 }
	public function getinfo(){

		$cl = new SphinxClient ();
		$cl->SetServer ('127.0.0.1', 9312);
		$cl->SetConnectTimeout (30);
		$cl->SetArrayResult ( true );
		$cl->SetMatchMode( "SPH_MATCH_ANY" );
		$cl->setLimits(0,10000);
		$keywords=$_GET['keyword'];
		$keyword=$_GET['keyword'];
		$res = $cl->Query ($keywords,"*");
		$total = $res['total_found'];//取得匹配总数
		$res=$res['matches'];//匹配到的数组
		////////////////手动拆词
		if(empty($res)){
			$count=mb_strlen($_GET['keyword']);
			$keywords=mb_substr($_GET['keyword'],1,$count-4,'utf-8');
			$res = $cl->Query ($keywords,"*");
			$res=$res['matches'];		
		}

		foreach($res as $row)
		{
		    $vedio.=$row['id'].",";
		}
		$vedio=rtrim($vedio, ",");
		//=====================================================
		//如果py未爬取的数据则去torrentkitty去获取
		if(!$vedio){
				if (!empty($_GET['counts']) && !empty($_GET['page'])) {

						$counts = intval($_GET['counts']);
						$page   = intval($_GET['page']);
								if ($counts >= $page && !empty($counts) && !empty($page)) {
										$list['info'] = $this->Collection($keyword, '/'.$page);
									}
						}
				
					$list['info']=$this->Collection($keyword, '/'.$page);
					//入库	
					foreach ($list as $key => $value) {
						 foreach ($value as $val => $url) {
						 	 $hash=substr($url['url'],20,40);
						 	 $info=$url['name'];
						 	 $sql = "INSERT INTO `ssbc`.`search_ssbc` (`id`, `info_hash`, `name`, `size`, `filelist`) VALUES (NULL, '$hash', '$info','','');";
						 	 $this->db->query($sql); 
							
						 }
					}
				
					$list['keyword']=$_GET['keyword'];
					//print_r($list);
					$list['counts'] =$this->Counts_page($keyword);
					$this->load->view('index/curl.html',$list);

		}
		else{
			$data['keyword']=$_GET['keyword'];
			$page=10;
			$page_link=$_GET['page']*$page;
			$data['info']=$this->cate->search($vedio,$page_link);
			$data['page']=$this->show_page($total,$_GET['page'],$page);
			$this->load->view('index/list.html',$data);
		}
			
		}
//=========================================================
//获取HASH
function get_hash($magnetic)
{
	$magn_str = explode("btih:", $magnetic);
	$magn_end = explode("&", $magn_str['1']);
	return $magn_end['0'];
}

//=========================================================
//请求数据
function get_data($url)
{
	$headers = array('Host: www.torrentkitty.net', 'Content-type: application/x-www-form-urlencoded;charset=UTF-8', 'Connection: Keep-Alive', 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg');
	$process =curl_init($url);
	curl_setopt ($process, CURLOPT_HTTPHEADER, $headers);
	curl_setopt ($process, CURLOPT_HEADER, 0);
	//curl_setopt ($process, CURLOPT_PROXY, '192.168.91.1');
	curl_setopt ( $process, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
	curl_setopt ($process, CURLOPT_REFERER, "http://www.torrentkitty.net/search/");
	curl_setopt ( $process, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $process, CURLOPT_TIMEOUT,600);
	$return = curl_exec ( $process );
	curl_close ( $process );
	return $return;
}

//=========================================================
//获得详情页数据
function get_shahinfo($hash)
{
	if (preg_match('/^[0-9A-Z]+$/u',$hash)) { 
		$cache = phpFastCache("files", array("path"=>"cache"));
		$conter = $cache->get($hash);
		if (is_null($conter)) {
			$url = 'http://www.torrentkitty.net/information/';
			$content = get_data($url.$hash);
			$html = new simple_html_dom();
			$html->load($content);

			@$ret = $html->find('h3');
			if (isset($ret['0']->nodes['0']->_['4']) && $ret['0']->nodes['0']->_['4'] == 'Magnet Link does not eixst. You may try to upload it again.') {

				$info['error'] = TRUE;

			} else {

				foreach($html->find('.magnet-link') as $article) {
					$item['magnet'] = $article->plaintext;
					$articles[] = $item;
				}

				foreach($html->find('.detailSummary') as $article) {
					foreach($article->find('tr') as $tr) {
						foreach($article->find('td') as $td) {
							$item[] =  $td->plaintext;
						}
					}
				}

				preg_match('%<table[^>]*id="torrentDetail"[^>]*>(.*?) </table>%si', $content, $match);
				preg_match('%<h2>(.*?)</h2>%si', $content, $ret);
				$title = mb_substr($ret['0'], 25);

				$info['title'] = $title;
				$info['list'] = $match;
				$info['size'] = $item['3'];
				$info['quantity'] = $item['2'];
				$info['cdate'] = $item['4'];
				$info['magnetic'] = $articles['0']['magnet'];
				$cache->set($hash, $info, 864000);
			}
		} else {
			$info = $conter;
		}
	} else {
		$info['error'] = TRUE;
	}
	return $info;
}

//=========================================================
//获取网页内容并缓存到本地
	function Curl_content($keyword, $page = ''){
	$cache = phpFastCache("files", array("path"=>"cache"));
	$htmlconter = $cache->get($keyword.$page);
	if ($htmlconter == null) {
		$url = 'http://www.torrentkitty.net/search/';
		$content = $this->get_data($url.$keyword.$page);
		$cache->set($keyword.$page, $content, 2592000);
		return $content;
	} else {
		return $htmlconter;
	}
}
//=========================================================
//计算翻页页数
	function Counts_page($keyword){
	$content =$this->Curl_content($keyword);
	$dom = new simple_html_dom();
	$dom->load($content);
	foreach($dom->find('div[class=pagination]') as $element) {}
	if (isset($element)) {
		foreach($element->find('a') as $tt) { $pagenum[] = $tt->href; }
		$pos = array_search(max($pagenum), $pagenum);
		$dom->clear();
		return $pagenum[$pos];
	} else {
		return '0';
	}
}
//=========================================================
//页面正则到内容
	public function Collection($keyword, $page){
	$content = $this->Curl_content($keyword, $page);
	preg_match_all("/<tr><td class=\"name\">(.+?)<\/td><\/tr>/ms", $content, $list);
	$lu_list = array();
	if (is_array($list['0'])) {
		for ($i=0; $i < count($list['0']); $i++) { 
			$video_list = $list['0'];
			preg_match_all("/<td(.[^>]*)>(.+?)<\/td>/ms", $video_list[$i], $video_info[]);
			preg_match ("/href=\"magnet:(.+?)\"/ms", $video_info[$i]['2']['3'], $magnet_infos[]);
			$bt = array();
			$bt['name'] = $video_info[$i]['2']['0'];
			$bt['size'] = $video_info[$i]['2']['1'];
			$bt['date'] = $video_info[$i]['2']['2'];
			$bt['url'] = "magnet:".$magnet_infos[$i]['1'];
			$bt_json[$i] =$bt;
		}
		return $bt_json;
	} else {
		return false;
	}
}
//=========================================================
//记录搜索词日志
	function search_log($msg){
		// $memcache_obj = memcache_connect("localhost", 11211);
		// $memcache_obj->add(time(), $msg, false, time()+87300);
	}
//=========================================================
//生成信息页短地址
	function create_dwz($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://dwz.soubt.org/api/create/');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('url' => $url));
		$curl_result = curl_exec($ch);
		if ($curl_result == FALSE) {
			return curl_error($ch);
		} else {
			$result_data = json_decode($curl_result, TRUE);
			if ($result_data['statusCode'] == '000000') {
				return $result_data['url'];
			} else {
				return $result_data['error'];
			}
		}
		curl_close($ch);

}

//=========================================================
//php分页,$count为总条目数，$page为当前页码，$page_size为每页显示条目数
	private function show_page($count,$page,$page_size){
		    $page_count  = ceil($count/$page_size);  //计算得出总页数
		    $init=1;
		    $page_len=7;
		    $max_p=$page_count;
		    $pages=$page_count;
		 
		    //判断当前页码
		    $page=(empty($page)||$page<0)?1:$page;
		    //获取当前页url
		    $url = $_SERVER['REQUEST_URI'];
		    //去掉url中原先的page参数以便加入新的page参数
		    $parsedurl=parse_url($url);
		    $url_query = isset($parsedurl['query']) ? $parsedurl['query']:'';
		    if($url_query != ''){
		        $url_query = preg_replace("/(^|&)page=$page/",'',$url_query);
		        $url = str_replace($parsedurl['query'],$url_query,$url);
		        if($url_query != ''){
		            $url .= '&';
		        }
		    } else {
		        $url .= '?';
		    }
		     
		    //分页功能代码
		    $page_len = ($page_len%2)?$page_len:$page_len+1;  //页码个数
		    $pageoffset = ($page_len-1)/2;  //页码个数左右偏移量
		 
		    $navs='';
		    if($pages != 0){
		        if($page!=1){
		            $navs.="<a href=\"".$url."page=1\">首页</a> ";        //第一页
		            $navs.="<a href=\"".$url."page=".($page-1)."\">上页</a>"; //上一页
		        } else {
		            $navs .= "<span class='disabled'>首页</span>";
		            $navs .= "<span class='disabled'>上页</span>";
		        }
		        if($pages>$page_len)
		        {
		            //如果当前页小于等于左偏移
		            if($page<=$pageoffset){
		                $init=1;
		                $max_p = $page_len;
		            }
		            else  //如果当前页大于左偏移
		            {    
		                //如果当前页码右偏移超出最大分页数
		                if($page+$pageoffset>=$pages+1){
		                    $init = $pages-$page_len+1;
		                }
		                else
		                {
		                    //左右偏移都存在时的计算
		                    $init = $page-$pageoffset;
		                    $max_p = $page+$pageoffset;
		                }
		            }
		        }
		        for($i=$init;$i<=$max_p;$i++)
		        {
		            if($i==$page){$navs.="<span class='current'>".$i.'</span>';} 
		            else {$navs.=" <a href=\"".$url."page=".$i."\">".$i."</a>";}
		        }
		        if($page!=$pages)
		        {
		            $navs.=" <a href=\"".$url."page=".($page+1)."\">下页</a> ";//下一页
		            $navs.="<a href=\"".$url."page=".$pages."\">末页</a>";    //最后一页
		        } else {
		            $navs .= "<span class='disabled'>下页</span>";
		            $navs .= "<span class='disabled'>末页</span>";
		        }

		         return $navs;
		       
	   }
}
	//===================================================
	//采集资源
	public function curl(){
		$keyword=$_POST['keyword'];
		$list['info']=$this->Collection($keyword);

			//入库	
					foreach ($list as $key => $value) {
						 foreach ($value as $val => $url) {
						 	 $hash=substr($url['url'],20,40);
						 	 $info=$url['name'];
						 	 $sql = "INSERT INTO `dht`.`hash_info` (`id`, `hash`, `info`) VALUES (NULL, '$hash', '$info');";
						 	 $res=$this->db->query($sql); 
						 	 if($res){
						 	 		echo "success";

						 	 }

							
						 }
					}


	}


}

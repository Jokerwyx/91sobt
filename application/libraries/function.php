<?php

/**
* 热门关键词列表
*/
function Popular_keywords()
{
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
 
    return $content;

}


/**
* 标题截断
*/
function title_truncation($title, $enc='utf-8')
{
    return mb_substr($title, 0, 50, $enc); 
}


/**
* 获取HASH
*/
function get_hash($magnetic)
{
	$magn_str = explode("btih:", $magnetic);
	$magn_end = explode("&", $magn_str['1']);
	return $magn_end['0'];
}


/**
* 请求数据
**/
function get_data($url)
{
	$headers = array('Host: www.torrentkitty.org', 'Content-type: application/x-www-form-urlencoded;charset=UTF-8', 'Connection: Keep-Alive', 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg');
	$process = curl_init($url);
	curl_setopt ($process, CURLOPT_HTTPHEADER, $headers);
	curl_setopt ($process, CURLOPT_HEADER, 0);
	//curl_setopt ($process, CURLOPT_PROXY, '192.168.91.1');
	curl_setopt ( $process, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
	curl_setopt ($process, CURLOPT_REFERER, "http://www.torrentkitty.org/search/");
	curl_setopt ( $process, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $process, CURLOPT_TIMEOUT,600);
	$return = curl_exec ( $process );
	curl_close ( $process );
	return $return;
}


/**
* 获得详情页数据
*/
function get_shahinfo($hash)
{
	if (preg_match('/^[0-9A-Z]+$/u',$hash)) { 
		$cache = phpFastCache("files", array("path"=>"cache"));
		$conter = $cache->get($hash);
		if (is_null($conter)) {
			$url = 'http://www.torrentkitty.org/information/';
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


/**
* 获取网页内容并缓存到本地
*/
function Curl_content($keyword, $page = '')
{
	$cache = phpFastCache("files", array("path"=>"cache"));
	$htmlconter = $cache->get($keyword.$page);
	if ($htmlconter == null) {
		$url = 'http://www.torrentkitty.org/search/';
		$content = get_data($url.$keyword.$page);
		$cache->set($keyword.$page, $content, 2592000);
		return $content;
	} else {
		return $htmlconter;
	}
}

/*
* 计算翻页页数
*/
function Counts_page($keyword)
{

	$content = Curl_content($keyword);
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

/*
* 页面正则到内容
*/
function Collection($keyword, $page)
{
	$content = Curl_content($keyword, $page);
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


/**
* 记录搜索词日志
*/
function search_log($msg)
{
	// $memcache_obj = memcache_connect("localhost", 11211);
	// $memcache_obj->add(time(), $msg, false, time()+87300);
}


/**
* 生成信息页短地址
*/
function create_dwz($url)
{
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

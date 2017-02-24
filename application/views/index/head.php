<?php 
ob_start(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>种子搜索神站|种子搜索网站|91sobt|蝌蚪窝</title>
  <meta name="keywords" content="种子搜索,种子搜索网站,种子搜索神站,磁力搜索网站,蝌蚪窝,释放蝌蚪的网站,番号搜索网站," />
  <meta name="description" content="种子搜索网站,种子搜索神站,是一个促进中日文化交流的种子搜索网站,种子搜索网站,一个释放蝌蚪的网站,最新的蝌蚪窝BT搜索" />	
  <link rel="stylesheet" href="<?php echo base_url('/public/css/bootstrap.min.css');?>">
  <link rel="stylesheet" href="<?php echo base_url('/public/css/bootstrap-theme.css');?>">
	<link href="<?php echo base_url('/public/css/style.css');?>" rel="stylesheet">
  <script src="<?php echo base_url('/public/js/jquery.min_1.9.js');?>"></script>
<!--<base target="_blank">--><base href="." target="_blank"></head>
<script src="http://siteapp.baidu.com/static/webappservice/uaredirect.js" type="text/javascript"></script>
<script type="text/javascript">uaredirect("http://m.91sobt.com/");</script>
        <link href="<?php echo base_url('/public/images/web.ico');?>" rel="shortcut icon" type="image/x-icon">
<body>
<div id="keyword" style="display: none;">
  <div class="tags">
<?php foreach ($info as $v):?>
<?php echo '<a href="/index/search/getinfo?keyword='.$v.'" class="label label-primary tags_a" target="_blank">'.$v.'</a> ';?>
<?php endforeach ?>
  </div>
  <div class="hide_keyword" id="hide_keyword" style="cursor:pointer">
      Close
  </div>
</div>
<!--<base target="_blank">--><base href="." target="_blank">
<script type="text/javascript">
  $("#keyword").hide();
    $(document).ready(function(){
      $("#show_keyword").click(function(){
      $("#keyword").slideDown(500);
      });
     $("#hide_keyword").click(function(){
      $("#keyword").slideUp(500);
      $("#show_keyword").show();
     });
  });
</script>

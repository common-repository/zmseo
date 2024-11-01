<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе
	
if ($api_go['z']==1) $post_fields['zapros']='our_id_app';
if ($api_go['z']==2) $post_fields['zapros']='id_site';
if ($api_go['z']==3) $post_fields['zapros']='more_kpi';
if ($api_go['z']==4) $post_fields['zapros']='more_key';
if ($api_go['z']==5) $post_fields['zapros']='status';
if ($api_go['z']==6) $post_fields['zapros']='cannibals';
if ($api_go['z']==7) $post_fields['zapros']='pointsup';
if ($api_go['z']==8) $post_fields['zapros']='report';
if ($api_go['z']==9) $post_fields['zapros']='post';
if ($api_go['z']==10) $post_fields['zapros']='links_page';

$post_fields['unid_key']=$tuning['unid_key'];
$post_fields['url_site']=$_SERVER['HTTP_HOST'];
$post_fields['version']='1.14.1';
$post_fields['token_1']=$tuning['token_1'];

if ($api_go['z']==1 and isset($_POST['phone'])) $post_fields['phone']=filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
if ($api_go['z']==1 and isset($_POST['email'])) $post_fields['email']=sanitize_email($_POST['email']);
if ($api_go['z']==1 and isset($_POST['name'])) $post_fields['name']=sanitize_post($_POST['name']);

if ($api_go['z']==3) $post_fields['good_pages']=$tuning['good_pages'];
if ($api_go['z']==3 and isset($_GET['g_page'])) $post_fields['g_page']=filter_var($_GET['g_page'], FILTER_SANITIZE_NUMBER_INT);
if ($api_go['z']==3 and isset($_GET['filter'])) $post_fields['filter_pages']=$mass_filter;

if ($api_go['z']==4) $post_fields['url_page']=get_permalink();
if ($api_go['z']==4 and $post->post_content) $post_fields['content']=$post->post_content;
if ($api_go['z']==4 and isset($_POST['change_new']) and wp_verify_nonce( $_POST['nonce'], $_POST['change_new'])) 
	$post_fields['change_new']=sanitize_post($_POST['text_change']);
if ($api_go['z']==4 and isset($_POST['save_up']) and wp_verify_nonce( $_POST['nonce'], $_POST['save_up'])) 
	{
		$post_fields['save_up']=filter_var($_POST['save_up'], FILTER_SANITIZE_NUMBER_INT); 
		$post_fields['text_change']=sanitize_post($_POST['text_change']);
	}
if ($api_go['z']==4 and isset($zmseo_keys)) $post_fields['keys']=$zmseo_keys;

if ($api_go['z']==7 and isset($_GET['sort'])) $post_fields['sort']=sanitize_post($_GET['sort']);

if ($api_go['z']==9 and isset($zm_title)) $post_fields['title']=$zm_title;
if ($api_go['z']==9 and isset($zm_desc)) $post_fields['desc']=$zm_desc;
if ($api_go['z']==9 and isset($zm_h1)) $post_fields['h1']=$zm_h1;
if ($api_go['z']==9 and isset($zmseo_keys)) $post_fields['keys']=$zmseo_keys;
if ($api_go['z']==9 and $post->post_content) $post_fields['content']=$post->post_content;
if ($api_go['z']==9) $post_fields['url_page']=get_permalink();
if ($api_go['z']==9 and isset($link_keys)) $post_fields['link_keys']=$link_keys;

if ($api_go['z']==10) $post_fields['url_page']=get_permalink();
if ($api_go['z']==10 and isset($_POST['up_links']) and $_POST['up_links']=='go') $post_fields['up_links']='go';


###
if (isset($post_fields['content']) and mb_strlen(trim($post_fields['content']), 'utf-8')>29000) $post_fields['content']=substr($post_fields['content'], 0, 29000);
###

#echo '<hr>';
#echo 'Отсылаем:<br>';
#print_r($post_fields);
#echo '<hr>';

if ($post_fields)
{
$url = 'https://zmseo.ru/api/api_sistem.php';
$args = array(
	'timeout'     => 8,
	'body'    => $post_fields
);
$out = wp_remote_post( $url, $args );
}
if (is_array($out)) $arr_api=json_decode($out['body'], true);

if ($arr_api['info']['status']!=1) 
{
$arr_api='';
$arr_api['attention'][]='Нет связи с удаленным сервером';
}
	if (isset($arr_api['attention']))
	foreach ($arr_api['attention'] as $i => $zn)
	{
		if ($zn) echo '<p style="color:red;">- '.$zn.'</p>';
	}
	
#echo 'Получаем:<br>';
#print_r($arr_api);
#echo '<hr>';

?>
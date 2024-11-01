<?php

### Анализ страницы
# TDH
if ($sel_sup->title) $zm_title = get_post_meta( $post->ID, $sel_sup->title, true );
if (!$sel_sup->title) $zm_title = $post->post_title;
if ($sel_sup->desc) $zm_desc = get_post_meta( $post->ID, $sel_sup->desc, true );
if ($sel_sup->h1) $zm_h1 = get_post_meta( $post->ID, $sel_sup->h1, true );
#

### Ссылки
$arr_tmp = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish'");
if (isset($arr_tmp) and sizeof($arr_tmp))
foreach( $arr_tmp as $i => $id_p ) {$title_arr[$id_p->ID]=$id_p->post_title;}
wp_reset_postdata();
###


	if (isset($title_arr) and sizeof($title_arr))
	foreach($title_arr as $i => $title_p )
    {
		$title_p = mb_convert_case($title_p, MB_CASE_LOWER, "UTF-8");
		$mass_title = explode(' ', trim($title_p));
			if (isset($mass_title) and sizeof($mass_title))
			foreach($mass_title as $n => $zn )
				{
					$zn=preg_replace('%[^A-Za-zА-Яа-яё]%u', '', trim($zn));
					$g_rez='';
					$rez = substr($zn, 0, 8);
					if 	(mb_strlen($rez, 'utf-8')==4) $g_rez=$rez;
					$rez = substr($zn, 0, 4);
					if 	(mb_strlen($rez, 'utf-8')==4) $g_rez=$rez;
					if ($g_rez) $titles_arr4[$i][]=$g_rez; # Массив названия статьи из 4 и более букв
				}
	}
#

#
	if (isset($title_arr) and sizeof($title_arr))
	foreach($title_arr as $i2 => $title_p2) #перебор всех заголовков
	{
		$sh_prc=0; unset($sh);
		if ($post->ID!=$i2 and isset($titles_arr4[$post->ID]) and isset($titles_arr4[$i2])) # не сравниваем сами с собой
		{
			$sh=array_intersect($titles_arr4[$post->ID],$titles_arr4[$i2]); #массив совпадений
			$sh_prc=round(sizeof($sh)*100/sizeof($titles_arr4[$post->ID]),2); #процент совпадения
		}
		if ($sh_prc>0) {$arr_id_post[$i2]=$sh_prc; $url_page[$i2]=get_permalink($i2);}
	}
	if (isset($arr_id_post) and sizeof($arr_id_post)) arsort($arr_id_post);
	if (isset($arr_id_post) and sizeof($arr_id_post)>20) $arr_id_post=array_slice($arr_id_post, 0, 20, true);
#

#	
	if (isset($arr_id_post) and sizeof($arr_id_post))
	foreach($arr_id_post as $i => $zn) #перебор всех заголовков
	{
		$tmp=get_post_meta( $i, '_zmseo_keys', true );
		if ($tmp) $tmp_arr[$i]=explode("*", $tmp);
		$link_keys[$i]['name']=esc_html( $title_arr[$i] );
		$link_keys[$i]['link']=$url_page[$i];
		if (isset($tmp_arr[$i])) $link_keys[$i]['keys']=$tmp_arr[$i];

	}
#
###
?>
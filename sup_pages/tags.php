<?php

##############################support
### Tags
if ($_POST['add_tag'] and $_POST['id_tag'] and wp_verify_nonce( $_POST['nonce'], $_SERVER['REQUEST_URI'])) #добавить
{
	$col_tag=(int)$_POST['id_tag'];
	$tag='tag_'.$col_tag;
	$arr_tag['id']=$col_tag;
	if ($_POST['color']==1) {$color_b='green'; $color_t='white';}
		if ($_POST['color']==2){$color_b='blue'; $color_t='white';}
			if ($_POST['color']==3){$color_b='red'; $color_t='white';}
				if ($_POST['color']==4){$color_b='yellow'; $color_t='black';}
					if ($_POST['color']==5){$color_b='black'; $color_t='white';}
	$arr_tag['color_b']=$color_b;
	$arr_tag['color_t']=$color_t;
	$arr_tag['name_tag']=$_POST['name_tag'];
	$arr_tag['dec_tag']=$_POST['dec_tag'];
	
	$data_tag=sanitize_post(json_encode($arr_tag));

if ($tuning[$tag]=='free') 
	$rows = $wpdb->update($name_bd, array('val_d' => $data_tag), array('name' => $tag), array( '%s' ), array( '%s' ));
else 
	$wpdb->insert($name_bd, array( 'name' => $tag, 'val_d' => $data_tag),array( '%s', '%s' ));

$tuning[$tag]=$data_tag;
echo '<p><b>Тег добавлен</b></p>';
}

if (($_POST['edit_tag'] or $_POST['delete_tag']) and $_POST['id_tag'] and wp_verify_nonce( $_POST['nonce'], $_SERVER['REQUEST_URI'])) #изменить/удалить
{
	$tag='tag_'.(int)$_POST['id_tag'];
	$arr_tag['id']=(int)$_POST['id_tag'];
	if ($_POST['color']==1) {$color_b='green'; $color_t='white';}
		if ($_POST['color']==2){$color_b='blue'; $color_t='white';}
			if ($_POST['color']==3){$color_b='red'; $color_t='white';}
				if ($_POST['color']==4){$color_b='yellow'; $color_t='black';}
					if ($_POST['color']==5){$color_b='black'; $color_t='white';}
	$arr_tag['color_b']=$color_b;
	$arr_tag['color_t']=$color_t;
	$arr_tag['name_tag']=$_POST['name_tag'];
	$arr_tag['dec_tag']=$_POST['dec_tag'];
	
	$data_tag=json_encode($arr_tag);#sanitize_post(json_encode($arr_tag));
	if ($_POST['delete_tag']) 
	{
		$data_tag='free';
		$name_bd_meta=$wpdb->prefix.'postmeta';
		$arr_meta = $wpdb->get_results("SELECT * FROM ".$name_bd_meta." WHERE meta_key='_zmseo_tags'", ARRAY_A);
		if (sizeof($arr_meta))
		foreach ($arr_meta as $i_meta => $zn_meta)
		{
			$sel_tag=json_decode($zn_meta['meta_value']);
			if ($sel_tag->$tag) 
				{
					$sel_tag->$tag='null';
					$data_tag_post=json_encode($sel_tag);
					update_post_meta($zn_meta['post_id'], '_zmseo_tags', $data_tag_post);						
				}
		}
	}

$rows = $wpdb->update($name_bd, // указываем таблицу
	array('val_d' => $data_tag), // поменяем имя 
	array('name' => $tag), // где 
	array( '%s' ), // формат для данных  %d-число, %s-строка
	array( '%s' )  // формат для где
);

$tuning[$tag]=$data_tag;
echo '<p><b>Тег изменен ['.$tag.']</b></p>';
}

###
############################################


##########################################keys
if (!$tag and $_POST['tag'] and $post->ID)
{
	$sup_tags['tag_1']=$_POST['tag_1'];
	$sup_tags['tag_2']=$_POST['tag_2'];
	$sup_tags['tag_3']=$_POST['tag_3'];
	$sup_tags['tag_4']=$_POST['tag_4'];
	$sup_tags['tag_5']=$_POST['tag_5'];

	$data_tag=sanitize_post(json_encode($sup_tags));
	add_post_meta( $post->ID, '_zmseo_tags', $data_tag, true );

	$tag = $data_tag;
}
if ($tag and $post->ID and ($_POST['tag'] or $_POST['color']))
{
	if ($_POST['tag']) 
	{
	$sup_tags['tag_1']=$_POST['tag_1'];
	$sup_tags['tag_2']=$_POST['tag_2'];
	$sup_tags['tag_3']=$_POST['tag_3'];
	$sup_tags['tag_4']=$_POST['tag_4'];
	$sup_tags['tag_5']=$_POST['tag_5'];
	}
	if ($_POST['color']) 
	{
	$sup_tags=json_decode($tag);
	$sup_tags->$_POST['color']=1;
	}

	$data_tag=sanitize_post(json_encode($sup_tags));
	update_post_meta($post->ID, '_zmseo_tags', $data_tag);
	
	$tag = $data_tag;
}

##########################

?>
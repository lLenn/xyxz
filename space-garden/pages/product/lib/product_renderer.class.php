<?php

class ProductRenderer
{
	private $manager;
	
	function ProductRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_actions()
	{
		return '<a class="mini link_button" href="' . Url :: create_url(array("page" => "add_product")). '">' . Language :: get_instance()->translate("add_product"). '</a>';
	}
	
	public function get_table()
	{
		$html = array();

		$products = $this->manager->get_data_manager()->retrieve_products();    
        $table = new Table($products);
        $table->set_table_id("products_table");
        $table->set_table_body_id("p_sortable");
        $table->set_ids("id");                  
        $table->set_row_link("edit_product", "id");
        $table->set_no_data_message(Language::get_instance()->translate("no_products"));
        $table->set_delete_button("browse_products", "delete_product", Language::get_instance()->translate("delete_product"), Language::get_instance()->translate("sure_delete_product"));
        
        $columns = array();
        $column = new Column("&nbsp;", "order");
        $column->set_style_attributes(array("width"=>"50px", "text-align"=>"right"));
        $columns[] = $column;
        $column = new Column(Language::get_instance()->translate("name"), "name");
        $column->set_style_attributes(array("width"=>"200px"));
        $columns[] = $column;
        $columns[] = new Column(Language::get_instance()->translate("description"), "description");
        $table->set_columns($columns);
        
        return $table->render_table();
    }
	
	public function get_html($product, $width, $height)
	{
		$html = array();
		$html[] = $this->get_product_html($product, $width, $height);
	
		$desc_height = 75*((1/385)*$height);
		$desc_padding = 10*((1/385)*$height);
		$desc_font = 14*((1/385)*$height);
		
		$html[] = "<div class='description_div' style='width: " . $width . "px; padding-top: " . $desc_padding . "px; height: " . $desc_height . "px; font-size: " . $desc_font . "px'>";
		$html[] = $product->get_description();
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_product_html($product, $width, $height, $mini = false)
	{
		$html[] = "<div class='media_div' style='height: " . $height . "px'>";
		if((!is_null($product->get_image()) && $product->get_image() != '') || ($mini && !is_null($product->get_video()) && $product->get_video() != ''))
		{			
			$image = "";
			if(!is_null($product->get_image()) && $product->get_image() != '')
			{
				$image = $product->get_image();
			}
			else
			{
				$image = "layout/images/video.gif";
			}
			
			if(file_exists($image))
			{
				if($size = @getimagesize($image))
				{
					if($size[1]/$height > $size[0]/$width)
					{
						$style = "height: " . $height . "px;";
					}
					else
					{
						$style = "width: " . $width . "px;";
					}
				}
				else
				{
					$style = "width: " . $width . "px; height: " . $height . "px;";
				}
				$html[] = "<table style='width: " . $width . "px; height: " . $height . "px;' style='border: 0; border-spacing: 0;'><tr><td style='vertical-align: middle; text-align: center; border: 0;padding: 0;'><img src='" . $image . "' style='" . $style . "'></td></tr></table>";
			}
			else
			{
				throw new Exception(Language :: get_instance()->translate("image_no_exist"));
			}
		}
		elseif(!is_null($product->get_video()) && $product->get_video() != '')
		{
			$video = preg_replace("/width=[\'\"][0-9]+[\'\"]/", "width='" . $width . "'", $product->get_video());
			$video = preg_replace("/height=[\'\"][0-9]+[\'\"]/", "height='" . $height . "'", $video);
			$html[] = $video;
		}
		else
		{
			throw new Exception(Language :: get_instance()->translate("no_media"));
		}
		$html[] = "</div>";
		return implode("\n", $html);
	}
	   
	public function get_form($product = null)
	{
		$html = array();
		//TODO ADD TEXT LIMITATION
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/product/javascript/product_form.js"></script>';
		$html[] = '<form method="post" action="" class="product" name="editor" id="editor">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate("name") . ' : </div>';
		$html[] = '<div class="record_input"><input type="text" name="name" size="40" value="' . (!is_null($product)?$product->get_name():"") . '"/></div>';
		$html[] = '<br class="clear_float">';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate("description") . ' : </div>';
		$html[] = '<div class="record_input"><textarea name="description" cols="75" rows="2">' . (!is_null($product)?$product->get_description():"") . '</textarea></div>';
		$html[] = '<br class="clear_float">';
		$html[] = '<br>';
		$html[] = '<br>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate("media_link") . ' : </div>';
		$html[] = '<div class="record_input"><input class="checkbox" type="radio" name="media_type" value="image"' . ((is_null($product) || is_null($product->get_video()) || $product->get_video() == '')?' checked="checked"':'') . '"/> ' . Language::get_instance()->translate("image") . '</div>';
		$html[] = '<br class="clear_float">';
		$html[] = '<div class="record_name_required"></div>';
		$html[] = '<div class="record_input"><input class="checkbox" type="radio" name="media_type" value="video"' . ((!is_null($product) && (is_null($product->get_image()) || $product->get_image() == '') && (!is_null($product->get_video()) && $product->get_video() != ''))?' checked="checked"':'') . '"/> ' . Language::get_instance()->translate("video") . '</div>';
		$html[] = '<br class="clear_float">';
		$html[] = '<div class="record_name_required">';
		$html[] = '<div class="image_type"' . ((is_null($product) || is_null($product->get_video()) || $product->get_video() == '')?'':' style="display: none;"') . '>' . Language::get_instance()->translate("image_url") . '</div>';
		$html[] = '<div class="video_type"' . ((!is_null($product) && (is_null($product->get_image()) || $product->get_image() == '') && (!is_null($product->get_video()) && $product->get_video() != ''))?'':' style="display: none;"') . '>' . Language::get_instance()->translate("video_embed") . '</div>';
		$html[] = '</div>';
		$html[] = '<div class="record_input">';
		$html[] = '<div class="image_type"' . ((is_null($product) || is_null($product->get_video()) || $product->get_video() == '')?'':' style="display: none;"') . '><input type="text" name="image_url" size="40"' . ((!is_null($product) && (is_null($product->get_video()) || $product->get_video() == ''))?' value="'.$product->get_image().'"':'') . '"/></div>';
		$html[] = '<div class="video_type"' . ((!is_null($product) && (is_null($product->get_image()) || $product->get_image() == '') && (!is_null($product->get_video()) && $product->get_video() != ''))?'':' style="display: none;"') . '><textarea cols="75" rows="5" name="video_embed">' . ((!is_null($product) && (is_null($product->get_image()) || $product->get_image() == ''))?$product->get_video():'') . '</textarea></div>';
		$html[] = '</div>';
		$html[] = '<br class="clear_float">';
		$html[] = '<br>';
		$html[] = '<br>';
		$html[] = '<div class="record_button_aligned"><a id="submit_form" class="link_button" href="javascript:;">' . Language :: get_instance()->translate("form_submit"). '</a></div>';
		$html[] = '<br class="clear_float">';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
}

?>
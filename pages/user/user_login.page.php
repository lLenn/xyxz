<?php
require_once Path::get_path() . "pages/article/lib/article_manager.class.php";

class UserLogin
{
	private $manager;
	
	function UserLogin($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_html()
	{
		$login_tried = is_string(Request::get("message"));
		$html = array();	
		$html[] = '<div id="login_container">';
		$html[] = '<div id="login_vertical_pos">';
		$html[] = '<div id="login_div">';
		//$html[] = '<div style="position: relative; float: right; z-index: 10;"><form name="language_form" method="post">' . LanguageRenderer::get_selector(Language::get_instance()->get_language()) . '</form></div>';
		$html[] = '<img alt="logo" src="'.Path::get_url_path().'layout/images/Kasteel_1.png'.'" style="position: absolute; border: 0; height:600px; width: 744px; margin-top: -20px;" />';
		
		$html[] = '<div id="login_form_div" style="' . ($login_tried?'':'visibility: hidden;') . ' margin: 0 auto; width: 450px; position: relative; top: 200px; padding: 20px; background-color: #ffffff; border: solid 3px #96BF0D; -moz-border-radius: 20px; border-radius: 20px;">';
		$html[] = '<img class="close_form" title="' . Language::get_instance()->translate(109) . '" alt="x" src="'.Path::get_url_path().'layout/images/buttons/close_good_message.png'.'" style="float: right; border: 0;"/>';
		$html[] = '<img alt="logo" src="'.Path::get_url_path().'layout/images/logo.png'.'" style="margin-right: 10px; border: 0; position: relative;"/>';
		$html[] = '<div style="float: right; margin-top: 100px">' . Display::get_message(250) . '</div>';
		$html[] = '<p class="title">' . Language::get_instance()->translate(484) . ' :</p>';
		$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_login_form();
		$html[] = '</div>';
			
		$html[] = '<div id="information_div" style="visibility: hidden; display: none; margin: 0 auto; width: 650px; height: 480px; position: relative; top: 30px; padding: 20px; background-color: #ffffff; border: solid 3px #96BF0D; -moz-border-radius: 20px; border-radius: 20px;">';
		$html[] = '<img class="close_form" title="' . Language::get_instance()->translate(109) . '" alt="x" src="'.Path::get_url_path().'layout/images/buttons/close_good_message.png'.'" style="margin-left: 630px; border: 0;"/><br class="clearfloat"/><br/>';
		$am = new ArticleManager(null);
		$page = $am->factory(ArticleManager::ARTICLE_INFORMATION);
		$html[] = '<div style="overflow: auto; height: 460px; width: 600px; margin-left: 30px;">';
		$html[] = '<img alt="logo" src="'.Path::get_url_path().'layout/images/logo.png'.'" style="margin-left: 228px; border: 0;"/>';
		$html[] = $page->get_html();
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = '<div id="registration_div" style="visibility: hidden; display: none; margin: 0 auto; width: 450px; height: auto; position: relative; top: 50px; padding: 20px; background-color: #ffffff; border: solid 3px #96BF0D; -moz-border-radius: 20px; border-radius: 20px;">';
		$html[] = '<img class="close_form" title="' . Language::get_instance()->translate(109) . '" alt="x" src="'.Path::get_url_path().'layout/images/buttons/close_good_message.png'.'" style="float: right; border: 0;"/>';
		$html[] = '<img alt="logo" src="'.Path::get_url_path().'layout/images/logo.png'.'" style="margin-right: 10px; border: 0; position: relative;"/>';
		//$html[] = '<p class="title">' . Language::get_instance()->translate(245) . ' :</p>';
		$html[] = '<div id="reg_ajax" style="margin-top: 40px">';
		$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_request_form();
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = '<div style="position: relative; left: 50px; top: 252px; z-index:6;" id="what_form_info"><a class="link_button" style="font-size: 14px" href="javascript:;"' . ($login_tried?' style="visibility: hidden;"':'') . '>' . Language::get_instance()->translate(950) . '</a></div>';
		$html[] = '<div style="position: relative; left: 50px; top: 232px; z-index:7;" id="how_form_info"><a class="link_button" style="font-size: 14px" href="javascript:;"' . ($login_tried?' style="visibility: hidden;"':'') . '>' . Language::get_instance()->translate(951) . '</a></div>';
		$html[] = '<div style="position: relative; left: 50px; top: 212px; z-index:8;" id="why_form_info"><a class="link_button" style="font-size: 14px" href="javascript:;"' . ($login_tried?' style="visibility: hidden;"':'') . '>' . Language::get_instance()->translate(952) . '</a></div>';
		$html[] = '<div style="position: relative; left: 50px; top: 192px; z-index:9;" id="adv_form_info"><a class="link_button" style="font-size: 14px" href="javascript:;"' . ($login_tried?' style="visibility: hidden;"':'') . '>' . Language::get_instance()->translate(953) . '</a></div>';
		
		$html[] = '<div style="position: relative; left: 50px; top: 166px; z-index:10;"><a id="submit_form_info" class="link_button" href="javascript:;"' . ($login_tried?' style="visibility: hidden;"':'') . '>' . Language::get_instance()->translate(789) . '</a></div>';
		$html[] = '<div style="position: relative; left: 300px; top: 156px; z-index:10;"><a id="submit_form_guest" class="link_button" href="javascript:;"' . ($login_tried?' style="visibility: hidden;"':'') . '>' . Language::get_instance()->translate(943) . '</a></div>';
		$html[] = '<div style="position: relative; left: 300px; top: 92px; z-index:10;"><a id="submit_form_alter" class="link_button" href="javascript:;"' . ($login_tried?' style="visibility: hidden;"':'') . '>' . Language::get_instance()->translate(436) . '</a></div>';
		
		//te vervangen =)
		$html[] = '<div style="position: relative; left: 550px; top: 82px; z-index:10;"><a id="submit_form_reg" class="link_button" href="' . Url::create_url(array("page" => "register")) . '"' . ($login_tried?' style="visibility: hidden;"':'') . '>' . Language::get_instance()->translate(758) . '</a></div>';
		//$html[] = '<div style="position: relative; left: 450px; top: 280px; z-index:10;" class="green_font">Heeft u nog geen account? Dan kun u zich <a href="'.Url::create_url(array('page' => 'register')).'">hier inschrijven</a></div>';
		
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '';
	}

}

?>
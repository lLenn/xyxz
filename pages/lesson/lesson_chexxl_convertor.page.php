<?php
require_once Path::get_path() . 'pages/lesson/lesson_continuation_availability.page.php';

class LessonChexxlConvertor
{
	private $manager;
	private $stage = 1;
	
	function LessonChexxlConvertor($manager)
	{
		$this->manager = $manager;
	}

	public function get_title()
	{
		return "";
	}
	
	
	public function get_description()
	{
		return '';
	}
	
	public function save_changes()
	{

	}
	
	public function get_html()
	{
		$html = array();
		$user = $this->manager->get_user();
		$html[] = '<div id="lesson_chexxl_convertor">';
		if($user->is_admin() || $user->get_id() == LessonManager::ID_CHEXXL)
		{
			$html[] = '<div style="padding-top: 10px;">';
			$html[] = '<h3 class="title">Chexxl convertor</h3>';
			$html[] = $this->manager->get_renderer()->get_chexxl_convertor_form();
			$html[] = '</div>';
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>
<?php
namespace application\weblcms;

use common\libraries\Header;
use core\admin\AdminManager;
use common\libraries\Redirect;
use common\libraries\Display;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Translation;

/**
 * $Id: course_type_selector.class.php 218 2010-03-26 14:21:26Z Yannick &
 * Tristan $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component allows to select a coursetype
 */
class WeblcmsManagerCourseTypeSelectorComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {

        if (!course_type\DataManager :: count(CourseType :: class_name()))
        {
            $this->simple_redirect(array('go' => WeblcmsManager :: ACTION_CREATE_COURSE));
        }

        if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }

        if (!$this->get_user()->is_teacher() && !$this->get_user()->is_platform_admin())
        {
            throw new \common\libraries\NotAllowedException();
        }

        $form = new CourseTypeSelectForm($this->get_url());

        if ($form->validate() || $form->get_size() == 1)
        {
            $this->simple_redirect(
                array(
                    'go'          => WeblcmsManager :: ACTION_CREATE_COURSE,
                    'course_type' => $form->get_selected_id()
                )
            );
        }
        else
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
            $form->display();
            $this->display_footer();
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(
                new Breadcrumb(
                    Redirect :: get_link(
                        AdminManager :: APPLICATION_NAME, array(
                            AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER
                        ), array(), false, Redirect :: TYPE_CORE
                    ), Translation :: get('TypeName', null, 'core\admin')
                )
            );
            $breadcrumbtrail->add(
                new Breadcrumb(
                    Redirect :: get_link(
                        AdminManager :: APPLICATION_NAME, array(
                            AdminManager :: PARAM_ACTION              => AdminManager :: ACTION_ADMIN_BROWSER,
                            DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME
                        ), array(), false, Redirect :: TYPE_CORE
                    ), Translation :: get('Courses')
                )
            );
            $breadcrumbtrail->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER
                        )
                    ), Translation :: get('CourseTypeList')
                )
            );
        }
        $breadcrumbtrail->add_help('weblcms_course_type_selector');
    }

    public function get_additional_parameters()
    {
        return array();
    }

}

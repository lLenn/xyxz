<?php
namespace application\weblcms\course;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Utilities;

use user\User;

/**
 * This class describes an action to update a course
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseManagerUpdateComponent extends CourseManagerCourseFormActionComponent
{

    /**
     * **************************************************************************************************************
     * Implemented Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the course for this form action
     *
     * @return Course
     */
    public function get_course()
    {
        return $this->get_selected_course();
    }

    /**
     * Handles the course form
     *
     * @param $course_type Course
     * @param
     *            string[string]
     *
     * @return boolean
     */
    public function handle_form(Course $course, $form_values)
    {
        if(!$course->update() || !$course->update_course_settings_from_values($form_values))
        {
            return false;
        }

        $titular = \user\DataManager :: retrieve_by_id(User :: class_name(), $course->get_titular_id());
        if($course->is_subscribed_as_course_admin($titular))
        {
            return true;
        }

        $course_user_relation = DataManager :: retrieve_course_user_relation_by_course_and_user(
            $course->get_id(), $course->get_titular_id()
        );

        if(!$course_user_relation)
        {
            $course_user_relation = new CourseUserRelation();
            $course_user_relation->set_course($course);
            $course_user_relation->set_user($titular);
        }
        $course_user_relation->set_status(CourseUserRelation::STATUS_TEACHER);
        return $course_user_relation->save();
    }

    /**
     * Returns the redirect message with the given succes
     *
     * @param boolean $succes
     */
    public function  get_redirect_message($succes)
    {
        $message = $succes ? 'ObjectUpdated' : 'ObjectNotUpdated';

        return Translation :: get(
            $message, array('OBJECT' => Translation :: get('Course')),
            Utilities :: COMMON_LIBRARIES
        );
    }

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Breadcrumbs are built semi automatically with the given application,
     * subapplication, component...
     * Use this function to add other breadcrumbs between the application /
     * subapplication and the current component
     *
     * @param $breadcrumbtrail \common\libraries\BreadcrumbTrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_update');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_url(), Translation :: get('CourseManagerBrowseComponent'))
        );
    }

    /**
     * Returns the registered parameters for this component
     *
     * @param
     *            string[]
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_COURSE_ID);
    }
}

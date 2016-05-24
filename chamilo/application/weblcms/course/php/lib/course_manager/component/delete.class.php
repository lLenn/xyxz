<?php
namespace application\weblcms\course;

use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\ObjectNotExistException;

/**
 * This class describes an action to delete a course
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseManagerDeleteComponent extends CourseManager
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $course_ids = $this->get_selected_course_ids();

        foreach ($course_ids as $course_id)
        {
            $course = DataManager :: retrieve(Course :: class_name(), $course_id);

            if (!$course)
            {
                throw new ObjectNotExistException(Translation :: get('Course'), $course_id);
            }

            if (!$course->delete())
            {
                $failures++;
            }
        }

        $message = $this->get_result(
            $failures, count($course_ids), 'SelectedCourseNotDeleted', 'SelectedCourseNotDeleted',
            'SelectedCourseDeleted', 'SelectedCourseDeleted'
        );

        $this->redirect(
            $message, ($failures > 0), array(self :: PARAM_ACTION => self :: ACTION_BROWSE), array(
                self :: PARAM_COURSE_ID
            )
        );
    }

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
        $breadcrumbtrail->add_help('weblcms_course_deleter');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_url(), Translation :: get('CourseManagerBrowseComponent'))
        );
    }

    /**
     * Returns additional parameters that need to be registered and are used in
     * every url generated by this component
     *
     * @return String[]
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_COURSE_ID);
    }

}

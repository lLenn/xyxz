<?php
namespace application\weblcms\course;

use common\libraries\AndCondition;
use common\libraries\TableSupport;

/**
 * This class describes a browser for the subscribed courses
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseManagerBrowseSubscribedCoursesComponent extends CourseManagerBrowseSubscriptionCoursesComponent implements
    TableSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the condition for the table
     *
     * @param $object_table_class_name
     *
     * @return \common\libraries\Condition
     */
    public function get_table_condition($object_table_class_name)
    {
        $conditions = array();

        $parent_condition = parent :: get_table_condition($object_table_class_name);
        if ($parent_condition)
        {
            $conditions[] = $parent_condition;
        }

        $conditions[] = DataManager :: get_user_courses_condition($this->get_user());

        return new AndCondition($conditions);
    }

    /**
     * Returns the course table for this component
     *
     * @return CourseTable
     */
    protected function get_course_table()
    {
        return new SubscribedCourseTable($this);
    }
}

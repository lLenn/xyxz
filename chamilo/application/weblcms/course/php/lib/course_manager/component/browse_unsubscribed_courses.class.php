<?php
namespace application\weblcms\course;

use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\NotCondition;
use common\libraries\InCondition;
use common\libraries\SubselectCondition;

/**
 * This class describes a browser for the courses where a user is not subscribed
 * to
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseManagerBrowseUnsubscribedCoursesComponent extends CourseManagerBrowseSubscriptionCoursesComponent
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

        $user = $this->get_user();

        $user_condition = new EqualityCondition(
            CourseUserRelation :: PROPERTY_USER_ID,
            $user->get_id(),
            CourseUserRelation :: get_table_name()
        );

        $conditions[] = new NotCondition(
            new SubselectCondition(
                Course :: PROPERTY_ID,
                CourseUserRelation :: PROPERTY_COURSE_ID,
                CourseUserRelation :: get_table_name(),
                $user_condition,
                Course :: get_table_name()
            )
        );

        $groups = $user->get_groups(true);
        if ($groups)
        {
            $groups_condition = new InCondition(
                CourseGroupRelation :: PROPERTY_GROUP_ID,
                $user->get_groups(true),
                CourseGroupRelation :: get_table_name()
            );

            $conditions[] = new NotCondition(
                new SubselectCondition(
                    Course :: PROPERTY_ID,
                    CourseGroupRelation :: PROPERTY_COURSE_ID,
                    CourseGroupRelation :: get_table_name(),
                    $groups_condition,
                    Course :: get_table_name()
                )
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * Returns the course table for this component
     *
     * @return CourseTable
     */
    protected function get_course_table()
    {
        return new UnsubscribedCourseTable($this);
    }
}

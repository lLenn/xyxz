<?php
namespace application\weblcms\course;

use common\libraries\RecordTableDataProvider;

/**
 * This class describes a data provider for the subscribed course table
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class SubscribedCourseTableDataProvider extends RecordTableDataProvider
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the data as a resultset
     *
     *
     * @param \common\libraries\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \common\libraries\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager :: retrieve_users_courses_with_course_type(
            $condition, $offset, $count, $order_property
        );
    }

    /**
     * Counts the data
     *
     *
     * @param \common\libraries\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager :: count_user_courses($condition);
    }
}

<?php
namespace application\weblcms\course_type;

use common\libraries\NewObjectTableDataProvider;
use common\libraries\DataClassRetrievesParameters;
use common\libraries\ObjectTableOrder;



/**
 * This class describes a data provider for the course type table
 *
 * @package \application\weblcms\course_type
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeTableDataProvider extends NewObjectTableDataProvider
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the objects for this table
     *
     * @param $offset int
     * @param $count int
     * @param $order_property String
     *
     * @return \common\libraries\ResultSet
     */
    public function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property(new ObjectTableOrder(CourseType :: PROPERTY_DISPLAY_ORDER));
        $parameters = new DataClassRetrievesParameters($this->get_condition(), $count, $offset, $order_property);

        return DataManager :: retrieves(CourseType :: class_name(), $parameters);
    }

    /**
     * Counts the number of objects for this table
     *
     * @return int
     */
    public function get_object_count()
    {
        return DataManager :: count(CourseType :: class_name(), $this->get_condition());
    }
}

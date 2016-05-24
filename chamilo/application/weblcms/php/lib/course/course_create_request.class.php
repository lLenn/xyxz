<?php
namespace application\weblcms;

use common\libraries\Utilities;

/**
 * $Id: course_request.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 *
 * @package application.lib.weblcms.course
 */

class CourseCreateRequest extends CommonRequest
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_NAME = 'course_name';
    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_COURSE_NAME,
                self :: PROPERTY_COURSE_TYPE_ID
            )
        );
    }

    public function get_course_name()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_NAME);
    }

    public function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    public function set_course_name($course_name)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_NAME, $course_name);
    }

    public function set_course_type_id($course_type_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    public function set_course_category_id($course_category_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_CATEGORY_ID, $course_category_id);
    }

    public static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}

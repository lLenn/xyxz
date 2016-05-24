<?php
namespace application\weblcms\course;

use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\Utilities;

use application\weblcms\CourseManagementRights;

/**
 * This class describes the default cell renderer for the unsubscribed course
 * table
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class UnsubscribedCourseTableCellRenderer extends CourseTableCellRenderer
{

    /**
     * Returns the actions toolbar
     *
     * @param $course Course
     *
     * @return String
     */
    public function get_actions($course)
    {
        if (CourseManagementRights :: get_instance()->is_allowed(
            CourseManagementRights :: DIRECT_SUBSCRIBE_RIGHT, $course[Course :: PROPERTY_ID]
        )
        )
        {
            $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Subscribe', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_subscribe.png',
                    $this->get_component()->get_subscribe_to_course_url($course[Course :: PROPERTY_ID]),
                    ToolbarItem :: DISPLAY_ICON
                )
            );

            return $toolbar->as_html();
        }
    }
}

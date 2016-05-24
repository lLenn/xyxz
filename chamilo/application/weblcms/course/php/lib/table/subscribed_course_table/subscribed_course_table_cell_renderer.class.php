<?php
namespace application\weblcms\course;

use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\Utilities;

use application\weblcms\CourseManagementRights;


/**
 * This class describes the default cell renderer for the subscribed course
 * table
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class SubscribedCourseTableCellRenderer extends CourseTableCellRenderer
{
    /****************************************************************************************************************
     * Inherited Functionality                                                                                      *
     ****************************************************************************************************************/

    /**
     * Returns the actions toolbar
     *
     * @param $course Course
     *
     * @return String
     */
    public function get_actions($course)
    {
        if (DataManager :: is_user_direct_subscribed_to_course(
            $this->get_component()->get_user_id(),
            $course[Course :: PROPERTY_ID]
        ) && CourseManagementRights :: get_instance()->is_allowed(
            CourseManagementRights :: DIRECT_UNSUBSCRIBE_RIGHT, $course[Course :: PROPERTY_ID]
        ) &&
            !$this->is_subscribed_as_course_admin($course[Course :: PROPERTY_ID], $this->get_component()->get_user())
        )
        {
            $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Unsubscribe', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_unsubscribe.png',
                    $this->get_component()->get_unsubscribe_from_course_url($course[Course :: PROPERTY_ID]),
                    ToolbarItem :: DISPLAY_ICON
                )
            );

            return $toolbar->as_html();
        }

    }

    /**
     * Checks whether the current user is subscribed as course admin of the given course
     *
     * @param int $course_id
     * @param User $user
     *
     * @return boolean
     */
    protected function is_subscribed_as_course_admin($course_id, $user)
    {
        return DataManager :: is_teacher_by_direct_subscription($course_id, $user->get_id()) ||
            DataManager :: is_teacher_by_platform_group_subscription($course_id, $user);
    }
}

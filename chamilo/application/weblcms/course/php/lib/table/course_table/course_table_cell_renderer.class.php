<?php
namespace application\weblcms\course;

use common\libraries\Toolbar;
use common\libraries\DataClassPropertyTableColumn;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\RecordTableCellRenderer;
use common\libraries\TableCellRendererActionsColumnSupport;

use application\weblcms\course_type\CourseType;
/**
 * This class describes the default cell renderer for the course table
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTableCellRenderer extends RecordTableCellRenderer implements
    TableCellRendererActionsColumnSupport
{
    /****************************************************************************************************************
     * Inherited Functionality                                                                                      *
     ****************************************************************************************************************/

    /**
     * Renders a cell for a given object
     *
     * @param $column \common\libraries\ObjectTableColumn
     *
     * @param mixed $course
     *
     * @return String
     */
    public function render_cell($column, $course)
    {
        if($column instanceof DataClassPropertyTableColumn)
        {
            switch($column->get_class_name())
            {
                case Course :: class_name() :
                {
                    switch ($column->get_name())
                    {
                        case Course :: PROPERTY_TITLE:
                            $course_title = parent :: render_cell($column, $course);
                            $course_home_url = $this->get_component()->get_view_course_home_url(
                                $course[Course :: PROPERTY_ID]
                            );

                            return '<a href="' . $course_home_url . '">' . $course_title . '</a>';
                        case Course :: PROPERTY_TITULAR_ID :
                            return \user\DataManager :: get_fullname_from_user(
                                $course[Course :: PROPERTY_TITULAR_ID], Translation :: get('TitularUnknown')
                            );
                    }
                    break;
                }
                case CourseType :: class_name() :
                {
                    if($column->get_name() == CourseType :: PROPERTY_TITLE)
                    {
                        $course_type_title = $course[Course :: PROPERTY_COURSE_TYPE_TITLE];
                        return !$course_type_title ? Translation :: get('NoCourseType') : $course_type_title;
                    }
                }
            }
        }

        return parent :: render_cell($column, $course);
    }

    /**
     * Returns the actions toolbar
     *
     * @param mixed $course
     *
     * @return String
     */
    public function get_actions($course)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ViewCourseHome'),
                Theme :: get_common_image_path() . 'action_home.png',
                $this->get_component()->get_view_course_home_url($course[Course :: PROPERTY_ID]),
                ToolbarItem :: DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: get_common_image_path() . 'action_edit.png',
                $this->get_component()->get_update_course_url($course[Course :: PROPERTY_ID]),
                ToolbarItem :: DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: get_common_image_path() . 'action_delete.png',
                $this->get_component()->get_delete_course_url($course[Course :: PROPERTY_ID]),
                ToolbarItem :: DISPLAY_ICON,
                true
            )
        );

        return $toolbar->as_html();
    }

}

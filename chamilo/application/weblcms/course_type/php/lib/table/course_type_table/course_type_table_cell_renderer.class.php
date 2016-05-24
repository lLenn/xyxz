<?php
namespace application\weblcms\course_type;

use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\NewObjectTableCellRenderer;
use common\libraries\NewObjectTableCellRendererActionsColumnSupport;

/**
 * This class describes the default cell renderer for the course type table
 *
 * @package \application\weblcms\course_type
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeTableCellRenderer extends NewObjectTableCellRenderer implements
    NewObjectTableCellRendererActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a cell for a given object
     *
     * @param $column \common\libraries\ObjectTableColumn
     *
     * @param $course_type CourseType
     *
     * @return String
     */
    public function render_cell($column, $course_type)
    {
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case CourseType :: PROPERTY_TITLE :
                $name = parent :: render_cell($column, $course_type);
                $name_short = $name;
                if (strlen($name_short) > 53)
                {
                    $name_short = mb_substr($name_short, 0, 50) . '&hellip;';
                }
                return '<a href="' . $this->get_component()->get_view_course_type_url($course_type->get_id()) .
                    '" title="' . $name . '">' . $name_short . '</a>';

            case CourseType :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $course_type));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }
                return $description;

            case CourseType :: PROPERTY_ACTIVE :
                if ($course_type->is_active())
                {
                    Return Translation :: get('ConfirmTrue', null, Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    Return Translation :: get('ConfirmFalse', null, Utilities :: COMMON_LIBRARIES);
                }
        }

        return parent :: render_cell($column, $course_type);
    }

    /**
     * Returns the actions toolbar
     *
     * @param $course_type CourseType
     *
     * @return String
     */
    public function get_object_actions($course_type)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($course_type->is_active())
        {
            $activation_translation = Translation :: get('Deactivate', null, Utilities :: COMMON_LIBRARIES);
            $activation_image = Theme :: get_common_image_path() . 'action_visible.png';
        }
        else
        {
            $activation_translation = Translation :: get('Activate', null, Utilities :: COMMON_LIBRARIES);
            $activation_image = Theme :: get_common_image_path() . 'action_invisible.png';
        }

        $toolbar->add_item(
            new ToolbarItem(
                $activation_translation, $activation_image, $this->get_component()
                    ->get_change_course_type_activation_url($course_type->get_id()),
                ToolbarItem :: DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: get_common_image_path() . 'action_edit.png', $this->get_component()
                    ->get_update_course_type_url($course_type->get_id()),
                ToolbarItem :: DISPLAY_ICON
            )
        );

        if (!DataManager :: has_course_type_courses($course_type->get_id()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_delete.png', $this->get_component()
                        ->get_delete_course_type_url($course_type->get_id()),
                    ToolbarItem :: DISPLAY_ICON, true
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('DeleteNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_delete_na.png', null, ToolbarItem :: DISPLAY_ICON
                )
            );
        }

        $display_order = $course_type->get_display_order();

        if ($display_order > 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveUp', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_up.png', $this->get_component()
                        ->get_move_course_type_url(
                            $course_type->get_id(), CourseTypeManager :: MOVE_DIRECTION_UP
                        ),
                    ToolbarItem :: DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveUpNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem :: DISPLAY_ICON
                )
            );
        }

        $max_objects = $this->get_object_table()->get_data_provider()->get_object_count();

        if ($display_order < $max_objects)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveDown', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_down.png', $this->get_component()
                        ->get_move_course_type_url(
                            $course_type->get_id(), CourseTypeManager :: MOVE_DIRECTION_DOWN
                        ),
                    ToolbarItem :: DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveDownNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem :: DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }
}

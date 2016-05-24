<?php
namespace application\weblcms\course_type;

use common\libraries\Breadcrumb;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;

use application\weblcms\course\Course;

/**
 * This class describes a viewer for a given course type
 *
 * @package \application\weblcms\course_type
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeManagerViewComponent extends CourseTypeManager
{
    /**
     * Keeps track of the action bar
     *
     * @var \common\libraries\ActionBarRenderer
     */
    private $action_bar;

    /****************************************************************************************************************
     * Inherited Functionality                                                *
     ****************************************************************************************************************/

    public function run()
    {
        $html = $this->get_html();

        $this->display_header();
        echo $html;
        $this->display_footer();
    }

    /**
     * Breadcrumbs are built semi automatically with the given application, subapplication, component...
     * Use this function to add other breadcrumbs between the application / subapplication and the current component
     *
     * @param \common\libraries\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_type_viewer');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_type_url(), Translation :: get('CourseTypeManagerBrowseComponent'))
        );
    }

    /**
     * Returns additional parameters that need to be registered and are used in every url generated by this component
     *
     * @return String[]
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_COURSE_TYPE_ID);
    }

    /****************************************************************************************************************
     * Helper Functionality                                                *
     ****************************************************************************************************************/

    /**
     * Returns the html for this component
     *
     * @return String
     */
    private function get_html()
    {
        $html = array();

        $course_type = $this->get_selected_course_type();
        $this->action_bar = $this->get_action_bar($course_type);

        $html[] = '<div>';
        $html[] = $this->action_bar->as_html() . '<br />';
        $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(';
        $html[] = Theme :: get_common_image_path() . 'place_group.png);">';
        $html[] = '<div class="title">' . Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = $course_type->get_description();
        $html[] = '</div>';
        $html[] = '<div class="content_object" style="background-image: url(';
        $html[] = Theme :: get_common_image_path() . 'place_publications.png);">';
        $html[] = '<div class="title">' . Translation :: get('Courses') . '</div>';
        $html[] = $this->get_courses_table_html($course_type);
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * Returns the html for the courses table that belong to the given course type
     *
     * @param CourseType $course_type
     *
     * @return String
     */
    private function get_courses_table_html(CourseType $course_type)
    {
        //$table = new AdminCourseBrowserTable($this, $this->get_parameters(), $this->get_condition($course_type));
        //return $table->as_html();
    }

    /**
     * Returns the condition for this
     *
     * @param CourseType $course_type
     *
     * @return \common\libraries\Condition
     */
    /*private function get_condition(CourseType $course_type)
    {
        $conditions = array();

        $conditions[] = $this->action_bar->get_conditions(array(Course :: PROPERTY_TITLE));
        $conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course_type->get_id());

        $condition_count = count($conditions);

        if ($condition_count > 1)
        {
            return new AndCondition($conditions);
        }
        elseif ($condition_count == 1)
        {
            return $conditions[0];
        }
        else
        {
            return null;
        }

    }*/

    /**
     * Creates and returns the action bar
     *
     * @return \common\libraries\ActionBarRenderer
     */
    private function get_action_bar(CourseType $course_type)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: get_common_image_path() . 'action_edit.png',
                $this->get_update_course_type_url($course_type->get_id()),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL
            )
        );

        if (!DataManager :: has_course_type_courses($course_type->get_id()))
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_delete.png',
                    $this->get_delete_course_type_url($course_type->get_id()),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL, true
                )
            );
        }
        else
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('DeleteNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: get_common_image_path() . 'action_delete_na.png',
                    null, ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );
        }

        return $action_bar;
    }

}

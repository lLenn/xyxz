<?php
namespace application\weblcms\course;

use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\TableFormActionsSupport;
use common\libraries\RecordTable;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * This class describes a table for the course object
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTable extends RecordTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = CourseManager :: PARAM_COURSE_ID;

    /****************************************************************************************************************
     * Inherited Functionality                                                                                      *
     ****************************************************************************************************************/

    /**
     * Returns the available table actions
     */
    public function get_implemented_form_actions()
    {
        $actions = new ObjectTableFormActions(__NAMESPACE__, CourseManager :: PARAM_ACTION);

        $actions->add_form_action(
            new ObjectTableFormAction(
                CourseManager :: ACTION_DELETE,
                Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)
            )
        );

        return $actions;
    }
}

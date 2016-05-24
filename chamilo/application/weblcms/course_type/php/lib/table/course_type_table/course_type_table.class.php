<?php
namespace application\weblcms\course_type;

use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\NewObjectTable;
use common\libraries\NewObjectTableFormActionsSupport;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * This class describes a table for the course type object
 *
 * @package \application\weblcms\course_type
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeTable extends NewObjectTable implements NewObjectTableFormActionsSupport
{
    const TABLE_IDENTIFIER = CourseTypeManager :: PARAM_COURSE_TYPE_ID;
    const DEFAULT_ROW_COUNT = 20;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the available table actions
     */
    public function get_implemented_form_actions()
    {
        $actions = new ObjectTableFormActions(__NAMESPACE__, CourseTypeManager :: PARAM_ACTION);

        $actions->add_form_action(
            new ObjectTableFormAction(
                CourseTypeManager :: ACTION_DELETE,
                Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)
            )
        );

        $actions->add_form_action(
            new ObjectTableFormAction(
                CourseTypeManager :: ACTION_ACTIVATE,
                Translation :: get('ActivateSelected', null, Utilities :: COMMON_LIBRARIES), false
            )
        );

        $actions->add_form_action(
            new ObjectTableFormAction(
                CourseTypeManager :: ACTION_DEACTIVATE,
                Translation :: get('DeactivateSelected', null, Utilities :: COMMON_LIBRARIES), false
            )
        );

        return $actions;
    }
}

<?php
namespace application\weblcms\course_type;

use common\libraries\NewObjectTableColumnModel;
use common\libraries\NewObjectTableColumnModelActionsColumnSupport;
use common\libraries\ObjectTableColumn;

/**
 * This class describes the column model for the course type table
 *
 * @package \application\weblcms\course_type
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeTableColumnModel extends NewObjectTableColumnModel implements
    NewObjectTableColumnModelActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new ObjectTableColumn(CourseType :: PROPERTY_TITLE, false));
        $this->add_column(new ObjectTableColumn(CourseType :: PROPERTY_DESCRIPTION, false));
        $this->add_column(new ObjectTableColumn(CourseType :: PROPERTY_ACTIVE, false));
    }
}

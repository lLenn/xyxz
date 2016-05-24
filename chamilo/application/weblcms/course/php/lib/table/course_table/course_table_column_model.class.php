<?php
namespace application\weblcms\course;

use common\libraries\Translation;
use common\libraries\DataClassPropertyTableColumn;
use common\libraries\RecordTableColumnModel;
use common\libraries\TableColumnModelActionsColumnSupport;

use application\weblcms\course_type\CourseType;

/**
 * This class describes the column model for the course table
 *
 * @package \application\weblcms\course
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    /****************************************************************************************************************
     * Inherited Functionality                                                                                      *
     ****************************************************************************************************************/

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Course :: class_name(), Course :: PROPERTY_VISUAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(Course :: class_name(), Course :: PROPERTY_TITLE));

        $this->add_column(
            new DataClassPropertyTableColumn(Course :: class_name(), Course :: PROPERTY_TITULAR_ID, null, false)
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                CourseType :: class_name(), CourseType :: PROPERTY_TITLE, Translation :: get('CourseType')
            )
        );
    }
}

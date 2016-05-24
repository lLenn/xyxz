<?php
namespace application\weblcms;

use application\weblcms\course\DataManager as CourseDataManager;
use application\weblcms\course\Course;
use common\libraries\DatetimeUtilities;
use user\User;
use user\UserDataManager;
use common\libraries\FormValidator;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\FormValidatorHtmlEditorOptions;
use common\libraries\ObjectTableOrder;

/**
 * $Id: course_request_form.class.php 2 2010-02-25 11:43:06Z Yannick & Tristan $
 *
 * @package application.lib.weblcms.course_type
 */

class CourseRequestForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_VIEW = 3;

    const CHOOSE_DATE = 'choose date';

    private $form_type;
    private $course;
    private $parent;
    private $request;
    private $user_id;
    private $multiple_users;
    private $request_user_id;

    public function __construct(
        $form_type, $action, $course, $parent, $request, $multiple_users = false,
        $request_user_id = null
    )
    {
        parent :: __construct('course_request', 'post', $action);
        $this->multiple_users = $multiple_users;
        $this->parent = $parent;
        $this->request = $request;
        $this->form_type = $form_type;
        $this->course = $course;
        $this->user_id = $parent->get_user_id();

        if (!$request_user_id)
        {
            $request_user_id = $this->user_id;
        }

        $this->request_user_id = $request_user_id;

        if ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creating_form();
        }

        if ($this->form_type == self :: TYPE_VIEW)
        {
            $this->build_viewing_form();
        }

        $this->setDefaults();
        $this->add_progress_bar(2);
    }

    public function build_creating_form()
    {
        $this->build_request_form();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive update')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_viewing_form()
    {
        $this->build_request_form();

        /*$buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation :: get('Print'),
            array('class' => 'positive update')
        );*/

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_request_form()
    {
        $this->addElement('html', '<div class="clear">&nbsp;</div><br/>');
        if ($this->form_type == self :: TYPE_CREATE)
        {

            $this->addElement('category', Translation :: get('CourseRequestProperties'));
            if ($this->multiple_users)
            {
                $udm = UserDataManager :: get_instance();
                $user_alias = $udm->get_alias(User :: get_table_name());
                $order = array();
                $order[] = new ObjectTableOrder(User :: PROPERTY_LASTNAME, SORT_ASC, $user_alias);
                $order[] = new ObjectTableOrder(User :: PROPERTY_FIRSTNAME, SORT_ASC, $user_alias);
                $users_result = $udm->retrieve_users(null, null, null, $order);
                // //$wdm->retrieve_course_subscribe_users_by_right(CourseGroupSubscribeRight
                // :: SUBSCRIBE_REQUEST, $this->parent->get_course());
                $users = array();
                while ($user = $users_result->next_result())
                {
                    $user_name = $user->get_fullname();
                    $users[$user->get_id()] = $user_name;
                }
                $this->addElement(
                    'select', CommonRequest :: PROPERTY_USER_ID, Translation :: get('User', null, 'user'), $users
                );
            }
            else
            {
                $user_name = UserDataManager :: get_instance()->retrieve_user($this->request_user_id)->get_fullname();
                $this->addElement('static', 'user', Translation :: get('User', null, 'user'), $user_name);
            }

            if ($this->request instanceof CourseCreateRequest)
            {
                // CTODO add right checks
                // $wdm->retrieve_course_types_by_user_right($this->parent->get_user(),
                // CourseTypeGroupCreationRight :: CREATE_REQUEST);
                $course_type_objects = course_type\DataManager :: retrieve_active_course_types();
                $course_types = array();

                $course_management_rights = CourseManagementRights :: get_instance();

                while ($course_type = $course_type_objects->next_result())
                {
                    if (
                        $course_management_rights->is_allowed(
                            CourseManagementRights :: REQUEST_COURSE_RIGHT, $course_type->get_id(),
                            CourseManagementRights :: TYPE_COURSE_TYPE
                        )
                    )
                    {
                        $course_types[$course_type->get_id()] = $course_type->get_title();
                    }
                }

                $this->addElement(
                    'select', CourseCreateRequest :: PROPERTY_COURSE_TYPE_ID,
                    Translation :: get('CourseType'), $course_types, array('class' => 'course_type_selector')
                );

                // $course_categories = array();
                // $course_category_objects =
                // DataManager :: retrieve_course_categories_ordered_by_name();
                // while ($course_category =
                // $course_category_objects->next_result())
                // {
                // $course_categories[$course_category->get_id()] =
                // $course_category->get_name();
                // }
                // $this->addElement('select', CourseCreateRequest ::
                // PROPERTY_COURSE_CATEGORY_ID,
                // Translation :: get('CourseCategory'), $course_categories,
                // array(
                // 'class' => 'course_category_selector'));

                // $this->addRule(CourseCreateRequest ::
                // PROPERTY_COURSE_TYPE_ID, Translation ::
                // get('ThisFieldIsRequired', null, Utilities ::
                // COMMON_LIBRARIES), 'required');
                $this->add_textfield(
                    CourseCreateRequest :: PROPERTY_COURSE_NAME, Translation :: get('CourseName'), true
                );
                $this->addRule(
                    CourseCreateRequest :: PROPERTY_COURSE_NAME,
                    Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required'
                );
            }
            else
            {
                $course_name = $this->course->get_title();
                $this->addElement('static', 'course', Translation :: get('CourseName'), $course_name);
            }

            $this->add_textfield(CommonRequest :: PROPERTY_SUBJECT, Translation :: get('Subject'), true);

            $this->add_html_editor(
                CommonRequest :: PROPERTY_MOTIVATION, Translation :: get('Motivation'), true,
                array(FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR => 'BasicMarkup')
            );

        }

        if ($this->form_type == self :: TYPE_VIEW)
        {
            $this->addElement('category', Translation :: get(Utilities :: get_classname_from_object($this->request)));

            $name_user =
                UserDataManager :: get_instance()->retrieve_user($this->request->get_user_id())->get_fullname();
            $this->addElement('static', 'request', Translation :: get('User', null, 'user'), $name_user);

            if ($this->request instanceof CourseCreateRequest)
            {
                $this->addElement(
                    'static', 'request', Translation :: get('CourseName'),
                    course_type\DataManager :: retrieve(
                        CourseType :: class_name(), $this->request->get_course_type_id()
                    )->get_title()
                );
                $request_name = $this->request->get_course_name();
            }
            else
            {
                $request_name = CourseDataManager :: retrieve(
                    Course :: class_name(), $this->request->get_course_id()
                )->get_title();
            }

            $this->addElement('static', 'request', Translation :: get('CourseName'), $request_name);

            $request_subject = $this->request->get_subject();
            $this->addElement('static', 'request', Translation :: get('Subject'), $request_subject);

            $motivation = $this->request->get_motivation();
            $this->addElement('static', 'request', Translation :: get('Motivation'), $motivation);

            $creation_date = DatetimeUtilities :: format_locale_date(null, $this->request->get_creation_date());
            $this->addElement('static', 'request', Translation :: get('CreationDate'), $creation_date);

            $decision = $this->request->get_decision();
            $decision_date = DatetimeUtilities :: format_locale_date(null, $this->request->get_decision_date());
            switch ($decision)
            {
                case CommonRequest :: ALLOWED_DECISION :
                    $this->addElement(
                        'static', 'request', Translation :: get('Decision'), Translation :: get('Allowed')
                    );
                    $this->addElement(
                        'static', 'request',
                        Translation :: get('ConfirmOn', null, Utilities :: COMMON_LIBRARIES), $decision_date
                    );
                    break;
                case CommonRequest :: DENIED_DECISION :
                    $this->addElement(
                        'static', 'request', Translation :: get('Decision'), Translation :: get('Denied')
                    );
                    $this->addElement(
                        'static', 'request',
                        Translation :: get('ConfirmOn', null, Utilities :: COMMON_LIBRARIES), $decision_date
                    );
                    break;
                default :
                    $this->addElement(
                        'static', 'request', Translation :: get('Decision'), Translation :: get('NoDecisionYet')
                    );
                    break;
            }
        }
        $this->addElement('category');
    }

    public function create_request()
    {
        $values = $this->exportValues();

        $course = $this->course;
        $request = $this->request;

        if ($this->request instanceof CourseCreateRequest)
        {
            $request->set_course_name($values[CourseCreateRequest :: PROPERTY_COURSE_NAME]);
            $request->set_course_type_id($values[CourseCreateRequest :: PROPERTY_COURSE_TYPE_ID]);
        }
        else
        {
            $request->set_course_id($course->get_id());
        }

        if ($this->multiple_users)
        {
            $request->set_user_id($values[CommonRequest :: PROPERTY_USER_ID]);
        }
        else
        {
            $request->set_user_id($this->request_user_id);
        }

        $request->set_subject($values[CommonRequest :: PROPERTY_SUBJECT]);
        $request->set_motivation($values[CommonRequest :: PROPERTY_MOTIVATION]);
        $request->set_creation_date(time());
        $request->set_decision_date($values[CommonRequest :: PROPERTY_DECISION_DATE]);
        $request->set_decision(CommonRequest :: NO_DECISION);

        if (!$request->create())
        {
            return false;
        }

        return true;
    }

    public function setDefaults($defaults = array())
    {
        $request = $this->request;

        if ($this->request instanceof CourseCreateRequest)
        {
            $defaults[CourseCreateRequest :: PROPERTY_COURSE_NAME] = $request->get_course_name();
        }
        else
        {
            $defaults[CourseRequest :: PROPERTY_COURSE_ID] = $request->get_course_id();
        }
        $defaults[CommonRequest :: PROPERTY_USER_ID] = $request->get_user_id();
        $defaults[CommonRequest :: PROPERTY_SUBJECT] = $request->get_subject();
        $defaults[CommonRequest :: PROPERTY_MOTIVATION] = $request->get_motivation();
        $defaults[CommonRequest :: PROPERTY_CREATION_DATE] = $request->get_creation_date();
        $defaults[CommonRequest :: PROPERTY_DECISION_DATE] = $request->get_decision_date();

        parent :: setDefaults($defaults);
    }

    public function get_form_type()
    {
        return $this->form_type;
    }
}

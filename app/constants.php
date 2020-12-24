<?php
namespace App;
class constants
{
    const SURVEY = [
        "KEY_ID" => "survey_id",
        "KEY_TITLE" => "survey_title",
        "KEY_CORRECT_PICTURE" => "survey_correct_picture",
        "KEY_INCORRECT_PICTURE" => "survey_incorrect_picture",
        "KEY_COMPLETE_MESSAGE" => "survey_complete_message",
        "KEY_STATUS" => "survey_status",
        "KEY_RESULT" => "survey_result",
    ];

    const OPTIONS = [
        "KEY_ID" => "option_id",
        "KEY_NAME" => "option_name",
        "KEY_CORRECT" => "option_correct",
        "KEY_POSITION" => "option_position",
    ];

    const QUIZ = [
        "KEY_ID" => "quiz_id",
        "KEY_TITLE" => "quiz_title",
        "KEY_SENTENCE" => "quiz_sentence",
        "KEY_PICTURE" => "quiz_picture",
        "KEY_EXPLAIN_CORRECT" => "quiz_explain_correct",
        "KEY_EXPLAIN_INCORRECT" => "quiz_explain_incorrect",
        "KEY_OPTIONS" => "quiz_options",
    ];

    const CLASSS = [
        "KEY_CLASSNAME" => "class_name",
        "KEY_CLASSID" => "class_id",
        "KEY_LIST_CLASS" => "classes"
    ];

    const DEPARTMENT = [
        "KEY_DEPARTMENTNAME" => "department_name",
        "KEY_DEPARTMENTID" => "department_id",
        "KEY_DEPARMENT_LIST" => "departments"
    ];

    const TAG = [
        "KEY_TAGNAME" => "tag_name",
        "KEY_TAGID" => "tag_id",
        "KEY_TAG_LIST" => "tags"
    ];

    const USER = [
        "KEY_USER_ID" => "user_id",
        "KEY_USER_NAME" => "user_name",
        "KEY_USER_EMAIL" => "user_email",
        "KEY_USER_ROLES" => "user_roles",
        "KEY_USER_PERMISSIONS" => "user_permission",
    ];

    const KEY_SEARCH_VALUE = "search_key";
    const KEY_LANGUAGE = "la";
}
?>
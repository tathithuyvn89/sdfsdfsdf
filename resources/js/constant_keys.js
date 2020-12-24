const USER = {
  ID: 'user_id',
  NAME: 'user_name',
  EMAIL: 'user_email',
  ROLES: 'user_roles',
  PERMISSIONS: 'user_permission',
  AVATAR: 'user_avatar',
};
const LANG = {
  KEY_LANGUAGE: 'la',
};
const CLASS = {
  KEY_NAME: 'class_name',
  KEY_ID: 'class_id',
  KEY_LIST: 'classes',
};
const DEPARTMENT = {
  KEY_TNAME: 'department_name',
  KEY_ID: 'department_id',
  KEY_LIST: 'departments',
};
const TAG = {
  KEY_NAME: 'tag_name',
  KEY_ID: 'tag_id',
  KEY_LIST: 'tags',
};
const SURVEY = {
  KEY_ID: 'survey_id',
  KEY_TITLE: 'survey_title',
  KEY_CORRECT_PICTURE: 'survey_correct_picture',
  KEY_INCORRECT_PICTURE: 'survey_incorrect_picture',
  KEY_COMPLETE_MESSAGE: 'survey_complete_message',
  KEY_STATUS: 'survey_status',
  KEY_RESULT: 'survey_result',
};
const QUIZ = {
  KEY_ID: 'quiz_id',
  KEY_TITLE: 'quiz_title',
  KEY_SENTENCE: 'quiz_sentence',
  KEY_PICTURE: 'quiz_picture',
  KEY_EXPLAIN_CORRECT: 'quiz_explain_correct',
  KEY_EXPLAIN_INCORRECT: 'quiz_explain_incorrect',
  KEY_OPTIONS: 'quiz_options',
};
const OPTION = {
  KEY_ID: 'option_id',
  KEY_NAME: 'option_name',
  KEY_CORRECT: 'option_correct',
  KEY_POSITION: 'option_position',
};
const SEARCH = {
  KEY_VALUE: 'search_key',
};
const RETURN_CODE = {
  KEY_STATUS: 'status',
  KEY_ERROR_NUMBER: 'ERROR',
};
const OTHERS = {
  KEY: '',
  KEY_ANSWER_SURVEYS: 'answered_survey_id',
};
module.exports = {
  USER,
  LANG,
  CLASS,
  DEPARTMENT,
  TAG,
  SURVEY,
  QUIZ,
  OPTION,
  SEARCH,
  RETURN_CODE,
  OTHERS,
};

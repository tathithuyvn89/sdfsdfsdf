<?php
/**
 * File Acl.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Laravue;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class Acl
 *
 * @package App\Laravue
 */
final class Acl
{
    const ROLE_ADMIN = 'admin'; // this role will have all permission
    const ROLE_USER = 'user'; //this rola will have permission about answer survey

    //permission for view of admin and manager
    const PERMISSION_VIEW_MENU_MANAGE_QUIZZ = 'view menu quizz';
    const PERMISSION_VIEW_MENU_MANAGE_SURVEY = 'view menu survey';
    const PERMISSION_VIEW_MENU_MANAGE_CLASS = 'view menu class';
    const PERMISSION_VIEW_MENU_MANAGE_DEPARTMENT = 'view menu department';
    const PERMISSION_VIEW_MENU_MANAGE_TAG = 'view menu tag';
    const PERMISSION_VIEW_MENU_MANAGE_ROLE = 'view menu role';
    const PERMISSION_VIEW_MENU_MANAGE_MEDIA = 'view menu media';
    const PERMISSION_VIEW_MENU_MANAGE_SETTING = 'view menu setting';
    //const PERMISSION_VIEW_MENU_RESULT_OF_SURVEY = 'result of survey';
    //every single permission for app(survey part)
    const PERMISSION_CREATE_SURVEY_LIST = 'create survey list';
    const PERMISSION_EDIT_SURVEY_LIST = 'edit survey list';
    const PERMISSION_SOFT_DELETE_SURVEY_LIST = 'soft delete survey list';
    const PERMISSION_FORCE_DELETE_SURVEY_LIST = 'force delete survey list';
    
    //every single permission for quizz part
    const PERMISSION_CREATE_QUIZZ = 'create quizz';
    const PERMISSION_EDIT_QUIZZ = 'edit quizz';
    const PERMISSION_SOFT_DELETE_QUIZZ = 'soft delete quizz';
    const PERMISSION_FORCE_DELETE_QUIZZ = 'force delete quizz';

    //Class permission
    const PERMISSION_CREATE_CLASS = 'create class';
    const PERMISSION_EDIT_CLASS= 'edit class';
    const PERMISSION_SOFT_DELETE_CLASS = 'soft delete class';
    const PERMISSION_FORCE_DELETE_CLASS = 'force delete class';

    //Department permission
    const PERMISSION_CREATE_DEPARTMENT = 'create department';
    const PERMISSION_EDIT_DEPARTMENT= 'edit department';
    const PERMISSION_SOFT_DELETE_DEPARTMENT = 'soft delete department';
    const PERMISSION_FORCE_DELETE_DEPARTMENT = 'force delete department';

    //Tag permission
    //Department permission
    const PERMISSION_CREATE_TAG = 'create tag';
    const PERMISSION_EDIT_TAG= 'edit tag';
    const PERMISSION_SOFT_DELETE_TAG = 'soft delete tag';
    const PERMISSION_FORCE_DELETE_TAG = 'force delete tag';
    
    //permission for view of user
    const PERMISSION_USER_VIEW_MENU_SURVEY = 'user view menu survey';
    const PERMISSION_USER_VIEW_MENU_ANSWER_SURVEY = 'user view menu answer survey';

    //permission for management user permission
    const PERMISSION_USER_MANAGE = 'manage user';
    const PERMISSION_ARTICLE_MANAGE = 'manage article';
    const PERMISSION_PERMISSION_MANAGE = 'manage permission';

    /**
     * @param array $exclusives Exclude some permissions from the list
     * @return array
     */
    public static function permissions(array $exclusives = []): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) use ($exclusives) {
                return !in_array($value, $exclusives) && Str::startsWith($key, 'PERMISSION_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    public static function menuPermissions(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'PERMISSION_VIEW_MENU_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    /**
     * @return array
     */
    public static function roles(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $roles =  Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'ROLE_');
            });

            return array_values($roles);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }
}

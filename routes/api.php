<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Laravue\Faker;
use \App\Laravue\JsonResponse;
use \App\Laravue\Acl;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->group(function() {

        Route::post('auth/login', 'AuthController@login')->middleware('session_timeout');
        Route::get('auth/aad','AuthController@loginAAD');
        Route::post('auth/azure/login','AuthController@loginWithAzure')->middleware('session_timeout');

        Route::group(['middleware' => 'auth:sanctum'], function () {

            Route::get('auth/user', 'AuthController@user');
            Route::post('auth/logout', 'AuthController@logout');
       
            Route::get('/user', function (Request $request) {
                   return new UserResource($request->user());
            });
       
               // Api resource routes
            Route::apiResource('roles', 'RoleController')->middleware('mypermission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::apiResource('users', 'UserController')->middleware('mypermission:' . Acl::PERMISSION_USER_MANAGE);
            Route::apiResource('permissions', 'PermissionController')->middleware('mypermission:' . Acl::PERMISSION_PERMISSION_MANAGE);//manage permission
       
               // Custom routes
            Route::put('users/{user}', 'UserController@update');
            Route::get('users/{user}/permissions', 'UserController@permissions')->middleware('mypermission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::put('users/{user}/permissions', 'UserController@updatePermissions')->middleware('mypermission:' .Acl::PERMISSION_PERMISSION_MANAGE);
            Route::get('roles/{role}/permissions', 'RoleController@permissions')->middleware('mypermission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('profile', 'UserController@update_avatar'); //updatae avatar in profile
            Route::get('profile', 'UserController@profile'); // take data user 
            Route::put('profile/changepass','UserController@update_password');// change password user
       
       
            Route::apiResource('class','ClassController')->middleware('mypermission:'.Acl::PERMISSION_VIEW_MENU_MANAGE_CLASS)->except('destroy');
            Route::delete('class/forcedelete','ClassController@forceDelete')->middleware('mypermission:' . Acl::PERMISSION_FORCE_DELETE_CLASS);
       
            Route::apiResource('tag','TagController')->middleware('mypermission:'.Acl::PERMISSION_VIEW_MENU_MANAGE_CLASS)->except('destroy');
            Route::delete('tag/forcedelete','TagController@forceDelete')->middleware('mypermission:' . Acl::PERMISSION_FORCE_DELETE_TAG);
       
            Route::apiResource('department','DepartmentController')->middleware('mypermission:'.Acl::PERMISSION_VIEW_MENU_MANAGE_DEPARTMENT)->except('destroy');
            Route::delete('department/forcedelete','DepartmentController@forceDelete')->middleware('mypermission:' . Acl::PERMISSION_FORCE_DELETE_DEPARTMENT);
       
               //Quiz Route
            Route::apiResource('quizz','QuizzController')->middleware([
                   'mypermission:'.Acl::PERMISSION_CREATE_QUIZZ,
                   'mypermission:'.Acl::PERMISSION_EDIT_QUIZZ,
                   'mypermission:'.Acl::PERMISSION_FORCE_DELETE_QUIZZ,
                   'mypermission:'.Acl::PERMISSION_VIEW_MENU_MANAGE_QUIZZ
            ])->except('destroy');
       
            Route::delete('quizz/forcedelete','QuizzController@forcedelete')->middleware([
                   'mypermission:'.Acl::PERMISSION_FORCE_DELETE_QUIZZ,
            ]);
       
            Route::apiResource('survey','SurveyController')->middleware([
                'mypermission:'. Acl::PERMISSION_CREATE_SURVEY_LIST,
                'mypermission:' . Acl::PERMISSION_EDIT_SURVEY_LIST,
                'mypermission:' . Acl::PERMISSION_FORCE_DELETE_SURVEY_LIST
            ])->except('destroy');
       
            Route::delete('survey/forcedelete','SurveyController@forcedelete')->middleware('mypermission:'.Acl::PERMISSION_FORCE_DELETE_SURVEY_LIST);
            Route::get('survey/get/information','SurveyController@Get_Quizz_List_Tags_Departments_Classes_Survey_Detail')
            ->middleware(['mypermission:'. Acl::PERMISSION_EDIT_SURVEY_LIST]);
       
            Route::middleware(['session_timeout'])->group(function(){
                Route::middleware([
                    'mypermission:'.Acl::PERMISSION_USER_VIEW_MENU_ANSWER_SURVEY
                ])->group(function () {
                   Route::get('respondent','RespondentController@GetListSurveys');
                   Route::get('respondent/{id}','RespondentController@GetSurveyDetail');
                   Route::get('informations','RespondentController@Get_Tags_Classes_Departments');
                   Route::post('respondent/result','RespondentController@InsertResultSurvey');
                });
            });
       
               Route::get('dashboard/result','SurveyResultController@GetSurveyResult')->middleware(['mypermission:'. Acl::PERMISSION_CREATE_SURVEY_LIST]);
               Route::get('dashboard/result/detail','SurveyResultController@GetSurveyResultDetail')->middleware(['mypermission:'. Acl::PERMISSION_CREATE_SURVEY_LIST]);
               Route::get('dashboard/respondent','SurveyResultController@GetRespondentName')->middleware(['mypermission:'. Acl::PERMISSION_CREATE_SURVEY_LIST]);
               Route::get('dashboard/survey','SurveyResultController@GetSurveyName')->middleware(['mypermission:'. Acl::PERMISSION_CREATE_SURVEY_LIST]);
           });
});


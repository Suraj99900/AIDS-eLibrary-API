<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Symfony\Component\HttpKernel\Fragment\SsiFragmentRenderer;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api', ['middleware' => 'ClientAuth']], function () use ($router) {
    //! User authentication ....
    $router->post('register', ['uses' => 'AidsUserController@addUser']);
    $router->put('register', ['uses' => 'AidsUserController@updateUser']);
    $router->get('login', ['uses' => 'AidsUserController@loginUser']);
    $router->get('user', ['uses' => 'AidsUserController@fetchUser']);
    $router->put('change-pass/{id}', ['uses' => 'AidsUserController@updatePassword']);
    $router->delete('user/{id}', ['uses' => 'AidsUserController@freezeOrUnFreezedById']);
    $router->get('semester', ['uses' => 'SemesterController@getAllSemester']);

    // upload Sestion API
    $router->post('upload', ['uses' => 'AIDSUploadController@addUploadData']);
    $router->get('fetch/book', ['uses' => 'AIDSUploadController@getUploadedBook']);
    $router->delete('fetch/book/{id}', ['uses' => 'AIDSUploadController@delete']);
    $router->post('download', ['uses' => 'AIDSUploadController@downloadFile']);

    // student Info API

    $router->post('student-info', 'AIDSSturntInfoController@addStudentInfo');
    $router->put('student-info', 'AIDSSturntInfoController@updateStudentByNameZPRN');
    $router->get('student-info', 'AIDSSturntInfoController@getStudentsInfo');
    $router->get('student-info/zprn', 'AIDSSturntInfoController@getStudentByNameZPRN');
    $router->delete('student-info/zprn', 'AIDSSturntInfoController@freezeStudent');


    // Book Management Routes
    $router->post('books', ['uses' => 'AidsBookManageController@add']);
    $router->put('books/{id}', ['uses' => 'AidsBookManageController@update']);
    $router->get('books', ['uses' => 'AidsBookManageController@fetch']);
    $router->get('books/{id}', ['uses' => 'AidsBookManageController@fetchById']);
    $router->delete('books/{id}', ['uses' => 'AidsBookManageController@delete']);

    // Book Issue Routes
    $router->get('book-issues', ['uses' => 'AIDSBookIssueController@getAllBookIssues']);
    $router->get('book-issues/{id}', ['uses' => 'AIDSBookIssueController@getBookIssueById']);
    $router->post('book-issues', ['uses' => 'AIDSBookIssueController@addBookIssue']);
    $router->put('book-issues/{id}', ['uses' => 'AIDSBookIssueController@updateBookIssueById']);
    $router->put('book-return/{id}', ['uses' => 'AIDSBookIssueController@returnBookById']);
    $router->delete('book-issues/{id}', ['uses' => 'AIDSBookIssueController@deleteBookIssueById']);


    // master folder 
    $router->get('folders', ['uses' => 'MasterFolderController@getAllFolders']);
    $router->get('folders/{id}', ['uses' => 'MasterFolderController@getFolderById']);
    $router->post('folders', ['uses' => 'MasterFolderController@createFolder']);
    $router->put('folders/{id}', ['uses' => 'MasterFolderController@updateFolder']);
    $router->put('folders/{id}/freeze', ['uses' => 'MasterFolderController@freezeFolder']);
    $router->delete('folders/{id}', ['uses' => 'MasterFolderController@deleteFolder']);

    // Subfolders Routes
    $router->post('subfolders', ['uses' => 'SubFolderController@createSubFolder']);
    $router->get('subfolders/{id}', ['uses' => 'SubFolderController@getSubFolderById']);
    $router->get('subfolders/master/{masterFolderId}', ['uses' => 'SubFolderController@getSubFoldersByMasterFolderId']);
    $router->get('subfolders', ['uses' => 'SubFolderController@getAllSubFolders']);
    $router->delete('subfolders/{id}', ['uses' => 'SubFolderController@deleteSubFolder']);
    $router->put('subfolders/{id}/freeze', ['uses' => 'SubFolderController@freezeSubFolder']);
    $router->put('subfolders/{id}/unfreeze', ['uses' => 'SubFolderController@unfreezeSubFolder']);

    $router->post('upload-data', ['uses' => 'UploadFileController@addUploadData']);
    $router->get('upload-data', ['uses' => 'UploadFileController@getAllData']);
    $router->post('download-file', ['uses' => 'UploadFileController@downloadFile']);

});

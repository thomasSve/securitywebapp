<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }

        $variables = [
            'users' => $this->userRepository->all(),
            'posts' => $this->postRepository->all()
        ];
        $this->render('admin.twig', $variables);
    }

    public function delete($username)
    {


            if($this->auth->isAdmin()) {
                if ($this->userRepository->deleteByUsername($username) === 1) {
                    $this->app->flash('info', "Sucessfully deleted  '$username'");
                    $this->app->redirect('/admin');
                    return;
                }
                $this->app->flash('info', "Did not delete, User does not exits");
                $this->app->redirect('/');
            }else{
                $this->app->flash('info', "An error ocurred. Unable to delete user");
                $this->app->redirect('/');
                return;
            }
           // else {
               // $this->app->flash('info', "You must be administrator to delete a user.");
               // $this->app->redirect('/');
           // }
        

    }

    public function deletePost($postId)
    {

            if($this->auth->isAdmin()) {
                if ($this->postRepository->deleteByPostid($postId) === 1) {
                    $this->app->flash('info', "Sucessfully deleted '$postId'");
                    $this->app->redirect('/admin');
                    return;
                }

                $this->app->flash('info', "Did not delete, post does not exist");
                $this->app->redirect('/');
          } else{
                $this->app->flash('info', "An error ocurred. Unable to delete POST. ");
                $this->app->redirect('/');
                return;
            }


    }
}

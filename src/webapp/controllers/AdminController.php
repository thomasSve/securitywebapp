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
		
		$csrf = rand(0,10000);
        $_SESSION['csrf'] = $csrf;
		
        $variables = [
			'csrf' => $csrf,
            'users' => $this->userRepository->all(),
            'posts' => $this->postRepository->all()
        ];
        $this->render('admin.twig', $variables);
    }

    public function delete()
    {
		$request = $this->app->request;
		$username    = $request->post('username');
        $csrf    = $request->post('csrf');

        if ($_SESSION['csrf'] != $csrf) {
            $this->app->flashNow('error', 'Bot?');
            $this->render('login.twig', ['csrf' => $csrf]);
            return;
        }

            if($this->auth->isAdmin()) {
                if ($this->userRepository->deleteByUsername($username)) {
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

    public function deletePost()
    {
			$request = $this->app->request;
			$postId    = $request->post('postId');
			$csrf    = $request->post('csrf');

			if ($_SESSION['csrf'] != $csrf) {
				$this->app->flashNow('error', 'Bot?');
				$this->render('login.twig', ['csrf' => $csrf]);
				return;
			}
			
			
            if($this->auth->isAdmin()) {
                if ($this->postRepository->deleteByPostid($postId)) {
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

    public function addDoctor()
    {
		$request = $this->app->request;
		$username    = $request->post('username');
        $csrf    = $request->post('csrf');

        if ($_SESSION['csrf'] != $csrf) {
            $this->app->flashNow('error', 'Bot?');
            $this->render('login.twig', ['csrf' => $csrf]);
            return;
        }
		
        if($this->auth->isAdmin()) {
            if ($this->userRepository->addDoctor($username)) {
                $this->app->flash('info', "Sucessfully added doctor '$username'");
                $this->app->redirect("/admin");
                return;
            }
            $this->app->flash('info', "Did not set user as doctor, User does not exits");
            $this->app->redirect("/");
        }else{
            $this->app->flash('info', "An error ocurred. Unable to set user as doctor");
            $this->app->redirect('/');
            return;
        }
    }

    public function removeDoctor() {
		
		$request = $this->app->request;
		$username    = $request->post('username');
        $csrf    = $request->post('csrf');

        if ($_SESSION['csrf'] != $csrf) {
            $this->app->flashNow('error', 'Bot?');
            $this->render('login.twig', ['csrf' => $csrf]);
            return;
        }
		
            if($this->auth->isAdmin()) {
            if ($this->userRepository->removeDoctor($username)) {
                $this->app->flash('info', "Sucessfully removed doctor '$username'");
                $this->app->redirect("/admin");
                return;
            }
            $this->app->flash('info', "Did not remove user as doctor, User does not exits");
            $this->app->redirect("/");

        }else{
            $this->app->flash('info', "An error ocurred. Unable to unset doctor");
            $this->app->redirect('/');
            return;
        }
    }
}

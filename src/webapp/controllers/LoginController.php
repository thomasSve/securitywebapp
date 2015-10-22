<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\repository\UserRepository;

class LoginController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->check()) {
            $username = $this->auth->user()->getUsername();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
            return;
        }

        $csrf = rand(0,10000);
        $_SESSION['csrf'] = $csrf;

        $this->render('login.twig', ['csrf' => $csrf]);
    }

    public function login()
    {
        $request = $this->app->request;
        $user    = $request->post('user');
        $pass    = $request->post('pass');
        $csrf    = $request->post('csrf');

        if ($_SESSION['csrf'] != $csrf) {
            $this->app->flash('info', "Bot?");
            $this->app->redirect('/');
            return;
        }

        if ($this->auth->checkCredentials($user, $pass)) {
            $_SESSION['user'] = $user;
            $isAdmin = $this->auth->user()->isAdmin();
            $isDoctor = $this->auth->user()->isDoctor();
            if ($isAdmin) {
                $_SESSION['isadmin'] = 'yes';
            } else {
                $_SESSION["isadmin"] = 'no';
            }
            if ($isDoctor) {
                $_SESSION['isdoctor'] = 'yes';
            } else {
                $_SESSION['isdoctor'] = 'no';
            }

            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
            return;
        }

        $this->app->flashNow('error', 'Incorrect user/pass combination.');
        $this->render('login.twig', []);
    }
}

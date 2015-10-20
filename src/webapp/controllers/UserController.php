<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\User;
use tdt4237\webapp\validation\EditUserFormValidation;
use tdt4237\webapp\validation\RegistrationFormValidation;
use tdt4237\webapp\validation\TransferValidation;

class UserController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            return $this->render('newUserForm.twig', []);
        }

        $username = $this->auth->user()->getUserName();
        $this->app->flash('info', 'You are already logged in as ' . $username);
        $this->app->redirect('/');
    }

    public function create()
    {
        $request  = $this->app->request;
        $username = $request->post('user');
        $password = $request->post('pass');
        $fullname = $request->post('fullname');
        $address = $request->post('address');
        $postcode = $request->post('postcode');
        

        $validation = new RegistrationFormValidation($username, $password, $fullname, $address, $postcode);

        if ($this->userRepository->findByUser($username)) {
            $validation->addValidationError("Username allready exists");
        } else  if ($validation->isGoodToGo()) {
            $password = $password;
			$salt = $this->generateSalt();
            $password = $this->hash->make($password, $salt);
            $user = new User($username, $password, $fullname, $address, $postcode, $salt);
            $this->userRepository->save($user);

            $this->app->flash('info', 'Thanks for creating a user. Now log in.');
            return $this->app->redirect('/login');
        }

        $errors = join("<br>\n", $validation->getValidationErrors());
        $this->app->flashNow('error', $errors);
        $this->render('newUserForm.twig', ['username' => $username]);
    }
	
	public static function generateSalt($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
    return $randomString;
    }

    public function all()
    {
        $this->render('users.twig', [
            'users' => $this->userRepository->all()
        ]);
    }

    public function logout()
    {
        $this->auth->logout();
        $this->app->redirect('/');
    }

    public function show($username)
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");

        } else {
            $user = $this->userRepository->findByUser($username);
            if ($user != false && $user->getUsername() == $this->auth->getUsername()) {

                $this->render('showuser.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            } else if ($this->auth->check()) {

                $this->render('showuserlite.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            }
        }
    }

    public function showUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();

        $this->render('edituser.twig', [
            'user' => $this->auth->user()
        ]);
    }

    public function receiveUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();
        $user = $this->auth->user();

        $request = $this->app->request;
        $email   = $request->post('email');
        $bio     = $request->post('bio');
        $age     = $request->post('age');
        $fullname = $request->post('fullname');
        $address = $request->post('address');
        $postcode = $request->post('postcode');

        $validation = new EditUserFormValidation($email, $bio, $age);

        if ($validation->isGoodToGo()) {
            $user->setEmail(new Email($email));
            $user->setBio($bio);
            $user->setAge(new Age($age));
            $user->setFullname($fullname);
            $user->setAddress($address);
            $user->setPostcode($postcode);
            $this->userRepository->save($user);

            $this->app->flashNow('info', 'Your profile was successfully saved.');
            return $this->render('edituser.twig', ['user' => $user]);
        }

        $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
        $this->render('edituser.twig', ['user' => $user]);
    }

    public function makeSureUserIsAuthenticated()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
        }
    }
    /*public function showTransferForm(){
        $this->makeSureUserIsAuthenticated();

    }
    public function submitTransfer(){
        $this->makeSureUserIsAuthenticated();
        $user = $this->auth->user();

        $request = $this->app->request;
        $value  = $request->post('value');

        $validation = new TransferValidation();
        $validation->validateTransfer($user);

        if (){
            $user->set
        }

    }*/
    public function showCardnumberForm(){
        $this->makeSureUserIsAuthenticated();

        $this->render('cardnumber.twig', [
            'user' => $this->auth->user()
        ]);
    }
    public function submitCardnumber(){
        $this->makeSureUserIsAuthenticated();
        $user = $this->auth->user();

        $request = $this->app->request;
        $cardNumber = $request->post('cardnumber');

        $validation = new TransferValidation();
        $validation->validateNewCardnumber($cardNumber);

        if ($validation->isGoodToGo()){
            $user->setCardnumber($cardNumber);

            $this->userRepository->saveCardNumber($user);

            $this->app->flashNow('info', 'Your cardnumber was successfully saved.');
            return $this->render('cardnumber.twig', ['user' => $this->auth->user()]);
        }
        $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
        return $this->render('cardnumber.twig', ['user' => $this->auth->user()]);
    }
}

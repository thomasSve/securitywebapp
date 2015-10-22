<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Post;
use tdt4237\webapp\controllers\UserController;
use tdt4237\webapp\models\Comment;
use tdt4237\webapp\validation\PostValidation;
use tdt4237\webapp\validation\TransferValidation;

class PostController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        if(!$this->auth->guest()) {
            $posts = $this->postRepository->all();

            $posts->sortByDate();
            $user = $this->auth->user();
            if($user->getIsDoctor()==1) {
                $temppost = array();
                for ($i = 0; $i < count($posts); $i++) {
                    $author = $this->userRepository->findByUser($posts[$i]->getAuthor());
                    if(!$author->getCardNumber()==null && $posts[$i]->getWantAnswerByDoctor() == 1){
                        array_push($temppost, $posts[$i]);
                    }
                }
                $posts = $temppost;
            }
            $this->render('posts.twig', [
                'user' => $user,
                'posts' => $posts
            ]);
        } else {
                $this->app->flash('info', 'You must log in to do that');
                $this->app->redirect('/login');
            }
    }

    public function show($postId)
    {
        if(!$this->auth->guest()) {
            $post = $this->postRepository->find($postId);
            $comments = $this->commentRepository->findByPostId($postId);
            $csrf = rand(0,10000);
            $_SESSION['csrf'] = $csrf;

            foreach ($comments as $comment) {
                $user = $this->userRepository->findByUser($comment->getAuthor());
                if ($user->isDoctor() == 0) {
                    $comment->setIsDoctor(false);
                }
                else {
                    $comment->setIsDoctor(true);
                }
            }

            $request = $this->app->request;
            $message = $request->get('msg');
            $variables = [];

            if ($message) {
                $variables['msg'] = $message;
            }

            $this->render('showpost.twig', [
                'post' => $post,
                'comments' => $comments,
                'csrf' => $csrf,
                'flash' => $variables
            ]);

        }
        else {
            $this->app->flash('info', 'you must log in to do that');
            $this->app->redirect('/login');
        }

    }

    public function addComment($postId)
    {
        /* csrf */
        if ($_SESSION['csrf'] != $_POST['csrf']) {
            $this->app->flash("info", "Bot?");
            $this->app->redirect("/posts/$postId");
        } else if(!$this->auth->guest()) {

            $comment = new Comment();
            $comment->setAuthor($_SESSION['user']);
            $comment->setText($this->app->request->post("text"));
            $comment->setDate(date("dmY"));
            $comment->setPost($postId);
            // Check if author is doctor, and then check if a doctor has already responded to
            $user = $this->auth->user();
            if ($user->isDoctor() == 1) {
                $post = $this->postRepository->find($postId);
                $postAuthor = $this->userRepository->findByUser($post->getAuthor());
                print("This is a doctor");
                if ($post->getAnsByDoc()==0 && ($user->getUsername() != $postAuthor->getUsername())) {
                    print("Post answering by doctor, user is not post author");
                    // Make transaction if post has asked for it, and post-author has creditcard
                    $validation = new TransferValidation();
                    $validation->validateTransfer($postAuthor);
                    if ($validation->isGoodToGo()) {
                        // Take 10$ from postauth's
                        print("Validation valid");
                        $postAuthor->changeBalance(-10);
                        // Send 7$ to doctor, commentpost
                        $user->changeBalance(7);
                        // Send 3$ to webpage, currently unknown bank-information
                        $post->setAnsByDoc(1);
                        $this->userRepository->saveTransaction($user->getUsername(), ($user->getBalance()));
                        $this->userRepository->saveTransaction($postAuthor->getUsername(), ($postAuthor->getBalance()));
                        $this->postRepository->addAnsDoctor($postId);
                        $this->commentRepository->save($comment);
                        $this->app->redirect('/posts/' . $postId);
                    }else{
                        print("Validation not valid");
                        $this->app->flash('msg', join('<br>', $validation->getValidationErrors()));
                        $this->app->redirect('/posts/' . $postId);
                    }
                }else {
                    $this->app->flash('msg', "Already answered by a doctor, or you answered on your own post, so no transaction was completed");
                    $this->commentRepository->save($comment);
                    $this->app->redirect('/posts/' . $postId);
                    print("Doctor has answered, or answered on own post");
                }

            } else {
                $this->commentRepository->save($comment);
                $this->app->redirect('/posts/' . $postId);
                print("Is not doctor");
            }
        }
        else {
            $this->app->flash('info', 'you must log in to do that');
            $this->app->redirect('/login');

        }

    }

    public function showNewPostForm()
    {

        if ($this->auth->check()) {
            $username = $_SESSION['user'];
            $csrf = rand(0,10000);
            $_SESSION['csrf'] = $csrf;
            $this->render('createpost.twig', ['username' => $username, 'csrf' => $csrf]);
        } else {
            $this->app->flash('error', "You need to be logged in to create a post");
            $this->app->redirect("/");
        }

    }

    public function create()
    {
        /* csrf */
        if ($_SESSION['csrf'] != $_POST['csrf']) {
            $this->app->flash("info", "Bot?");
            $this->app->redirect("/posts/new");
        } else if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged on to create a post");
            $this->app->redirect("/login");
        } else {
            $request = $this->app->request;
            $doctor = $request->post('doctor');
            $title = $request->post('title');
            $content = $request->post('content');
            $author = $_SESSION['user'];
            $date = date("dmY");
            $user = $this->auth->user();
            $validation = new PostValidation($author, $title, $content, $doctor, $user);
            if ($validation->isGoodToGo()) {
                $post = new Post();
                $post->setAuthor($author);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $post->setWantAnswerByDoctor($doctor);
                $savedPost = $this->postRepository->save($post);
                $this->app->redirect('/posts/' . $savedPost . '?msg="Post succesfully posted');
            }
            $this->app->flash('error', join('<br>', $validation->getValidationErrors()));
            $this->app->redirect('/posts/new');
        }
    }
}


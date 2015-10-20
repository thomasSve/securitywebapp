<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Post;
use tdt4237\webapp\controllers\UserController;
use tdt4237\webapp\models\Comment;
use tdt4237\webapp\validation\PostValidation;

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
            $this->render('posts.twig', ['posts' => $posts]);
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
            $request = $this->app->request;
            $message = $request->get('msg');
            $variables = [];


            if ($message) {
                $variables['msg'] = $message;

            }


            $this->render('showpost.twig', [
                'post' => $post,
                'comments' => $comments,
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

        if(!$this->auth->guest()) {

            $comment = new Comment();
            $comment->setAuthor($_SESSION['user']);
            $comment->setText($this->app->request->post("text"));
            $comment->setDate(date("dmY"));
            $comment->setPost($postId);
            $this->commentRepository->save($comment);
            $this->app->redirect('/posts/' . $postId);
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
            $this->render('createpost.twig', ['username' => $username]);
        } else {

            $this->app->flash('error', "You need to be logged in to create a post");
            $this->app->redirect("/");
        }

    }

    public function create()
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged on to create a post");
            $this->app->redirect("/login");
        } else {
            $request = $this->app->request;
            $doctor = $request->post('doctor');
            $title = $request->post('title');
            $content = $request->post('content');
            $author = $_SESSION['user'];
            $date = date("dmY");
            $user = $this->auth->useR();

            $validation = new PostValidation($author, $title, $content, $doctor, $user);
            if ($validation->isGoodToGo()) {
                $post = new Post();
                $post->setAuthor($author);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $savedPost = $this->postRepository->save($post);
                $this->app->redirect('/posts/' . $savedPost . '?msg="Post succesfully posted');
            }
            $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
            $this->app->render('createpost.twig', ['user' => $this->auth->user()]);
        }

    }
}


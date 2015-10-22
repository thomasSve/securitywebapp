<?php

namespace tdt4237\webapp;

use tdt4237\webapp\models\User;
use tdt4237\webapp\controllers\UserController;

class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up()
    {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, user VARCHAR(50), pass VARCHAR(50), email varchar(50) default null, fullname varchar(50), address varchar(50), postcode varchar (4), age varchar(50), bio varhar(50), isadmin INTEGER, isdoctor INTEGER, balance INTEGER, cardnumber VARCHAR(50),salt VARCHAR(50));";
        $q6 = "CREATE TABLE posts (postId INTEGER PRIMARY KEY AUTOINCREMENT, author TEXT, title TEXT NOT NULL, content TEXT NOT NULL, date TEXT NOT NULL, doctor INTEGER, answByDoctor INTEGER DEFAULT 0,FOREIGN KEY(author) REFERENCES users(user));";
        $q7 = "CREATE TABLE comments(commentId INTEGER PRIMARY KEY AUTOINCREMENT, date TEXT NOT NULL, author TEXT NOT NULL, text INTEGER NOT NULL, belongs_to_post INTEGER NOT NULL, FOREIGN KEY(belongs_to_post) REFERENCES posts(postId));";

        self::$pdo->exec($q1);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);

        print "[tdt4237] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
        self::insertPosts();
        self::insertComments();
    }

    static function insertDummyUsers()
    {
		$salt1 = UserController::generateSalt();
		$salt2 = UserController::generateSalt();
		$salt3 = UserController::generateSalt();
        $salt4 = UserController::generateSalt();
        $hash1 = Hash::make(bin2hex(openssl_random_pseudo_bytes(2)), $salt1);
        $hash2 = Hash::make('bobdylan', $salt2);
        $hash3 = Hash::make('liverpool', $salt3);
        $hash4 = Hash::make('Testuser123', $salt4);

        $q1 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode, salt, isdoctor) VALUES ('admin', '$hash1', 1, 'admin', 'homebase', '9090', '$salt1', 0)";
        $q2 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode, salt, isdoctor) VALUES ('bob', '$hash2', 0, 'Robert Green', 'Greenland Grove 9', '2010', '$salt2', 0)";
        $q3 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode, salt, cardnumber, isdoctor) VALUES ('bjarni', '$hash3', 0, 'Bjarni Torgmund', 'Hummerdale 12', '4120', '$salt3', 12345678912345, 0)";
        $q4 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode, salt, isdoctor) VALUES ('testuser', '$hash4', 1, 'admin', 'homebase', '9090', '$salt4', 0)";

        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        self::$pdo->exec($q3);
        self::$pdo->exec($q4);

        print "[tdt4237] Done inserting dummy users.".PHP_EOL;
    }

    static function insertPosts() {
        $q4 = "INSERT INTO posts(author, date, title, content, doctor) VALUES ('bob', '26082015', 'I have a problem', 'I have a generic problem I think its embarrasing to talk about. Someone help?', 0)";
        $q5 = "INSERT INTO posts(author, date, title, content, doctor) VALUES ('bjarni', '26082015', 'I also have a problem', 'I generally fear very much for my health', 0)";
        $q6 = "INSERT INTO posts(author, date, title, content, doctor) VALUES ('bjarni', '26082015', 'I have a problem i want answered by a doctor!', 'I generally fear very much for my health', 1)";

        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        self::$pdo->exec($q6);
        print "[tdt4237] Done inserting posts.".PHP_EOL;

    }

    static function insertComments() {
        $q1 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bjarni', '26082015', 'Don''t be shy! No reason to be afraid here',0)";
        $q2 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bob', '26082015', 'I wouldn''t worry too much, really. Just relax!',1)";
        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        print "[tdt4237] Done inserting comments.".PHP_EOL;


    }

    static function down()
    {
        $q1 = "DROP TABLE users";
        $q4 = "DROP TABLE posts";
        $q5 = "DROP TABLE comments";



        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);

        print "[tdt4237] Done deleting all SQL tables.".PHP_EOL;
    }
}
try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo $e->getMessage();
    exit();
}

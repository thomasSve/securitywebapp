<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Post;
use tdt4237\webapp\models\PostCollection;

class PostRepository
{

    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    public static function create($id, $author, $title, $content, $date, $doctor)
    {
        $post = new Post;
        
        return $post
            ->setPostId($id)
            ->setAuthor($author)
            ->setTitle($title)
            ->setContent($content)
            ->setDate($date)
            ->setWantAnswerByDoctor($doctor);
    }

    public function find($postId)
    {
        $sql  = "SELECT * FROM posts WHERE postId = :postid";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':postid', $postId, PDO::PARAM_INT);

        $stmt->execute();
        $row = $stmt->fetch();

        if($row === false) {
            return false;
        }

        return $this->makeFromRow($row);
    }

    public function all()
    {
        $sql   = "SELECT * FROM posts";
        $results = $this->db->query($sql);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in posts all()');
        }

        $fetch = $results->fetchAll();
        if(count($fetch) == 0) {
            return false;
        }

        return new PostCollection(
            array_map([$this, 'makeFromRow'], $fetch)
        );
    }

    public function makeFromRow($row)
    {
        return static::create(
            $row['postId'],
            $row['author'],
            $row['title'],
            $row['content'],
            $row['date'],
            $row['doctor']
        );

       //  $this->db = $db;
    }

    public function deleteByPostid($postId)
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE postid=:postid");
        $stmt->bindParam(':postid', $postId, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function save(Post $post)
    {
        $title   = $post->getTitle();
        $author = $post->getAuthor();
        $content = $post->getContent();
        $date    = $post->getDate();
        $doctor = $post->getWantAnswerByDoctor();

        if ($post->getPostId() === null) {
            $query = "INSERT INTO posts (title, author, content, date, doctor) "
                . "VALUES (:title, :author, :content, :date, :doctor)";
        }

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':author', $author, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':doctor', $doctor, PDO::PARAM_INT);

        $stmt->execute();

        return $this->db->lastInsertId();
    }
}

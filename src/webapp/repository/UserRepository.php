<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;

class UserRepository
{
    const SELECT_ALL = "SELECT * FROM users";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makeUserFromRow(array $row)
    {
        $user = new User($row['user'], $row['pass'], $row['fullname'], $row['address'], $row['postcode'], $row['salt']);
        $user->setUserId($row['id']);
        $user->setFullname($row['fullname']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode'])));
        $user->setBio($row['bio']);
        $user->setIsAdmin($row['isadmin']);

        if (!empty($row['email'])) {
            $user->setEmail(new Email($row['email']));
        }

        if (!empty($row['age'])) {
            $user->setAge(new Age($row['age']));
        }

        if (!empty($row['balance'])) {
            $user->setBalance(($row['balance']));
        }

        if (!empty($row['cardnumber'])) {
            $user->setCardNumber(($row['cardnumber']));
        }

        return $user;
    }

    public function getNameByUsername($username)
    {
        $query = "SELECT * FROM users WHERE user=:username";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['fullname'];
    }

    public function findByUser($username)
    {
        $query  = "SELECT * FROM users WHERE user=:username";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false) {
            return false;
        }

        return $this->makeUserFromRow($row);
    }

    public function deleteByUsername($username)
    {
        $query = "DELETE FROM users WHERE user=:username";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        return $stmt->execute();
    }



    public function all()
    {
        $rows = $this->pdo->query(self::SELECT_ALL);
        
        if ($rows === false) {
            return [];
            throw new \Exception('PDO error in all()');
        }

        return array_map([$this, 'makeUserFromRow'], $rows->fetchAll());
    }

    public function save(User $user)
    {
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        $this->saveExistingUser($user);
    }

    public function saveNewUser(User $user)
    {
        $query = "INSERT INTO users(user, pass, email, age, bio, isadmin, fullname, address, postcode, salt) VALUES(:username, :pass, :email, :age , :bio , :isadmin, :fullname, :address, :postcode, :salt)";

        $stmt = $this->pdo->prepare($query);

        $username = $user->getUsername();
        $pass = $user->getHash();
        $email = $user->getEmail();
        $age = $user->getAge();
        $bio = $user->getBio();
        $isadmin = $user->isAdmin();
        $fullname = $user->getFullname();
        $address = $user->getAddress();
        $postcode = $user->getPostcode();
        $salt = $user->getSalt();

        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':age', $age, PDO::PARAM_STR);
        $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
        $stmt->bindParam(':isadmin', $isadmin, PDO::PARAM_INT);
        $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':postcode', $postcode, PDO::PARAM_STR);
        $stmt->bindParam(':salt', $salt, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function saveExistingUser(User $user)
    {
        $query = "UPDATE users SET email=:email, age=:age, bio=:bio, isadmin=:isadmin, fullname=:fullname, address=:address, postcode=:postcode WHERE id=:id";

        $stmt = $this->pdo->prepare($query);

        $email = $user->getEmail();
        $age = $user->getAge();
        $bio = $user->getBio();
        $isadmin = $user->isAdmin();
        $fullname = $user->getFullname();
        $address = $user->getAddress();
        $postcode = $user->getPostcode();
        $id = $user->getUserId();

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':age', $age, PDO::PARAM_STR);
        $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
        $stmt->bindParam(':isadmin', $isadmin, PDO::PARAM_INT);
        $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':postcode', $postcode, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function saveCardNumber(User $user){
        $query = "UPDATE users SET cardnumber=:cardNumber WHERE id=:id";
        $stmt = $this->pdo->prepare($query);

        $cardnumber = $user->getCardNumber();
        $id = $user->getUserId();

        $stmt->bindParam(':cardNumber', $cardnumber, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

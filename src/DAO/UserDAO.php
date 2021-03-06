<?php
namespace MicroCMS\DAO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use MicroCMS\Domain\User;

class UserDAO extends DAO implements UserProviderInterface {
    /**
     * Returns a user matching the supplied id.
     *
     * @param integer $id The user id.
     *
     * @return \MicroCMS\Domain\User|throws an exception if no matching user is found
     */
    public function find($id) {
        $sql = "SELECT * FROM t_users WHERE usr_id = ?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("Aucun utilisateur ne correspond à l'ID : " . $id);
    }

    //return whether the given username is unique
    //can be given an ID to except it during the search (needed on email modifications)
    public function findByUsername($username, $id = null) {
        if (is_null($id)) {
            $sql = "SELECT * FROM t_users WHERE usr_name = ?";
            $row = $this->getDb()->fetchAssoc($sql, array($username));
        }
        else {
            $sql = "SELECT * FROM t_users WHERE usr_name = ? AND usr_id != ?";
            $row = $this->getDb()->fetchAssoc($sql, array($username, $id));
        }

        if ($row)
            return true;
        else
            return false;
    }

    //return whether the given email adress is unique
    //can be given an ID to except it during the search (needed on username modifications)
    public function findByEmail($email, $id = null) {
        if (is_null($id)) {
            $sql = "SELECT * FROM t_users WHERE usr_email = ?";
            $row = $this->getDb()->fetchAssoc($sql, array($email));
        }
        else {
            $sql = "SELECT * FROM t_users WHERE usr_email = ? AND usr_id != ?";
            $row = $this->getDb()->fetchAssoc($sql, array($email, $id));
        }

        if ($row)
            return true;
        else
            return false;
    }

    /**
     * Returns a list of all users, sorted by role and name.
     *
     * @return array A list of all users.
     */
    public function findAll() {
        $sql = "SELECT * FROM t_users ORDER BY usr_role, usr_name";
        $result = $this->getDb()->fetchAll($sql);

        //Convert query results to an array of Domain objects
        $users = array();
        foreach ($result as $row) {
            $userId = $row['usr_id'];
            $users[$userId] = $this->buildDomainObject($row);
        }

        return $users;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username) {
        $sql = "SELECT * FROM t_users WHERE usr_name = :username OR usr_email = :username";
        $row = $this->getDb()->fetchAssoc($sql, array('username' => $username));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user) {
        $class = get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class) {
        return 'MicroCMS\Domain\User' === $class;
    }

    /**
     * Creates a User object based on a DB row.
     *
     * @param array $row The DB row containing User data.
     * @return \MicroCMS\Domain\User
     */
    protected function buildDomainObject(array $row) {
        $user = new User();

        $user->setId((int) $row['usr_id']);
        $user->setUsername($row['usr_name']);
        $user->setEmail($row['usr_email']);
        $user->setPassword($row['usr_password']);
        $user->setSalt($row['usr_salt']);
        $user->setRole($row['usr_role']);
        $user->setIsActive((bool) $row['usr_is_active']);

        return $user;
    }

    /**
     * Saves a user into the database.
     *
     * @param \MicroCMS\Domain\User $user The user to save
     */
    public function save(User $user) {
        $userData = array(
            'usr_name' => $user->getUsername(),
            'usr_email' => $user->getEmail(),
            'usr_password' => $user->getPassword(),
            'usr_salt' => $user->getSalt(),
            'usr_role' => $user->getRole(),
            'usr_is_active' => $user->getIsActive()
        );

        if ($user->getId()) {
            // The user has already been saved : update it
            $this->getDb()->update('t_users', $userData, array('usr_id' => $user->getId()));
        } else {
            // The user has never been saved : insert it
            $this->getDb()->insert('t_users', $userData);

            // Get the id of the newly created user and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $user->setId($id);
        }
    }

    /**
     * Ban a user.
     *
     * @param @param integer $id The user id.
     */
    public function ban($id) {
        // Ban the user
        $this->getDb()->update('t_users', array('usr_is_active' => false), array('usr_id' => $id));
    }

    /**
     * Unban a user.
     *
     * @param @param integer $id The user id.
     */
    public function unban($id) {
        // Unban the user
        $this->getDb()->update('t_users', array('usr_is_active' => true), array('usr_id' => $id));
    }

    /**
     * Removes a user from the database.
     *
     * @param @param integer $id The user id.
     */
    public function delete($id) {
        // Delete the user
        $this->getDb()->delete('t_users', array('usr_id' => $id));
    }
}

<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Crypt\Password\BCrypt;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UserTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getActiveUsers($paginated=false){
        if($paginated) {
            $select = new Select('user');
            $select -> where(array(new \Zend\Db\Sql\Predicate\IsNotNull('role_id')));
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            $paginatorAdapter = new DbSelect(
                $select,
                $this->tableGateway->getAdapter(),
                $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->select(array(new \Zend\Db\Sql\Predicate\IsNotNull('role_id')));
        return $resultSet;
    }

    public function getInactiveUsers($paginated=false){
         if($paginated) {
            $select = new Select('user');
            $select -> where(array('role_id' => NULL));
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            $paginatorAdapter = new DbSelect(
                $select,
                $this->tableGateway->getAdapter(),
                $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->select(array('role_id' => NULL));
        return $resultSet;
    }

    public function getUser($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUserbyUsername($username){
        $rowset = $this->tableGateway->select(array('username' => $username));
        $row = $rowset->current();
        return $row;
    }

    public function saveUser(User $user)
    {
        $bcrypt = new Bcrypt();
        $securePass = $bcrypt->create($user->password);
        $data = array(
            'username' => $user->username,
            'password' => $securePass,
            'email' => $user->email,
            'property_id' => $user->property_id,
            'role_id' => $user->role_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        );

        $id = (int)$user->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function editUser(User $user){
        $id = $user -> id;

        $data = array(
            'username' => $user->username,
            'email' => $user->email,
            'property_id' => $user->property_id,
            'role_id' => $user->role_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        );

        $this->tableGateway->update($data, array('id' => $id));
    }

    public function updatePassword(User $user){
        $id = $user -> id;

        $bcrypt = new Bcrypt();
        $securePass = $bcrypt->create($user->password);

        $data = array(
            'password' => $securePass,
        );

        $this -> tableGateway -> update($data, array('id' => $id));
    }

    public function enableUser(User $user){
        $id = $user -> id;
        $data = array(
            'property_id' => $user->property_id,
            'role_id' => $user->role_id,
        );

        $this->tableGateway->update($data, array('id' => $id));    
    }

    public function checkUser(User $user){
        $username = $user->username;
        $pass = $user->password;
        $storedUser = $this->getUserbyUsername($username);

        if ($storedUser){
            $suPass = $storedUser -> password;    
        } else {
            $e = 'Something really bad happened';
            return $e;
        }
        if($suPass){
            $bcrypt = new BCrypt();
            $check = $bcrypt -> verify($pass, $suPass);
            if($check == 1) {
                return 1;
            } else {
                $error = 'Incorrect Password';
                return $error;
            }
        } else {
            $error  = 'Something really bad happend';
            return $error;
        }
    }

    public function disableUser($id)
    {
        $data = array(
            'role_id' => NULL,
        );

        if ($this->getUser($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function disableUsersbyProperty($pid){
        $data = array(
            'role_id' => NULL,
        );

        $this->tableGateway->update($data, array('property_id' => $pid));
    }

}
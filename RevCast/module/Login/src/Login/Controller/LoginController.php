<?php
namespace Login\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Login\Form\LoginForm;
use Admin\Model\User;

use Zend\Crypt\Password\BCrypt;
class LoginController extends AbstractActionController
{
    protected $userTable;

    public function indexAction()
    {
        $form = new LoginForm;
        $form -> get('submit') -> setValue('Login');

        $request = $this->getRequest();

        /*$bcrypt = new BCrypt();
        $pass = $bcrypt -> create('AMANDA14');
        echo $pass; die; */ 

        if($request->isPost()){
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if($form->isValid()){
                $user->exchangeArray($form->getData());
                
                $check = $this->getUserTable()->checkUser($user);
                
                if($check == 1){
                    $storedUser = $this->getUserTable()->getUserbyUsername($user->username);
                    $session = new Container('userData');
                    if ($storedUser){
                        $session -> username = $storedUser -> username;
                        $session -> userId = $storedUser -> id;
                        $session -> role = $storedUser -> role_id;
                        
                        return $this->redirect()->toRoute('welcome');
                    } else {
                        $e = 'ERROR';
                        return $e;
                    }
                } else{
                    $e = 'ERROR';
                    return $e;
                }
            }
        }

        return array('form' => $form);
    }

    public function welcomeAction()
    {
        $session = new Container('userData');
        $username = $session -> username;
        $role = $session -> role;
 
        switch($role){
            case 1:
                return $this->redirect()->toRoute('admin');
                break;
            case 2:
                return $this->redirect()->toRoute('reports');
                break;
            default:
                return $this->redirect()->toRoute('reports');
                break;
        }   
    }

    public function logoutAction(){
        $session = new Container('userData');
        $session->getManager()->getStorage()->clear('userData');
    }

    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Admin\Model\UserTable');
        }
        return $this->userTable;
    }
}
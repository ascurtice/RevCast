<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Brand;
use Admin\Form\BrandForm;

class BrandController extends AbstractActionController
{
    protected $brandTable;
    protected $propertyTable;
    protected $userTable;

    public function indexAction()
    {
        return new ViewModel(array(
            'activeBrands' => $this->getBrandTable()->getActiveBrands(),
            'inactiveBrands' => $this->getBrandTable()->getInactiveBrands(),
        ));
    }

    public function addAction()
    {
        $form = new BrandForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $brand = new Brand();
            $form->setInputFilter($brand->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $brand->exchangeArray($form->getData());
                $this->getBrandTable()->saveBrand($brand);

                return $this->redirect()->toRoute('brand');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('brand', array(
                'action' => 'add'
            ));
        }
        $brand = $this->getBrandTable()->getBrand($id);

        $form  = new BrandForm();
        $form->bind($brand);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($brand->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getBrandTable()->saveBrand($form->getData());

                return $this->redirect()->toRoute('brand');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function disableAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('brand');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                
                //disable brand
                $this->getBrandTable()->disableBrand($id);

                //get all properties associated to brand
                $properties = $this->getPropertyTable()->getPropertiesByBrand($id);
                foreach ($properties as $property){
                    $this -> getUserTable() -> disableUsersbyProperty($property -> id);
                    $this -> getPropertyTable() -> disableProperty($property -> id);
                }

            }
            return $this->redirect()->toRoute('brand');
        }

        return array(
            'id'    => $id,
            'brand' => $this->getBrandTable()->getBrand($id)
        );
    }

    public function enableAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('brand');
        }
        $this->getBrandTable()->enableBrand($id);
        return $this->redirect()->toRoute('brand');
    }

    public function getBrandTable()
    {
        if (!$this->brandTable) {
            $sm = $this->getServiceLocator();
            $this->brandTable = $sm->get('Admin\Model\BrandTable');
        }
        return $this->brandTable;
    }

    public function getPropertyTable()
    {
        if(!$this->propertyTable){
            $sm = $this->getServiceLocator();
            $this->propertyTable = $sm->get('Admin\Model\PropertyTable');
        }
        return $this->propertyTable;
    }

    public function getUserTable(){
        if(!$this->userTable){
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Admin\Model\UserTable');
        }
        return $this->userTable;
    }
}
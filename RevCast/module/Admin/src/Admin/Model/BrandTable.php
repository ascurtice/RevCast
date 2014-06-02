<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class BrandTable
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

    public function getActiveBrands(){
        $resultSet = $this->tableGateway->select(array('active' => 1));
        return $resultSet;
    }

    public function getInactiveBrands(){
        $resultSet = $this->tableGateway->select(array('active' => 0));
        return $resultSet;
    }

    public function getBrand($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveBrand(Brand $brand)
    {
        $active = 1;
        $data = array(
            'brand' => $brand->brand,
            'active' => $active,
        );

        $id = (int)$brand->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getBrand($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function disableBrand($id)
    {
        $active = 0;
        $data = array(
            'active' => $active,
        );

        if ($this->getBrand($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function enableBrand($id)
    {
        $active = 1;
        $data = array(
            'active' => $active,
        );

        if ($this->getBrand($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }
}
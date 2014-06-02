<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class RoleTable
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

    public function getRole($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveRole(Role $role)
    {
        $active = 1;
        $data = array(
            'role' => $role->role,
        );

        $id = (int)$role->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRole($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
}
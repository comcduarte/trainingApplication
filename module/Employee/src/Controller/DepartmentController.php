<?php
namespace Employee\Controller;

use Midnet\Controller\AbstractBaseController;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Like;
use Zend\View\Model\ViewModel;

class DepartmentController extends AbstractBaseController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->from('departments');
        $select->columns([
            'UUID' => 'UUID',
            'Code' => 'CODE',
            'Department Name' => 'NAME',
        ]);
        $select->where(['departments.STATUS' => $this->model::ACTIVE_STATUS]);
        $select->order('NAME ASC');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $data = $resultSet->toArray();
        
        $header = [];
        if (!empty($data)) {
            $header = array_keys($data[0]);
        }
        
        $view->setVariable('header', $header);
        $view->setVariable('data', $data);
        
        return $view;
    }
    
    public function updateAction()
    {
        $view = new ViewModel();
        $view = parent::updateAction();
        
        /****************************************
         * EMPLOYEES SUBTABLE
         ****************************************/
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->columns(['UUID', 'LNAME', 'FNAME'])
            ->from('employees')
            ->where([new Like('DEPT', $this->model->UUID)]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $employees = $resultSet->toArray();
        
        $view->setVariable('employees', $employees);
        return $view;
    }
}
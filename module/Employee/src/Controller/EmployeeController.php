<?php
namespace Employee\Controller;

use Midnet\Controller\AbstractBaseController;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Like;
use Zend\View\Model\ViewModel;

class EmployeeController extends AbstractBaseController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->from('employees');
        $select->columns([
            'UUID' => 'UUID',
            'First Name' => 'FNAME',
            'Last Name' => 'LNAME',
            'Email' => 'EMAIL',
        ]);
        $select->where(['employees.STATUS' => $this->model::ACTIVE_STATUS]);
        $select->join('departments', 'departments.UUID = employees.DEPT', ['Department' => 'NAME'], Select::JOIN_LEFT);
        $select->order('LNAME ASC');
        
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
        $view->setVariable('uuid', $this->model->UUID);
        
        /****************************************
         * CLASSES SUBTABLE
         ****************************************/
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->columns(['UUID'])
        ->from('employee_classes')
        ->join('classes', 'employee_classes.CLASS_UUID = classes.UUID', ['UUID_C' => 'UUID', 'Code' => 'CODE', 'Name' => 'NAME', 'Date' => 'DATE_SCHEDULE'], Join::JOIN_INNER)
        ->where([new Like('EMP_UUID', $this->model->UUID)])
        ->order('DATE_SCHEDULE DESC');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $classes = $resultSet->toArray();
        
        $view->setVariable('classes', $classes);
        
        /****************************************
         * FILES SUBTABLE
         ****************************************/
        $path = './data/files/' . $this->model->UUID;
        $files = [];
        if (file_exists($path)) {
            $scannedfiles = array_diff(scandir($path), array('.', '..'));
            
            foreach ($scannedfiles as $index => $filename) {
                $files[] = [
                    'UUID' => $this->model->UUID,
                    'FILENAME' => $filename,
                ];
            }
        }
        
        $view->setVariable('files', $files);
        
        /****************************************
         * REPORTS SUBTABLE
         ****************************************/
        $reports = [];
        
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->columns(['UUID', 'NAME'])
        ->from('reports')
        ->where([new Like('NAME', 'EMP - %')]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $reports = $resultSet->toArray();
        
        $view->setVariable('reports', $reports);
        
        return $view;
    }
}
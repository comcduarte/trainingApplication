<?php
namespace Employee\Controller;

use Employee\Form\UploadFileForm;
use Employee\Model\DepartmentModel;
use Employee\Model\EmployeeModel;
use Midnet\Controller\AbstractConfigController;
use Midnet\Model\Uuid;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Ddl\DropTable;
use Zend\Db\Sql\Ddl\Column\Datetime;
use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\PrimaryKey;
use Zend\View\Model\ViewModel;

class EmployeeConfigController extends AbstractConfigController
{
    public function __construct()
    {
        $this->setRoute('employee/config');
    }
    
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        
        $importForm = new UploadFileForm('EMPLOYEES');
        $importForm->initialize();
        $importForm->addInputFilter();
        
        $view->setVariable('importForm', $importForm);
        
        $view->setTemplate('employee/config');
        
        return $view;
    }
    
    public function clearDatabase()
    {
        $sql = new Sql($this->adapter);
        $ddl = [];
        
        $ddl[] = new DropTable('employees');
        $ddl[] = new DropTable('departments');
        
        foreach ($ddl as $obj) {
            $this->adapter->query($sql->buildSqlString($obj), $this->adapter::QUERY_MODE_EXECUTE);
        }
    }

    public function createDatabase()
    {
        $sql = new Sql($this->adapter);
        
        /******************************
         * EMPLOYEES
         ******************************/
        $ddl = new CreateTable('employees');
        
        $ddl->addColumn(new Varchar('UUID', 36));
        $ddl->addColumn(new Integer('STATUS', TRUE));
        $ddl->addColumn(new Datetime('DATE_CREATED', TRUE));
        $ddl->addColumn(new Datetime('DATE_MODIFIED', TRUE));
        
        $ddl->addColumn(new Varchar('EMP_NUM', 6));
        $ddl->addColumn(new Varchar('FNAME', 255));
        $ddl->addColumn(new Varchar('LNAME', 255));
        $ddl->addColumn(new Varchar('EMAIL', 255, TRUE));
        $ddl->addColumn(new Varchar('DEPT', 36, TRUE));
        $ddl->addColumn(new Varchar('TIME_GROUP', 3, TRUE));
        $ddl->addColumn(new Varchar('TIME_SUBGROUP', 3, TRUE));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
        
        /******************************
         * DEPARTMENTS
         ******************************/
        $ddl = new CreateTable('departments');
        
        $ddl->addColumn(new Varchar('UUID', 36));
        $ddl->addColumn(new Integer('STATUS', TRUE));
        $ddl->addColumn(new Datetime('DATE_CREATED', TRUE));
        $ddl->addColumn(new Datetime('DATE_MODIFIED', TRUE));
        
        $ddl->addColumn(new Varchar('CODE', 5));
        $ddl->addColumn(new Varchar('NAME', 255));
        $ddl->addColumn(new Varchar('PARENT', 36, TRUE));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
        
    }

    public function reconciledirectoriesAction()
    {
        $sql = new Sql($this->adapter);
        $select = new Select();
        
        $tables = [
            'employees',
            'classes',
        ];
        
        foreach ($tables as $table) {
            $select->from($table);
            $select->columns([
                'UUID' => 'UUID',
            ]);
            
            $statement = $sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            $resultSet = new ResultSet($results);
            $resultSet->initialize($results);
            $data = $resultSet->toArray();
            
            foreach ($data as $record) {
                if (!file_exists('./data/files/' . $record['UUID'])) {
                    mkdir('./data/files/' . $record['UUID'], 0777, true);
                    $this->flashmessenger()->addSuccessMessage($record['UUID'] . ' folder created.');
                }
            }
            
            unset($data);
        }
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }
    
    
    public function importemployeesAction()
    {
        $EMP_NUM = 0;
        $FNAME = 1;
        $LNAME = 2;
        $DEPT_CODE = 3;
        $DEPT_NAME = 4;
        $PTG = 5;
        $PTSG = 6;
        
        $request = $this->getRequest();
        
        $form = new UploadFileForm();
        $form->initialize();
        $form->addInputFilter();
        
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
                );
            
            $form->setData($data);
            
            if ($form->isValid()) {
                $uuid = new Uuid();
                $date = new \DateTime('now',new \DateTimeZone('EDT'));
                $today = $date->format('Y-m-d H:i:s');
                
                $data = $form->getData();
                // $records = file($data['FILE']['tmp_name']);
                
                $row = 0;
                if (($handle = fopen($data['FILE']['tmp_name'],"r")) !== FALSE) {
                    while (($record = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        /****************************************
                         * Departments
                         ****************************************/
                        $current_dept = "";
                        $dept = new DepartmentModel($this->adapter);
                        $result = $dept->read(['CODE' => sprintf('%05d', $record[$DEPT_CODE])]);
                        if ($dept->UUID === NULL) {
                            $dept->UUID = $uuid->generate()->value;
                            $dept->CODE = sprintf('%05d', $record[$DEPT_CODE]);
                            $dept->NAME = $record[$DEPT_NAME];
                            $dept->DATE_CREATED = $today;
                            $dept->DATE_MODIFIED = $today;
                            $dept->STATUS = $dept::ACTIVE_STATUS;
                            $dept->setCurrentUser('SYSTEM');
                            $dept->create();
                        }
                        $current_dept = $dept->UUID;
                        
                        /****************************************
                         * Employees
                         ****************************************/
                        $emp = new EmployeeModel($this->adapter);
                        $result = $emp->read(['EMP_NUM' => sprintf('%06d', $record[$EMP_NUM])]);
                        if ($emp->UUID === NULL) {
                            $emp->UUID = $uuid->generate()->value;
                            $emp->EMP_NUM = sprintf('%06d', $record[$EMP_NUM]);
                            $emp->FNAME = $record[$FNAME];
                            $emp->LNAME = $record[$LNAME];
                            $emp->EMAIL = $record[$FNAME] . '.' . $record[$LNAME] . '@middletownct.gov';
                            $emp->DEPT = $current_dept;
                            $emp->TIME_GROUP = sprintf('%03d', $record[$PTG]);
                            $emp->TIME_SUBGROUP = sprintf('%03d', $record[$PTSG]);
                            $emp->DATE_CREATED = $today;
                            $emp->DATE_MODIFIED = $today;
                            $emp->STATUS = $emp::ACTIVE_STATUS;
                            $emp->setCurrentUser('SYSTEM');
                            $create_result = $emp->create();
                        }
                        $row++;
                    }
                    fclose($handle);
                    unlink($data['FILE']['tmp_name']);
                }
                $this->flashMessenger()->addSuccessMessage("Successfully imported employees.");
                
            } else {
                $this->flashmessenger()->addErrorMessage("Form is Invalid.");
            }
        }
        
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }
}
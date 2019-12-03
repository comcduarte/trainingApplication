<?php
namespace Training\Controller;

use Employee\Form\UploadFileForm;
use Midnet\Controller\AbstractConfigController;
use Midnet\Model\Uuid;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Ddl\DropTable;
use Zend\Db\Sql\Ddl\Column\Datetime;
use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\PrimaryKey;
use Zend\View\Model\ViewModel;
use Employee\Model\EmployeeModel;
use Training\Model\TrainingModel;

class TrainingConfigController extends AbstractConfigController
{
    public function __construct()
    {
        $this->setRoute('training/config');
    }
    
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        
        $importForm = new UploadFileForm('CLASSES');
        $importForm->initialize();
        $importForm->addInputFilter();
        $view->setVariable('importForm', $importForm);
        
        $view->setTemplate('training/config');
        
        return $view;
    }
    
    public function clearDatabase()
    {
        $sql = new Sql($this->adapter);
        $ddl = [];
        
        $ddl[] = new DropTable('classes');
        $ddl[] = new DropTable('employee_classes');
        
        foreach ($ddl as $obj) {
            $this->adapter->query($sql->buildSqlString($obj), $this->adapter::QUERY_MODE_EXECUTE);
        }
    }
    
    public function createDatabase()
    {
        $sql = new Sql($this->adapter);
        
        /******************************
         * CLASSES
         ******************************/
        $ddl = new CreateTable('classes');
        
        $ddl->addColumn(new Varchar('UUID', 36));
        $ddl->addColumn(new Integer('STATUS', TRUE));
        $ddl->addColumn(new Datetime('DATE_CREATED', TRUE));
        $ddl->addColumn(new Datetime('DATE_MODIFIED', TRUE));
        
        $ddl->addColumn(new Varchar('CODE', 255));
        $ddl->addColumn(new Varchar('NAME', 255));
        $ddl->addColumn(new Varchar('INSTRUCTOR', 255, TRUE));
        $ddl->addColumn(new Varchar('CATEGORY', 255, TRUE));
        $ddl->addColumn(new Varchar('DATE_SCHEDULE', 255, TRUE));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
        
        /******************************
         * EMPLOYEE_CLASSES
         ******************************/
        $ddl = new CreateTable('employee_classes');
        
        $ddl->addColumn(new Varchar('UUID', 36));

        $ddl->addColumn(new Varchar('EMP_UUID', 36));
        $ddl->addColumn(new Varchar('CLASS_UUID', 36));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
    }
    
    public function createfoldersAction()
    {
        $this->createFolders();
        $this->flashMessenger()->addSuccessMessage("Folders Created.");
        return $this->redirect()->toRoute($this->getRoute());
    }
    
    public function createFolders()
    {
        if (!file_exists('./data/files')) {
            mkdir('./data/files', 0777, true);
        }
    }
    
    public function importclassesAction()
    {
        /****************************************
         * Field Indexes
         ****************************************/
        $FNAME = 0;
        $LNAME = 1;
        $EMP_NUM = 2;
        $DEPT = 3;
        $CAT = 4;
        $NAME = 5;
        $DATE = 6;
        
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
                        $employee = new EmployeeModel($this->adapter);
                        $result = NULL;
                        
                        if ($record[$EMP_NUM] === NULL) {
                            /****************************************
                             * No Employee ID Specified
                             ****************************************/
                            $result = $employee->read(['LNAME' => $record[$LNAME], ['FNAME' => $record[$FNAME]]]);
                            if (!$result) {
                                /****************************************
                                 * Create Unmatched Employee
                                 ****************************************/
                                $employee->UUID = $uuid->generate()->value;
                                $employee->EMP_NUM = sprintf('%06d', $employee->UUID);
                                $employee->FNAME = $record[$FNAME];
                                $employee->LNAME = $record[$LNAME];
                                $employee->EMAIL = $record[$FNAME] . '.' . $record[$LNAME] . '@middletownct.gov';
                                $employee->DATE_CREATED = $today;
                                $employee->DATE_MODIFIED = $today;
                                $employee->STATUS = $employee::ACTIVE_STATUS;
                                $employee->create();
                            }
                        } else {
                            if (is_numeric($record[$EMP_NUM])) {
                                $tempEmpNum = sprintf('%06d', $record[$EMP_NUM]);
                            } else {
                                /****************************************
                                 * Force Match by Name
                                 ****************************************/
                                $tempEmpNum = NULL;
                            }
                            
                            
                            $result = $employee->read(['EMP_NUM' => $tempEmpNum]);
                            if (!$result) {
                                $result = $employee->read(['LNAME' => $record[$LNAME], 'FNAME' => $record[$FNAME]]);
                            }
                            if (!$result) {
                                /****************************************
                                 * Create Unmatched Employee
                                 ****************************************/
                                $employee->UUID = $uuid->generate()->value;
                                $employee->EMP_NUM = sprintf('%06d', $record[$EMP_NUM]);
                                $employee->FNAME = $record[$FNAME];
                                $employee->LNAME = $record[$LNAME];
                                $employee->EMAIL = $record[$FNAME] . '.' . $record[$LNAME] . '@middletownct.gov';
                                $employee->DATE_CREATED = $today;
                                $employee->DATE_MODIFIED = $today;
                                $employee->STATUS = $employee::ACTIVE_STATUS;
                                $employee->create();
                            }
                        }
                        
                        /** I Have the Employee **/
                        
                        $class = new TrainingModel($this->adapter);
                        $result = NULL;
                        
                        $result = $class->read(['NAME' => $record[$NAME], 'DATE_SCHEDULE' => date('Y-m-d', strtotime($record[$DATE]))  . 'T12:00']);
                        if (!$result) {
                            /****************************************
                             * Create Training Class
                             ****************************************/
                            $class->UUID = $uuid->generate()->value;
                            
                            $class->CODE = sprintf('%06d', $class->UUID);
                            $class->STATUS = $class::ACTIVE_STATUS;
                            $class->DATE_CREATED = $today;
                            $class->DATE_MODIFIED = $today;
                            $class->DATE_SCHEDULE = date('Y-m-d', strtotime($record[$DATE])) . 'T12:00';
                            $class->CATEGORY = $record[$CAT];
                            $class->NAME = $record[$NAME];
                            $class->create();
                        }
                        
                        /** I Have the Class **/
                        
                        
                        /****************************************
                         * Assign Employee
                         ****************************************/
                        $class->assignEmployee($employee->UUID);
                        
                        $row++;
                    }
                    fclose($handle);
                    unlink($data['FILE']['tmp_name']);
                }
                $this->flashMessenger()->addSuccessMessage("Successfully imported classes.");
            } else {
                $this->flashmessenger()->addErrorMessage("Form is Invalid.");
            }
        }
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }
}
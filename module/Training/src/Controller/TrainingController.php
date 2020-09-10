<?php
namespace Training\Controller;

use Annotation\Model\AnnotationModel;
use Midnet\Controller\AbstractBaseController;
use Midnet\Model\Uuid;
use Training\Form\FindTrainingForm;
use Training\Model\TrainingModel;
use User\Model\UserModel;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\Like;
use Zend\View\Model\ViewModel;
use Exception;

class TrainingController extends AbstractBaseController
{
    public $EmployeeClassesForm;
    
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->from('classes');
        $select->columns([
            'UUID' => 'UUID',
            'Code' => 'CODE',
            'Name' => 'NAME',
            'Instructor' => 'INSTRUCTOR',
            'Session' => 'DATE_SCHEDULE',
        ]);
        $select->where(['classes.STATUS' => $this->model::ACTIVE_STATUS]);
        $select->order(['NAME','DATE_SCHEDULE']);
        
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
        $uuid = new Uuid();
        
        $view = new ViewModel();
        $view = parent::updateAction();
        
        /****************************************
         * EMPLOYEE SUBTABLE
         ****************************************/
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->columns(['UUID'])
            ->from('employee_classes')
            ->join('employees', 'employee_classes.EMP_UUID = employees.UUID', ['UUID_E' => 'UUID', 'Employee ID' => 'EMP_NUM', 'Employee Name' => new Expression("CONCAT(LNAME, '\, ', FNAME)")], Join::JOIN_INNER)
            ->where([new Like('CLASS_UUID', $this->model->UUID)]);
            
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $roster = $resultSet->toArray();
        
        
        $view->setVariable('employee_classes_form', $this->EmployeeClassesForm);
        $view->setVariable('uuid', $this->model->UUID);
        $view->setVariable('roster', $roster);
        
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
            ->where([new Like('NAME', 'CLASS - %')]);  
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $reports = $resultSet->toArray();
        
        $view->setVariable('reports', $reports);
        
        /****************************************
         * ANNOTATIONS
         ****************************************/
        $annotation = new AnnotationModel($this->adapter);
        $where = new Where([
            new Like('TABLENAME', $this->model->getTableName()),
            new Like('PRIKEY', $this->model->UUID),
        ]);
        $annotations = $annotation->fetchAll($where, ['DATE_CREATED DESC']);
        $notes = [];
        foreach ($annotations as $annotation) {
            $user = new UserModel($this->adapter);
            $user->read(['UUID' => $annotation['USER']]);
            
            $notes[] = [
                'USER' => $user->USERNAME,
                'ANNOTATION' => $annotation['ANNOTATION'],
                'DATE_CREATED' => $annotation['DATE_CREATED'],
            ];
        }
        $view->setVariables([
            'annotations' => $notes,
            'annotations_prikey' => $this->model->UUID,
            'annotations_tablename' => $this->model->getTableName(),
            'annotations_user' => '',
        ]);
        
        return $view;
    }
    
    public function assignAction()
    {
        $form = $this->EmployeeClassesForm;
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $model = new TrainingModel($this->adapter);
                $data = $form->getData();
                
                $class_uuid = $data['CLASS'];
                $employee_uuid = $data['EMP'];
                
                $model->read(['UUID' => $class_uuid]);
                
                /****************************************
                 * HISTORY
                 ****************************************/
                $user = $this->currentUser();
                $model->setCurrentUser($user->USERNAME);
                
                $model->assignEmployee($employee_uuid);
                $model->update();
            }
        }
        
        //-- Return to previous screen --//
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }
    
    public function unassignAction()
    {
        $join_uuid = $this->params()->fromRoute('uuid',0);
        
        /****************************************
         * HISTORY
         ****************************************/
        $user = $this->currentUser();
        $this->model->setCurrentUser($user->USERNAME);
        
        $this->model->unassignEmployee($join_uuid);
        
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }

    public function findAction()
    {
        $view = new ViewModel();
        $view->setTemplate('training/training/index');
        
        $model = new TrainingModel($this->adapter);
        $form = new FindTrainingForm();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $data = $request->getPost();
            $form->setData($data);
            
            if ($form->isValid()) {
                $sql = new Sql($this->adapter);
                
                $select = new Select();
                $select->from($model->getTableName());
                $select->columns(['UUID','NAME','DATE_SCHEDULE']);
                $select->order('DATE_SCHEDULE DESC');
                
                
                $predicate = new Where();
                $predicate->equalTo('NAME', $data['NAME']);
                
                $select->where($predicate);
                $select->order('NAME');
                
                $statement = $sql->prepareStatementForSqlObject($select);
                $resultSet = new ResultSet();
                
                try {
                    $results = $statement->execute();
                    $resultSet->initialize($results);
                } catch (Exception $e) {
                    return $e;
                }
                
                $classes = $resultSet->toArray();
            }
        }
        
        $header = [];
        if (!empty($data)) {
            $header = array_keys($classes[0]);
        }
        
        $view->setVariable('header', $header);
        $view->setVariable('data', $classes);
        $view->setVariable('primary_key', $model->getPrimaryKey());
        return $view;
    }

}
<?php
namespace Training\Model;

use Midnet\Model\DatabaseObject;
use Midnet\Model\Uuid;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\InputFilter\InputFilter;
use RuntimeException;

class TrainingModel extends DatabaseObject
{
    public $CODE;
    public $NAME;
    public $INSTRUCTOR;
    public $CATEGORY;
    public $DATE_SCHEDULE;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('classes');
    }
    
    public function create()
    {
        parent::create();
        
        if (!file_exists('./data/files/' . $this->UUID)) {
            mkdir('./data/files/' . $this->UUID, 0777, true);
        }
        
        return $this;
    }
    
    public function delete()
    {
        parent::delete();
        
        // TODO: Remove file upload directory and its contents.  Possibly Archive them.
        
        return true;
    }
    
    public function assignEmployee($employee_uuid)
    {
        $sql = new Sql($this->adapter);
        $uuid = new Uuid();
        
        $columns = [
            'UUID',
            'EMP_UUID',
            'CLASS_UUID',
        ];
        
        $values = [
            $uuid->value,
            $employee_uuid,
            $this->UUID,
        ];
        
        
        $insert = new Insert();
        $insert->into('employee_classes');
        $insert->columns($columns);
        $insert->values($values);
        
        $statement = $sql->prepareStatementForSqlObject($insert);
        
        try {
            $statement->execute();
        } catch (RuntimeException $e) {
            return $e;
        }
        return $this;
    }
    
    public function unassignEmployee($join_uuid)
    {
        $sql = new Sql($this->adapter);
        
        $delete = new Delete();
        $delete->from('employee_classes');
        
        if ($join_uuid != NULL) {
            $delete->where(['UUID' => $join_uuid]);
        }
        
        $statement = $sql->prepareStatementForSqlObject($delete);
        
        try {
            $statement->execute();
        } catch (RuntimeException $e) {
            return $e;
        }
        return $this;
    }
    
    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFilter = parent::getInputFilter();
        
        $inputFilter->add([
            'name' => 'FILE',
            'required' => FALSE,
            'filters' => [
                [
                    'name' => 'filerenameupload',
                    'options' => [
                        'target'    => './data/files/' . $this->UUID,
                        'useUploadName' => TRUE,
                        'useUploadExtension' => TRUE,
                        'overwrite' => TRUE,
                        'randomize' => FALSE,
                    ],
                ],
            ],
            'validators' => [
                [
                    'name'    => 'FileMimeType',
                    'options' => [
                        'mimeType'  => ['application/pdf', 'text/plain']
                    ]
                ],
            ],
        ]);
        
        return $inputFilter;
    }
}
<?php
namespace Training\Model;

use Midnet\Model\DatabaseObject;
use Midnet\Model\Uuid;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use RuntimeException;
use Zend\Db\Sql\Delete;

class TrainingModel extends DatabaseObject
{
    public $CODE;
    public $NAME;
    public $CATEGORY;
    public $DATE_SCHEDULE;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('classes');
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
    
}
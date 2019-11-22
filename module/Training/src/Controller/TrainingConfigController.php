<?php
namespace Training\Controller;

use Midnet\Controller\AbstractConfigController;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Ddl\DropTable;
use Zend\Db\Sql\Ddl\Column\Datetime;
use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\PrimaryKey;

class TrainingConfigController extends AbstractConfigController
{
    public function __construct()
    {
        $this->setRoute('training/config');
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
        $ddl->addColumn(new Varchar('INSTRUCTOR', 255));
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
}
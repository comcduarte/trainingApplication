<?php
namespace Training\Form;

use Midnet\Form\AbstractBaseForm;
use Midnet\Form\Element\DatabaseSelectObject;
use Midnet\Model\Uuid;
use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Form\Element\Hidden;

class EmployeeClassesForm extends AbstractBaseForm
{
    use AdapterAwareTrait;
    
    public function initialize()
    {
        parent::initialize();
        
        $uuid = new Uuid();
        
        $this->add([
            'name' => 'UUID',
            'type' => Hidden::class,
            'attributes' => [
                'value' => $uuid->value,
                'id' => 'UUID',
            ],
        ]);
        
        $this->add([
            'name' => 'CLASS',
            'type' => Hidden::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'CLASS',
                'value' => '',
            ],
        ]);
        
        $this->add([
            'name' => 'EMP',
            'type' => DatabaseSelectObject::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'USER',
            ],
            'options' => [
                'label' => 'Employee',
                'database_adapter' => $this->adapter,
                'database_table' => 'employees',
                'database_id_column' => 'UUID',
                'database_value_column' => 'LNAME',
            ],
            
        ]);
        
        $this->remove('STATUS');
    }
}
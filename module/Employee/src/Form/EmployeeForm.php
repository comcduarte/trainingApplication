<?php
namespace Employee\Form;

use Midnet\Form\AbstractBaseForm;
use Midnet\Form\Element\DatabaseSelectObject;
use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Form\Element\File;
use Zend\Form\Element\Text;

class EmployeeForm extends AbstractBaseForm
{
    use AdapterAwareTrait;
    
    public function initialize()
    {
        parent::initialize();
        
        $this->add([
            'name' => 'EMP_NUM',
            'type' => Text::class,
            'attributes' => [
                'id' => 'EMP_NUM',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Employee Number',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'FNAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'FNAME',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'First Name',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'LNAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'LNAME',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Last Name',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'EMAIL',
            'type' => Text::class,
            'attributes' => [
                'id' => 'EMAIL',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Email Address',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'DEPT',
            'type' => DatabaseSelectObject::class,
            'attributes' => [
                'id' => 'DEPT',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Department',
                'database_adapter' => $this->adapter,
                'database_table' => 'departments',
                'database_id_column' => 'UUID',
                'database_value_column' => 'NAME',
                
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'FILE',
            'type' => File::class,
            'attributes' => [
                'id' => 'FILE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'File',
            ],
        ],['priority' => 100]);
    }
}
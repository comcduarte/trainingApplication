<?php
namespace Training\Form;

use Midnet\Form\AbstractBaseForm;
use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Form\Element\DateTimeLocal;
use Zend\Form\Element\File;
use Zend\Form\Element\Text;

class TrainingForm extends AbstractBaseForm
{
    use AdapterAwareTrait;
    
    public function initialize()
    {
        parent::initialize();
        
        $this->add([
            'name' => 'CODE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'CODE',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Session Code',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'NAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'NAME',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Session Name',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'INSTRUCTOR',
            'type' => Text::class,
            'attributes' => [
                'id' => 'INSTRUCTOR',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Instructor',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'CATEGORY',
            'type' => Text::class,
            'attributes' => [
                'id' => 'CATEGORY',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Session Category',
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
        
        $this->add([
            'name' => 'DATE_SCHEDULE',
            'type' => DateTimeLocal::class,
            'attributes' => [
                'id' => 'DATE_SCHEDULE',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Date',
            ],
        ],['priority' => 100]);
    }
}
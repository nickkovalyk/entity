<?php

namespace Post\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Post\Entity\Post;
use Zend\Captcha;
use Zend\Captcha\Image;

/**
 * This form is used to collect post data.
 */
class CommentForm extends Form
{


    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('post-form');
        $this->setAttribute('novalidate', true);
        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        

   //TEXT
        $this->add([
            'type'  => 'text',
            'name' => 'username',
            'attributes' => [
                'id' => 'username',
                'v-model' => "commentForm.data.username"
            ],
            'options' => [
                'label' => 'Username',
            ],
            'required' => true,
        ]);
    //EMAIL
        $this->add([
            'type'  => 'email',
            'name' => 'email',
            'attributes' => [
                'id' => 'email'
            ],
            'options' => [
                'label' => 'Email',
            ],
            'required' => true,
        ]);

    //CONTENT
        $this->add([
            'type'  => 'textarea',
            'name' => 'content',
            'attributes' => [
                'id' => 'content'
            ],
            'required' => true,
            'options' => [
                'label' => 'Your message...',
            ],
        ]);
        //HOMEPAGE

        $this->add([
            'type' => 'Zend\Form\Element\Url',
            'name' => 'homepage',
            'attributes' => [
                'id' => 'homepage'
            ],
            'required' => false,
            'options' => [
                'label' => 'Homepage',
            ],
        ]);
        //ATTACHMENT
        $this->add([
            'type'  => 'file',
            'name' => 'attachment',
            'attributes' => [
                'id' => 'attachment',
            ],
            'options' => [
                'label' => 'Attachment',
            ],
        ]);
//POST_ID
        $this->add([
            'type'  => 'hidden',
            'name' => 'post_id',
            'attributes' => [
                'id' => 'post_id'
            ],
            'required' => true,

        ]);
//PARENT_ID
        $this->add([
            'type'  => 'hidden',
            'name' => 'parent_id',
            'attributes' => [
                'id' => 'parent_id'
            ],
            'required' => false,

        ]);

        $captchaImage = new Image([
            'font' => "/public/fonts/arial.ttf",
            'width' => 250,
            'height' => 100,
            'dotNoiseLevel' => 40,
            'lineNoiseLevel' => 3
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'options' => [
                'label' => 'Please verify you are human',
                'captcha' => $captchaImage,
            ],
        ]);

        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Create',
                'id' => 'submitbutton',
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {

        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'     => 'username',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'StripNewlines'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 1024
                    ],
                ],
            ],
        ]);
         $inputFilter->add([
             'name'     => 'email',
             'required' => true,
             'filters'  => [
                 ['name' => 'StringTrim'],
                 ['name' => 'StripTags'],
             ],
             'validators' => [
                 [
                     'name' => 'EmailAddress',
                     'options' => [
                         'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                         'useMxCheck' => false,
                     ],
                 ],
             ],
         ]);
          $inputFilter->add([
              'name'     => 'homepage',
              'required' => false,
              'filters'  => [
                  ['name' => 'StringTrim'],
                  ['name' => 'StripTags'],
                  ['name' => 'StripNewlines'],
              ],
              'validators' => [
                  ['name'    => 'StringLength',
                      'options' => [
                          'min' => 1,
                          'max' => 1024
                      ],
                  ],
              ],
          ]);


        $inputFilter->add([
            'name'     => 'content',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 4096
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'parent_id',
            'required' => false,

        ]);

    }
}
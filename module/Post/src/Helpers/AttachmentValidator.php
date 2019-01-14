<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 11.01.19
 * Time: 18:43
 */

namespace Post\Helpers;


class AttachmentValidator
{
    protected $errors = [];
    private $config;
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * check mimes and return mime type or false
     * @param $path
     * @return mixed
     *
     */
    protected function checkMime($path)
    {
        $mime = mime_content_type($path);
        $bool = (in_array(mime_content_type($path), $this->config['allowableMimeTypes']));
        if(!$bool) {
            $this->errors = array_merge($this->errors, ['Not allowed extension', $mime]);
        }
        return $mime ?: false;

    }

    protected function checkSize($path)
    {
        $size = filesize($path);
        if($size > $this->config['maxSizeOfText']) {
            $this->errors = array_merge($this->errors, ['File is too large']);
        }
    }

    public function validate($path)
    {
       $mime = $this->checkMime($path);
       if($mime) {
           if($mime === 'text/plain') {
              $this->checkSize($path);
           }
       }

       if(empty($this->errors)) {
           return true;
       }
       return false;
    }

    public function getErrors()
    {
        return ['attachment' => $this->errors];
    }





}
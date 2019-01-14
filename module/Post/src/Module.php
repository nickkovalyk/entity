<?php
namespace Post;
use Zend\Mvc\MvcEvent;
use Zend\View\HelperPluginManager;
class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [

            ],

        ];
    }

}

<?php
namespace Post\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Post\Form\CommentForm;
use Zend\View\Model\ViewModel;
use Post\Helpers\TagFilter;
use Zend\View\Model\JsonModel;


class CommentController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Post manager.
     * @var Post\Service\CommentManager
     */
    private $commentManager;

    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $commentManager, $attachmentHandler)
    {
        $this->entityManager = $entityManager;
        $this->commentManager = $commentManager;
        $this->attachmentHandler = $attachmentHandler;
    }


    public function createAction()
    {
        $form = new CommentForm();
        if ($this->getRequest()->isPost()) {

            $errors = [];
            $request = $this->getRequest();
            $data = $this->params()->fromPost();

            //if ajax, need to convert captcha
            if ($this->getRequest()->isXmlHttpRequest()) {
                $parsedCaptcha = explode('||', $data['captcha']);
                $data['captcha'] = ['id' => $parsedCaptcha[0], 'input' => $parsedCaptcha[1]];
            }

            $file = $request->getFiles()->toArray()['attachment'] ?? null;
            if(!$file) {
                $errors['attachment'] = ['File is required'];
            }

            //SOME VALIDATION AND FILTER TO "EXOTIC" FIELDS
            $content = TagFilter::closeAndStrip($data['content'], '<a><code><strong><i>');
            $data['content'] = $content;

            $form->setData($data);
            $isValid = $form->isValid();
            if ($file && $file['tmp_name']) {

                $attachment = $this->attachmentHandler;
                $isFileValid = $attachment->validate($file['tmp_name']);
                if (!$isFileValid) {
                    $errors = array_merge($errors, $attachment->getErrors());
                }
            }

            if ($isValid && empty($errors)) {
                $data = $form->getData();
                $newComment = $this->commentManager->createNewComment(array_merge($data, ['attachment' => $file]));

            } else {
                $errors = array_merge($errors, $form->getMessages());
            }

            // AJAX RESPONSE
            if ($this->getRequest()->isXmlHttpRequest()) {
                if ($this->getRequest()->isPost()) {
                    if (!empty($errors)) {
                        return new JsonModel([
                            'status' => 'error',
                            'errors' => $errors,
                        ]);
                    }
                    return new JsonModel([
                        'status' => 'ok',
                        'attachment' => $newComment->getAttachment(),
                        'id' => $newComment->getId()
                    ]);
                }
            }
        }
        $this->redirect()
            ->toRoute('post',['action' => 'show', 'id' => $this->params()->fromRoute('id', 1)]);
    }



}
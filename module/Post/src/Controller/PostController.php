<?php
namespace Post\Controller;

use Post\Form\CommentForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Post\Entity\Post;
use Post\Entity\Comment;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Doctrine\Common\Collections\Criteria;
use Zend\Paginator\Adapter;


class PostController extends AbstractActionController
{
    protected $entityManager;

    public function __construct($entityManager, $config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    public function indexAction()
    {
        $posts = $this->entityManager->getRepository(Post::class)
            ->findBy(['id' => 1]);

        return new ViewModel();


    }

    public function showAction()
    {
        $id = (int) $this->params()->fromRoute('id', 1);
        $post = $this->entityManager->getRepository(Post::class)
            ->findOneBy(['id' => $id]);

        $page = $this->params()->fromQuery('page', 1);
        $order = $this->params()->fromQuery('order', 'desc');
        $sortBy = $this->params()->fromQuery('sortBy', 'createdAt');

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c')
            ->from(Comment::class,'c')
            ->where('c.parentId is NULL');


        if ($sortBy) {
            $sorts = ['username', 'createdAt', 'email'];
            if(in_array($sortBy, $sorts, true)) {
                $queryBuilder->orderBy('c.'.$sortBy, (strtolower($order) == 'desc' ? 'desc' : 'asc'));
            }

        } else {
            $queryBuilder->orderBy('c.createdAt', 'DESC');
        }
        $queryBuilder = $queryBuilder->getQuery();
        $adapter = new DoctrineAdapter(new ORMPaginator($queryBuilder, true));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage($this->config['commentPagination']['itemsPerPage']);
        $paginator->setCurrentPageNumber($page);



        $form = new CommentForm();
        return new ViewModel([
            'post' => $post,
            "form" => $form,
            'comments' => $paginator,
            'sortBy' => $sortBy,
            'order' => $order,
            'currentPage' => $page,
            'perPage' => $this->config['commentPagination']['itemsPerPage'],
            'lastPage' => ceil($paginator->getTotalItemCount() / $this->config['commentPagination']['itemsPerPage'])
        ]);
    }
}
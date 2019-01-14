<?php
namespace Post\Service;

use Post\Entity\Post;
use Post\Entity\Comment;
use Zend\Filter\StaticFilter;
use Post\Helpers\ImageHandler;

// The PostManager service is responsible for adding new posts.
class CommentManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $config;
    // Constructor is used to inject dependencies into the service.
    public function __construct($entityManager, $config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    // This method adds a new post.
    public function createNewComment($data)
    {

        // Create new Post entity.
        $comment = new Comment();
        $comment->setUsername($data['username']);
        $comment->setEmail($data['email']);
        $comment->setContent(addslashes($data['content']));
        $comment->setHomepage($data['homepage']);
        $remote = new \Zend\Http\PhpEnvironment\RemoteAddress;
        $ip = $remote->getIpAddress();
        $comment->setIp($ip);
        $comment->setBrowser($_SERVER["HTTP_USER_AGENT"]);
        $currentDate = date('Y-m-d H:i:s');
        $comment->setCreatedAt($currentDate);


        //file handle
        if($data['attachment']['tmp_name']) {
            $tmpPath = $data['attachment']['tmp_name'];
            $mime = mime_content_type($tmpPath);
            $extension = array_flip($this->config['attachment']['allowableMimeTypes'])[$mime];
            $directory = $this->config['fileSettings']['uploadPath']['postComments'];
            $filename = uniqid().".$extension";
            $fullpath = "$directory/$filename";

            // IF FILE ARE IMAGE
            if($extension != 'txt') {

                list($width, $height) = getimagesize($tmpPath);
                $maxWidth  = $this->config['fileSettings']['imageSizes']['width'];
                $maxHeight = $this->config['fileSettings']['imageSizes']['height'];

                if($width > $maxWidth || $height > $maxHeight) {
                    $fullpath = ImageHandler::resizeImage($tmpPath, $maxWidth, $maxHeight, $directory);
                } else {
                    move_uploaded_file($tmpPath, $fullpath);
                }
            //txt file
            } else {
                move_uploaded_file($tmpPath, $fullpath);
            }
            $fromRootPath = explode('public',$fullpath);
            $comment->setAttachment($fromRootPath[1]);
        }

        // Add the entity to entity manager.
        $this->entityManager->persist($comment);
        $post = $this->entityManager->getRepository(Post::class)
            ->findOneBy(['id' => $data['post_id']]);
        if(!$post) {
            throw new \Exception('Post entity not found');
        }
        $post->addComment($comment);
        $comment->setPost($post);
        //set self parent
        if($data['parent_id']) {

            $parent =  $this->entityManager->getRepository(Comment::class)
                ->findOneBy(['id' => $data['parent_id']]);

            $parent->addChildren($comment);
            $comment->setParent($parent);
            $this->entityManager->persist($parent);
        }

        $this->entityManager->flush();

        return $comment;
    }

}
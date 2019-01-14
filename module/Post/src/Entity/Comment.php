<?php
namespace Post\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
/**
 * This class represents a comment related to a blog post.
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment
{

//@ORM\JoinColumn(name="parent_id", referencedColumnName="id")
    // ...
    /**
     * One Category has Many Categories.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parent")
     *@ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL, defa")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="children")
     *
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity="\Post\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    protected $post;

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="username")
     */
    protected $username;

    /**
     * @ORM\Column(name="content")
     */
    protected $content;

    /**
     * @ORM\Column(name="email")
     */
    protected $email;

    /**
     * @ORM\Column(name="attachment")
     */
    protected $attachment;

    /**
     * @ORM\Column(name="homepage")
     */
    protected $homepage;
    /**
     * @ORM\Column(name="ip")
     */
    protected $ip;

    /**
     * @ORM\Column(name="parent_id")
     */
    protected $parentId;

    /**
     * @ORM\Column(name="browser")
     */
    protected $browser;

    /**
     * @ORM\Column(name="created_at")
     */
    protected $createdAt;



    public function __construct() {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getChildren()
    {
        if(!$this->hasChildren()) {
            return [];
        }
        $criteria = Criteria::create()->orderBy(["createdAt" => Criteria::ASC]);
        return $this->children->matching($criteria);

    }
    public function addChildren($child)
    {
        $this->children[] = $child;
    }
    public function hasChildren()
    {
        return !is_null($this->children);
    }

    /*
     * Returns associated post.
     * @return \Post\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Sets associated post.
     * @param \Post\Entity\Post $post
     */
    public function setPost($post)
    {
        $this->post = $post;
        $post->addComment($this);
    }
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;
    }


    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getHomepage()
    {
        return $this->homepage;
    }

    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }



    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParent($parentComment)
    {
        $this->parent = $parentComment;
    }

    public function getAttachment()
    {
        if($this->attachment) {
            $type = (pathinfo($this->attachment)['extension'] == 'txt') ? 'txt' : 'img';
            return ['path' => $this->attachment, 'type' => $type];
        }
        return null;

    }

    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }



}
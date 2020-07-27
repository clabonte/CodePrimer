<?php
/*
 * This file has been generated by CodePrimer.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CodePrimer\Tests\Entity;

use \DateTime;
use \DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Topic
 * A topic regroups a set of posts made by various authors
 * @package CodePrimer\Tests\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TopicRepository")
 * @ORM\Table(name="topics")
 */
class Topic
{
    /**
     * @var string The topic title
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title = '';

    /**
     * @var string|null The topic description
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = null;

    /**
     * @var Collection|User[]|null List of authors who are allowed to post on this topic
     * @ORM\ManyToMany(targetEntity="CodePrimer\Tests\Entity\User", inversedBy="topics")
     */
    protected $authors = null;

    /**
     * @var Collection|Post[]|null List of posts published on this topic
     * @ORM\OneToMany(targetEntity="CodePrimer\Tests\Entity\Post", mappedBy="topic", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $posts = null;

    /**
     * @var DateTimeInterface|null Time at which the post was created
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created = null;

    /**
     * @var DateTimeInterface|null Last time at which the post was updated
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated = null;

    /**
     * @var string DB unique identifier field
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="string", length=36)
     */
    protected $id = '';

    /**
     * Topic default constructor
     * @var string $title The topic title
     * @var string $id DB unique identifier field
     */
    public function __construct(
        string $title,
        string $id
    ) {
        $this->title = $title;
        $this->authors = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->id = $id;
    }

    /**
     * @param string $title
     * @return Topic
     */
    public function setTitle(string $title): Topic
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string|null $description
     * @return Topic
     */
    public function setDescription(?string $description): Topic
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param Collection|User[]|null $authors
     * @return Topic
     */
    public function setAuthors(Collection $authors): Topic
    {
        $this->authors = $authors;
        return $this;
    }

    /**
     * @return Collection|User[]|null
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    /**
     * @param Collection|Post[]|null $posts
     * @return Topic
     */
    public function setPosts(Collection $posts): Topic
    {
        $this->posts = $posts;
        return $this;
    }

    /**
     * @return Collection|Post[]|null
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @param DateTimeInterface|null $created
     * @return Topic
     */
    public function setCreated(?DateTimeInterface $created): Topic
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @param DateTimeInterface|null $updated
     * @return Topic
     */
    public function setUpdated(?DateTimeInterface $updated): Topic
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdated(): ?DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @param string $id
     * @return Topic
     */
    public function setId(string $id): Topic
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }


    /**
     * Checks if this Topic contains at least one instance of a given User
     * @param User $authors
     * @return boolean
     */
    public function containsAuthor(User $authors): boolean
    {
        return $this->authors->contains($authors);
    }

    /**
     * Adds a User instance to this Topic if it is not already present
     * @param User $authors
     * @return Topic
     */
    public function addAuthor(User $authors): Topic
    {
        if (!$this->authors->contains($authors)) {
            $this->authors[] = $authors;
            $authors->setTopics($this);
        }

        return $this;
    }

    /**
     * Removes all instances of a given User from this Topic
     * @param User $authors
     * @return Topic
     */
    public function removeAuthor(User $authors): Topic
    {
        if ($this->authors->contains($authors)) {
            $this->authors->removeElement($authors);
            // set the owning side to null (unless already changed)
            if ($authors->getTopics() === $this) {
                $authors->setTopics(null);
            }
        }

        return $this;
    }

    /**
     * Checks if this Topic contains at least one instance of a given Post
     * @param Post $posts
     * @return boolean
     */
    public function containsPost(Post $posts): boolean
    {
        return $this->posts->contains($posts);
    }

    /**
     * Adds a Post instance to this Topic if it is not already present
     * @param Post $posts
     * @return Topic
     */
    public function addPost(Post $posts): Topic
    {
        if (!$this->posts->contains($posts)) {
            $this->posts[] = $posts;
            $posts->setTopic($this);
        }

        return $this;
    }

    /**
     * Removes all instances of a given Post from this Topic
     * @param Post $posts
     * @return Topic
     */
    public function removePost(Post $posts): Topic
    {
        if ($this->posts->contains($posts)) {
            $this->posts->removeElement($posts);
            // set the owning side to null (unless already changed)
            if ($posts->getTopic() === $this) {
                $posts->setTopic(null);
            }
        }

        return $this;
    }
    /**
     * Automatically manage timestamps upon entity creation
     * @ORM\PrePersist
     */
    public function updateTimestampsBeforePersist(): void
    {
        $this->updated = new DateTime('now');
        if ($this->created === null) {
            $this->created = new DateTime('now');
        }
    }

    /**
     * Automatically manage timestamp upon entity update
     * @ORM\PreUpdate
     */
    public function updateTimestampBeforeUpdate(): void
    {
        $this->updated = new DateTime('now');
    }
}

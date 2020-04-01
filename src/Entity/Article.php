<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\IsValidOwner;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"security"="is_granted('VIEW_LIST', object)"},
 *          "post"={"security"="is_granted('CREATE', object)"},
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('VIEW', object)"},
 *          "put"={"security"="is_granted('EDIT', object)"},
 *          "delete"={"security"="is_granted('DELETE', object)"}
 *     },
 *     normalizationContext={"groups"={"article:read", "article:list", "admin:read"}},
 *     denormalizationContext={"groups"={"article:write", "admin:write"}},
 * )
 * @ApiFilter(PropertyFilter::class)
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"article:read", "article:list", "article:write", "admin:read", "admin:write"})
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     maxMessage="Describe your cheese in 50 chars or less"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"article:read", "article:write", "admin:read", "admin:write"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"article:read", "article:list", "article:write", "admin:read", "admin:write"})
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"article:read", "article:list", "admin:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"article:write", "admin:read", "admin:write"})
     */
    private $isPublished;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"article:read", "article:list", "admin:read"})
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"article:read", "article:list", "article:write", "admin:read", "admin:write"})
     */
    private $published_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"article:read", "article:list", "admin:read", "admin:write"})
     * @IsValidOwner()
     */
    private $owner;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Article
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @Groups({"article:read", "article:list", "admin:read"})
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->content) < 40) {
            return $this->content;
        }

        return substr($this->content, 0, 40).'...';
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Article
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return Article
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return Article
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool|null $isPublished
     * @return Article
     */
    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param DateTimeInterface $updated_at
     * @return Article
     */
    public function setUpdatedAt(DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->published_at;
    }

    /**
     * @param DateTimeInterface|null $published_at
     * @return Article
     */
    public function setPublishedAt(?DateTimeInterface $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|null $owner
     * @return Article
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 */
class Profile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(max="255", maxMessage="About must contain maximum 255 characters.")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $about;

    /**
     * @Assert\NotBlank(message="Please, select the photo.")
     * @Assert\Image(
     *     mimeTypes={"image/png", "image/jpg", "image/jpeg"},
     *     mimeTypesMessage = "Please upload a valid PNG, JPG or JPEG",
     *     maxSize="3000k",
     *     maxSizeMessage="Max size of the photo 3000 k",
     *     minHeight="300",
     *     minHeightMessage="Min height 300 px",
     *     minWidth="300",
     *     minWidthMessage="Min width 300 px"
     * )
     * @ORM\Column(type="string")
     */
    private $picture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}

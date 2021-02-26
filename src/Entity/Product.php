<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Serializer\Groups({"list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Serializer\Groups({"list", "describe"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     *
     * @Serializer\Groups({"describe"})
     */
    private $description;

    /**
     * @ORM\Column(type="json")
     *
     * @Serializer\Groups({"describe"})
     */
    private $tech_specs;

    /**
     * @ORM\Column(type="float")
     *
     * @Serializer\Groups({"list", "describe"})
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTechSpecs(): ?array
    {
        return $this->tech_specs;
    }

    public function setTechSpecs(array $tech_specs): self
    {
        $this->tech_specs = $tech_specs;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}

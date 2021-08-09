<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserSettingsRepository::class)
 * @ORM\Table(name="`user_settings`")
 */
class UserSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=7)
     */
    private $userId;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    private $rentOrSale;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $keyword;

    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $area;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $rooms;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priceFrom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priceTo;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRentOrSale(): ?int
    {
        return $this->rentOrSale;
    }

    public function getRentOrSaleValue(): ?string
    {
        return RentOrSale::getType($this->rentOrSale);
    }

    public function setRentOrSale(int $rentOrSale): self
    {
        $this->rentOrSale = $rentOrSale;

        return $this;
    }

    public function getKeyword(): ?array
    {
        return $this->keyword;
    }

    public function getKeywordValue(): ?string
    {
        return implode(',', $this->keyword);
    }

    public function setKeyword(array $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getTypeValue(): ?string
    {
        return Type::getType($this->type);
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getArea(): ?array
    {
        return $this->area;
    }

    public function setArea(array $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getRooms(): ?array
    {
        return $this->rooms;
    }

    public function getRoomsValue(): ?string
    {
        return Room::getType($this->rooms);
    }

    public function setRooms(array $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getPriceFrom(): ?int
    {
        return $this->priceFrom;
    }

    public function setPriceFrom(int $priceFrom): self
    {
        $this->priceFrom = $priceFrom;

        return $this;
    }

    public function getPriceTo(): ?int
    {
        return $this->priceTo;
    }

    public function setPriceTo(int $priceTo): self
    {
        $this->priceTo = $priceTo;

        return $this;
    }
}

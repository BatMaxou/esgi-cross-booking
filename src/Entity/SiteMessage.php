<?php

namespace App\Entity;

use App\Entity\Trait\UuidTrait;
use App\Enum\SiteMessagePlaceEnum;
use App\Repository\SiteMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteMessageRepository::class)]
class SiteMessage
{
    use UuidTrait;

    #[ORM\Column(enumType: SiteMessagePlaceEnum::class)]
    private SiteMessagePlaceEnum $place;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    public function getPlace(): SiteMessagePlaceEnum
    {
        return $this->place;
    }

    public function setPlace(SiteMessagePlaceEnum $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}

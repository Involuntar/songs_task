<?php

namespace App\Entity;

use App\Repository\MusicRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicRepository::class)]
class Music
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $track_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $listening_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTrackId(): ?int
    {
        return $this->track_id;
    }

    public function setTrackId(int $track_id): static
    {
        $this->track_id = $track_id;

        return $this;
    }

    public function getListeningDate(): ?\DateTime
    {
        return $this->listening_date;
    }

    public function setListeningDate(\DateTime $listening_date): static
    {
        $this->listening_date = $listening_date;

        return $this;
    }
}

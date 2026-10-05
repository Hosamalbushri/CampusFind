<?php

namespace CampusFind\LostAndFound\DataTransferObjects;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

readonly class PublicLostReportData implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $reference,
        public string $title,
        public ?string $category,
        public ?string $lostLocation,
        public ?DateTimeInterface $lostAt,
        public ?string $description,
        public ?string $imageUrl = null,
        public bool $hasImage = false,
    ) {}

    public function toArray(): array
    {
        return [
            'reference' => $this->reference,
            'title' => $this->title,
            'category' => $this->category,
            'lost_location' => $this->lostLocation,
            'lost_at' => $this->lostAt?->format('Y-m-d H:i:s'),
            'description' => $this->description,
            'image_url' => $this->imageUrl,
            'has_image' => $this->hasImage,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

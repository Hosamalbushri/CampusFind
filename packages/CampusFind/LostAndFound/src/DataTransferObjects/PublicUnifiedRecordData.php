<?php

namespace CampusFind\LostAndFound\DataTransferObjects;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

readonly class PublicUnifiedRecordData implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $publicId,
        public string $type,
        public string $reference,
        public string $title,
        public string $status,
        public string $action,
        public bool $actionAvailable,
        public ?string $detailDestination = null,
        public ?string $category = null,
        public ?string $location = null,
        public ?DateTimeInterface $occurredAt = null,
        public ?string $description = null,
        public ?string $imageUrl = null,
        public bool $hasImage = false,
    ) {}

    /**
     * @return array{
     *     public_id: string,
     *     type: string,
     *     reference: string,
     *     title: string,
     *     status: string,
     *     action: string,
     *     action_available: bool,
     *     detail_destination: string|null,
     *     category: string|null,
     *     location: string|null,
     *     occurred_at: string|null,
     *     description: string|null,
     *     image_url: string|null,
     *     has_image: bool
     * }
     */
    public function toArray(): array
    {
        return [
            'public_id' => $this->publicId,
            'type' => $this->type,
            'reference' => $this->reference,
            'title' => $this->title,
            'status' => $this->status,
            'action' => $this->action,
            'action_available' => $this->actionAvailable,
            'detail_destination' => $this->detailDestination,
            'category' => $this->category,
            'location' => $this->location,
            'occurred_at' => $this->occurredAt?->format('Y-m-d H:i:s'),
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

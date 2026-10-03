<?php

namespace CampusFind\LostAndFound\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Models registered by this Concord module.
     *
     * @var array<int, string>
     */
    protected $models = [
        \CampusFind\LostAndFound\Models\LostFoundCategory::class,
        \CampusFind\LostAndFound\Models\FoundItem::class,
        \CampusFind\LostAndFound\Models\FoundItemImage::class,
        \CampusFind\LostAndFound\Models\LostReport::class,
        \CampusFind\LostAndFound\Models\LostFoundClaim::class,
    ];
}

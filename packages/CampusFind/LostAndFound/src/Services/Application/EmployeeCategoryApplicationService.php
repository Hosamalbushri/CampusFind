<?php

namespace CampusFind\LostAndFound\Services\Application;

use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Repositories\LostFoundCategoryRepository;
use Webkul\User\Models\User;

class EmployeeCategoryApplicationService
{
    public function __construct(
        protected LostFoundCategoryRepository $categoryRepository
    ) {}

    public function createCategory(User $actor, array $data): LostFoundCategory
    {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.settings.categories');

        return $this->categoryRepository->create($data);
    }

    public function updateCategory(User $actor, int $id, array $data): LostFoundCategory
    {
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.settings.categories');

        return $this->categoryRepository->update($data, $id);
    }
}

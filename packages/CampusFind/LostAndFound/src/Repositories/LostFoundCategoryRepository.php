<?php

namespace CampusFind\LostAndFound\Repositories;

use CampusFind\LostAndFound\Contracts\LostFoundCategory;
use LogicException;
use Webkul\Core\Eloquent\Repository;

class LostFoundCategoryRepository extends Repository
{
    public function model(): string
    {
        return LostFoundCategory::class;
    }

    public function update(array $data, $id, $attribute = 'id')
    {
        $category = $this->findOrFail($id);

        if (
            array_key_exists('code', $data)
            && $data['code'] !== $category->code
            && ($category->foundItems()->exists() || $category->lostReports()->exists())
        ) {
            throw new LogicException('An operational Lost & Found category code is immutable.');
        }

        return parent::update($data, $id, $attribute);
    }
}

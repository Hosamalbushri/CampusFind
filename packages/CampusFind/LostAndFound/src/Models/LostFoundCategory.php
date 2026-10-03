<?php

namespace CampusFind\LostAndFound\Models;

use CampusFind\LostAndFound\Contracts\LostFoundCategory as LostFoundCategoryContract;
use Illuminate\Database\Eloquent\Model;

class LostFoundCategory extends Model implements LostFoundCategoryContract
{
    protected $table = 'lost_found_categories';

    protected $fillable = [
        'code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function foundItems()
    {
        return $this->hasMany(FoundItemProxy::modelClass(), 'category_id');
    }

    public function lostReports()
    {
        return $this->hasMany(LostReportProxy::modelClass(), 'category_id');
    }
}

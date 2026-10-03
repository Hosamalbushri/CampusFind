<?php

namespace CampusFind\Student\Providers;

use CampusFind\Student\Models\Student;
use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Student::class,
    ];
}

<?php

namespace Visiosoft\Kanban\Resources\IssueResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Visiosoft\Kanban\Resources\IssueResource;

class CreateIssue extends CreateRecord
{
    protected static string $resource = IssueResource::class;
}

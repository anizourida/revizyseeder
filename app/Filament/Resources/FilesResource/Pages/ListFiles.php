<?php

namespace App\Filament\Resources\FilesResource\Pages;

use App\Filament\Resources\FilesResource;
use Filament\Resources\Pages\ListRecords;

class ListFiles extends ListRecords
{
    protected static string $resource = FilesResource::class;

    protected static string $view = 'filament.resources.files-resource.pages.list-files';

    public function mount(): void
    {
        parent::mount();

        if ($this->tableRecordsPerPage === 'all') {
            $this->tableRecordsPerPage = 50;
            session()->put($this->getTablePerPageSessionKey(), 50);
        }
    }
}

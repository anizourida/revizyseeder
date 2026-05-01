<?php

namespace App\Filament\Resources\LivretResource\Pages;

use App\Filament\Resources\LivretResource;
use App\Models\Raiida\Page;
use Filament\Resources\Pages\ListRecords;

class ListLivrets extends ListRecords
{
    protected static string $resource = LivretResource::class;

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public function getTableRecords(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Contracts\Pagination\CursorPaginator
    {
        $gradeId = $this->tableFilters['grade_id']['value'] ?? 1;
        $hasOcrText = $this->tableFilters['has_ocr_text']['value'] ?? null;

        $query = Page::query()
            ->where('grade_id', $gradeId)
            ->whereNotNull('page_number')
            ->where('page_number', '!=', '');

        $isTrue = in_array($hasOcrText, [true, 1, '1', 'true'], true);
        $isFalse = in_array($hasOcrText, [false, 0, '0', 'false'], true);

        if ($isTrue) {
            $query->where(function ($q): void {
                $q->whereNotNull('ocr_olmocr_path')
                    ->where('ocr_olmocr_path', '!=', '')
                    ->orWhere(function ($q2): void {
                        $q2->whereNotNull('ocr_chandra_path')
                            ->where('ocr_chandra_path', '!=', '');
                    });
            });
        } elseif ($isFalse) {
            $query->where(function ($q): void {
                $q->whereNull('ocr_olmocr_path')
                    ->orWhere('ocr_olmocr_path', '');
            })->where(function ($q): void {
                $q->whereNull('ocr_chandra_path')
                    ->orWhere('ocr_chandra_path', '');
            });
        }

        $rows = $query
            ->orderByRaw('CAST(page_number AS INTEGER) ASC')
            ->orderByDesc('updated_at')
            ->get()
            ->unique(static fn (Page $page): string => trim((string) $page->page_number))
            ->sortBy(static fn (Page $page): int => (int) $page->page_number)
            ->values();

        return new \Illuminate\Database\Eloquent\Collection($rows->all());
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

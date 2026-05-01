<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\Grade;
use App\Models\Raiida\FileAsset;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = FileAsset::query()
            ->select([
                'file_assets.id',
                'file_assets.filename',
                'file_assets.size_bytes',
                'file_assets.is_downloaded',
                'file_assets.is_corrupt',
                'file_assets.session_id',
                'file_assets.is_vocab_extracted',
                'file_assets.vocab_count',
                'grades.name as grade_name',
                'subjects.name as subject_name',
                'periods.name as period_name',
                'weeks.name as week_name',
            ])
            ->join('weeks', 'file_assets.week_id', '=', 'weeks.id')
            ->join('periods', 'weeks.period_id', '=', 'periods.id')
            ->join('subjects', 'periods.subject_id', '=', 'subjects.id')
            ->join('grades', 'subjects.grade_id', '=', 'grades.id')
            ->where('subjects.name', 'FR')
            ->whereIn('grades.name', $this->allowedGradeNames())
            ->orderBy('file_assets.id')
            ->get();

        $payload = $rows->map(static function (FileAsset $row): array {
            return [
                'id' => $row->id,
                'filename' => $row->filename,
                'size' => (int) $row->size_bytes,
                'is_downloaded' => (bool) $row->is_downloaded,
                'is_corrupt' => (bool) $row->is_corrupt,
                'grade' => $row->grade_name,
                'subject' => $row->subject_name,
                'period' => $row->period_name,
                'week' => $row->week_name,
                'session' => $row->session_id,
                'is_vocab_extracted' => (bool) $row->is_vocab_extracted,
                'vocab_count' => (int) $row->vocab_count,
            ];
        })->values();

        return response()->json($payload);
    }

    public function tree(): JsonResponse
    {
        $grades = Grade::query()
            ->whereIn('name', $this->allowedGradeNames())
            ->with([
                'subjects.periods.weeks.files' => function ($query): void {
                    $query->orderBy('id');
                },
            ])
            ->orderBy('id')
            ->get();

        $tree = $grades->map(function (Grade $grade): array {
            $subjects = $grade->subjects
                ->filter(fn ($subject) => $subject->name === 'FR')
                ->map(function ($subject): array {
                    $periods = $subject->periods->map(function ($period): array {
                        $weeks = $period->weeks->map(function ($week): array {
                            $files = $week->files->map(static function ($file): array {
                                return [
                                    'id' => $file->id,
                                    'name' => $file->filename,
                                    'type' => 'file',
                                    'is_downloaded' => (bool) $file->is_downloaded,
                                    'is_corrupt' => (bool) $file->is_corrupt,
                                    'size' => (int) $file->size_bytes,
                                    'original_url' => $file->original_url,
                                    'local_path' => $file->local_path,
                                ];
                            })->values()->all();

                            return [
                                'id' => $week->id,
                                'name' => $week->name,
                                'type' => 'week',
                                'children' => $files,
                            ];
                        })->values()->all();

                        return [
                            'id' => $period->id,
                            'name' => $period->name,
                            'type' => 'period',
                            'children' => $weeks,
                        ];
                    })->values()->all();

                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'type' => 'subject',
                        'children' => $periods,
                    ];
                })
                ->values()
                ->all();

            return [
                'id' => $grade->id,
                'name' => $grade->name,
                'type' => 'grade',
                'children' => $subjects,
            ];
        })->filter(fn (array $gradeNode) => $gradeNode['children'] !== [])
            ->values();

        return response()->json($tree);
    }

    private function allowedGradeNames(): array
    {
        return ['1', '2', '3', '4', '5', '6'];
    }
}

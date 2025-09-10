<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Find;
use App\Models\Publication;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RemoveDupes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-dupes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate entries from database';

    /**
     * Remove duplicate records from any model, keeping the first occurrence
     *
     * @param string $modelClass The fully qualified model class name
     * @param array $columns The columns to check for duplicates
     * @param string $primaryKey The primary key column name (default: 'id')
     * @return array Result statistics
     */
    public static function removeDupes(
        string $modelClass,
        array $columns,
        string $primaryKey = 'id'
    ): array {
        // Validate model class
        if (!class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("{$modelClass} is not a valid Eloquent model");
        }

        // Validate columns
        $instance = new $modelClass;
        $table = $instance->getTable();
        $existingColumns = DB::getSchemaBuilder()->getColumnListing($table);

        // Check if all specified columns exist
        $allColumns = array_merge($columns, [$primaryKey]);
        $invalidColumns = array_diff($allColumns, $existingColumns);

        if (!empty($invalidColumns)) {
            throw new \InvalidArgumentException("Invalid columns: " . implode(', ', $invalidColumns));
        }

        // Count before
        $totalBefore = $modelClass::count();

        // Get the IDs of records to keep (first record of each group)
        $recordsToKeep = $modelClass::select(DB::raw("MIN({$primaryKey}) as keep_id"))
            ->groupBy($columns)
            ->pluck('keep_id');

        // Delete all records that are not in the "keep" list
        $deletedCount = $modelClass::whereNotIn($primaryKey, $recordsToKeep)->delete();

        // Count after
        $totalAfter = $modelClass::count();

        return [
            'deleted_count' => $deletedCount,
            'total_before' => $totalBefore,
            'total_after' => $totalAfter,
            'columns_checked' => $columns,
            'model' => $modelClass
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->removeDupes(Find::class, ['find_type_id', 'title', 'url', 'date']);
        $this->removeDupes(Event::class, ['name', 'schedule', 'start_date', 'start_time', 'end_date', 'end_time', 'url']);
        $this->removeDupes(Publication::class, ['publication_type_id', 'year', 'title', 'url']);
    }
}

<?php

/**
 * Seed pivot table
 *
 * @param string  $pivotTable           Pivot table name
 * @param string  $firstTable           First table name
 * @param string  $secondTable          Second table name
 * @param Closure $customColumnCallback Should return array of additional columns and their values to be inserted in
 *                                      pivot table
 * @param bool    $timestamps           Insert timestamps
 *
 * @return bool
 */
function seedPivotData($pivotTable, $firstTable, $secondTable, Closure $customColumnCallback = null, $timestamps = true)
{
    $firstIds = DB::table($firstTable)->inRandomOrder()->pluck('id')->toArray();
    $secondCount = DB::table($secondTable)->count();
    $data = [];

    foreach ($firstIds as $firstId) {
        $secondIds = [];
        for ($i = 0; $i <= rand(1, ceil($secondCount / 3)); $i++) {
            if (!$secondIds) {
                $secondIds = DB::table($secondTable)->inRandomOrder()->pluck('id')->toArray();
            }

            // TODO
            // Handle table that have no id column

            $row = [
                str_singular($firstTable) . '_id' => $firstId,
                str_singular($secondTable) . '_id' => ($secondId = array_pop($secondIds))
            ];

            // Add column => value from callback return array
            if (is_callable($customColumnCallback)) {
                $row = array_merge($row, $customColumnCallback($firstId, $secondId));
            }

            // Add timestamps
            if ($timestamps) {
                $now = \Carbon\Carbon::now();
                $row = array_merge($row, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $data[] = $row;
        }
    }

    return DB::table($pivotTable)->insert($data);
}

/**
 * Write to Laravel log.
 * Helper shortcut for laravel log method.
 *
 * @param        $var
 * @param string $level
 */
function ll($var, $level = 'debug')
{
    if (is_object($var)) {
        $var = (array) $var;
    } elseif (is_bool($var)) {
        $var = $var ? '~TRUE~' : '~FALSE~';
    } elseif (is_null($var)) {
        $var = '~NULL~';
    }

    \Log::$level($var);
}

/**
 * Chance to $win or $loose variables/callbacks
 *
 * @param int           $percent Percent of chance to win
 * @param Closure|mixed $win     Callback to call or value to return if turn win
 * @param mixed         $loose   Callback to call or value to return if turn loose
 *
 * @return mixed
 */
function chance($percent = 50, $win = true, $loose = false)
{
    if (!is_numeric($percent)) {
        throw new \InvalidArgumentException('Percent needs to be a number or numeric string');
    }

    return (rand(1, 100) <= $percent)
        // Win
        ? (is_callable($win) ? $win() : $win)
        // Loose
        : (is_callable($loose) ? $loose() : $loose);
}

/**
 * Generate valid RFC 4211 compliant Universally Unique Identifiers (UUID)
 * version 3, 4 and 5.
 *
 * @return string
 */
function uuid()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Get list of all image resolutions used in application
 *
 * @return array
 */
function getImageResolutions()
{
    $files = scandir(app_path('Lib/ImageVariations'));
    $allSizes = [];

    // Get image variations base class and namespace
    $baseClass = \App\Lib\ImageVariations\ImageVariationsBase::class;
    $namespace = explode("\\", $baseClass);
    array_pop($namespace);
    $namespace = implode("\\", $namespace);

    // Go through all ImageVariationsBase children and run getSizes method
    // to get all project image sizes
    for ($i = 0; $i < count($files); $i++) {
        if (substr($files[$i], -4) == '.php') {
            $className = rtrim($files[$i], '.php');
            $fullClassName = $namespace . '\\' . $className;

            if (is_subclass_of($fullClassName, $baseClass)) {
                foreach ($fullClassName::getSizes() as $size) {
                    $allSizes[] = $size;
                }
            }
        }
    }

    $uniqueSizes = array_unique($allSizes);

    return $uniqueSizes;
}
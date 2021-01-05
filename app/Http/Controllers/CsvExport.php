<?php
declare(strict_types=1);

namespace App\Http\Controllers;

class CsvExport extends Controller {
    /**
     * Converts the user input into a CSV file and streams the file back to the user
     */
    public function convert(\Illuminate\Http\Request $request)
    {
        $callback = function() use ($request) {
            $file = fopen('php://output', 'w');
            fputcsv($file, count($request->all()) ? array_keys(array_merge(...$request->all())) : []);

            foreach ($request->all() as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=test.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}

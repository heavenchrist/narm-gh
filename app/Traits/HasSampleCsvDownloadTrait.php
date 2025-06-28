<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasSampleCsvDownloadTrait
{

    /**
     * generateSampleCsv
     *
     * @param  array $headers
     * @param  string $filename
     * @return string
     */
    public function generateSampleCsv($headers,$filename): string
    {

        $extension='.csv';

        $sampleRow = ['123', 'John Doe', 'Finance', 'Dar', 'Central', '50000', '2025-05'];

       $filename .= $extension;
        $path = "public/sample_csvs/".$filename;

        // ✅ Check if file already exists
        if (Storage::exists($path)) {
            return Storage::url('sample_csvs/' . $filename);
        }
// ❌ File does not exist, create it
        // Open a stream to a temporary in-memory file
        $temp = fopen('php://temp', 'r+');
        fputcsv($temp, $headers);
        //fputcsv($temp, $sampleRow);
        rewind($temp);

        // Write the content to storage
        Storage::put($path, stream_get_contents($temp));
        fclose($temp);

        // Return a public URL
        return Storage::url('sample_csvs/' . $filename);
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataExtractionController extends Controller
{
    public function extractData()
    {
        $url = 'https://www.codal.ir/Reports/InterimStatement.aspx?LetterSerial=EPrfVlRNU8rYEIYM1740RA%3d%3d';

        $htmlContent = @file_get_contents($url);

        if ($htmlContent === FALSE) {
            Log::error("Failed to fetch content from URL: " . $url);
            return response()->json(['error' => 'Could not fetch content from the provided URL.'], 500);
        }

        $datasourceValue = null;
        $scriptTags = explode('<script', $htmlContent);

        foreach ($scriptTags as $scriptTag) {
            if (strpos($scriptTag, 'var datasource =') !== false) {
                $lines = explode("\n", $scriptTag);
                foreach ($lines as $line) {
                    if (strpos(trim($line), 'var datasource =') === 0) {
                        $valuePart = substr($line, strpos($line, 'var datasource =') + strlen('var datasource ='));
                        $potentialJson = trim(rtrim($valuePart, ';'));

                        // --- بخش جدید: تبدیل HTML entities به کاراکترهای فارسی ---
                        $decodedJsonString = preg_replace_callback(
                            '/&\#(\d+);/',
                            function ($matches) {
                                // $matches[1] حاوی عدد بین &# و ; است
                                return html_entity_decode("&#" . $matches[1] . ";");
                            },
                            $potentialJson
                        );
                        // -----------------------------------------------------------

                        // حالا سعی در decode کردن JSON تبدیل شده
                        if ((str_starts_with($decodedJsonString, '[') && str_ends_with($decodedJsonString, ']')) || (str_starts_with($decodedJsonString, '{') && str_ends_with($decodedJsonString, '}'))) {
                            $datasourceValue = json_decode($decodedJsonString, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                break 2;
                            } else {
                                Log::warning('JSON decoding failed after HTML entity conversion for datasource. Original: ' . $potentialJson . ' Converted: ' . $decodedJsonString . ' Error: ' . json_last_error_msg());
                            }
                        } else {
                            Log::warning('Datasource value after conversion does not look like JSON: ' . $decodedJsonString);
                            $datasourceValue = $decodedJsonString;
                            break 2;
                        }
                    }
                }
            }
        }

        if ($datasourceValue !== null) {
            Log::info('Successfully extracted datasource: ', $datasourceValue);
            return response()->json(['data' => $datasourceValue]);
        } else {
            Log::error("Could not find or parse 'var datasource =' in the page source after conversion.");
            return response()->json(['error' => "Could not find or parse 'var datasource =' in the page source after conversion."], 404);
        }
    }
}

<?php

namespace App\Modules\Codal;

class Extractor
{
    public function extractMetadata(
        string $html,
        string $symbolOverride = ''
    ): ?array
    {
        $datasource = $this->extractDatasource($html);

        if ($datasource === null) {
            return null;
        }

        $periodEndDate = trim(
            (string)($datasource['periodEndToDate'] ?? '')
        );

        if (!preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $periodEndDate)) {
            return null;
        }

        $symbol = trim($symbolOverride);

        if ($symbol === '') {
            $dom = $this->createDom($html);
            $xpath = new DOMXPath($dom);

            $symbol = trim(
                $xpath->evaluate(
                    'string(//*[@id="ctl00_lblDisplaySymbol"])'
                )
            );
        }

        if ($symbol === '') {
            return null;
        }

        return [
            'rawSymbol' => $symbol,
            'periodEndDate' => $periodEndDate,
        ];
    }

    /**
     * Extract datasource JavaScript object.
     */
    public function extractDatasource(string $html): ?array
    {
        $patterns = [
            '/\b(?:var|let|const)\s+datasource\s*=/',
            '/\bwindow\.datasource\s*=/',
            '/\bdatasource\s*=/',
        ];

        foreach ($patterns as $pattern) {
            if (!preg_match(
                $pattern,
                $html,
                $matches,
                PREG_OFFSET_CAPTURE
            )) {
                continue;
            }

            $offset = $matches[0][1] + strlen($matches[0][0]);

            $json = $this->extractBalancedObject(
                $html,
                $offset
            );

            if ($json === null) {
                continue;
            }

            $decoded = json_decode($json, true);

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Extract a balanced JavaScript object.
     */
    private function extractBalancedObject(
        string $content,
        int    $offset
    ): ?string
    {
        $start = strpos($content, '{', $offset);

        if ($start === false) {
            return null;
        }

        $length = strlen($content);

        $depth = 0;
        $quote = null;
        $escaped = false;

        for ($i = $start; $i < $length; $i++) {
            $char = $content[$i];

            if ($quote !== null) {
                if ($escaped) {
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }

                if ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if (
                $char === '"'
                || $char === "'"
                || $char === '`'
            ) {
                $quote = $char;
                continue;
            }

            if ($char === '{') {
                $depth++;
            }

            if ($char === '}') {
                $depth--;

                if ($depth === 0) {
                    return substr(
                        $content,
                        $start,
                        $i - $start + 1
                    );
                }
            }
        }

        return null;
    }

    /**
     * Create DOMDocument safely.
     */
    private function createDom(string $html): DOMDocument
    {
        $previousState = libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $html,
            LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
        );

        libxml_clear_errors();

        libxml_use_internal_errors($previousState);

        return $dom;
    }
}

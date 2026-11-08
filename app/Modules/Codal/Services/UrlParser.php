<?php

namespace App\Modules\Codal\Services;

use App\Modules\Codal\Exceptions\InvalidCodalUrlException;
use function App\Modules\Codal\str_ends_with;

class UrlParser
{
    private string $letterSerial;
    private array $queryParams;

    public function __construct(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);

        if (
            !isset($parts['host'])
            || !in_array(
                strtolower($parts['host']),
                ['codal.ir', 'www.codal.ir'],
                true
            )
        ) {
            return null;
        }

        if (
            !isset($parts['path'])
            || !str_ends_with($parts['path'], '/Reports/Decision.aspx')
        ) {
            return null;
        }

        parse_str($parts['query'] ?? '', $queryParams);

        $letterSerial = $queryParams['LetterSerial'] ?? null;

        unset($queryParams['sheetId']);

        if ($letterSerial === null) {
            throw new InvalidCodalUrlException("Invalid Codal URL or missing LetterSerial.");
        }

        $this->letterSerial = $letterSerial;

        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return mixed|string
     */
    public function getLetterSerial(): mixed
    {
        return $this->letterSerial;
    }

}

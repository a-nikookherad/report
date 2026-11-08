<?php

namespace App\Modules\Codal\Services;

use App\Modules\Codal\Exceptions\InvalidSheetIdException;

class SheetIdResolver
{
    /**
     * Only the four required financial statements.
     */
    public const STATEMENTS = [
        '0' => 'balance_sheet',
        '1' => 'income_statements',
        '9' => 'cashflow',
        '1060' => 'change_in_property_right',
    ];

    public function resolve(?string $sheetId): ?array
    {
        if ($sheetId === null || $sheetId === '') {
            return array_keys(self::STATEMENTS);
        }

        $sheetId = trim($sheetId);

        if (!array_key_exists($sheetId, self::STATEMENTS)) {
            throw new InvalidSheetIdException('Invalid sheetId. Allowed values: '
                . implode(', ', array_keys(self::STATEMENTS)));
        }


        return [$sheetId];
    }
}

<?php

namespace App\Modules\Codal\Services;


use App\Modules\Codal\Exceptions\CannotDetectExtractorException;
use App\Modules\Codal\Extractors\Activities\ActivityDataExtractor;
use App\Modules\Codal\Extractors\BalanceSheets\BalanceSheetDataExtractor;
use App\Modules\Codal\Extractors\CashFlows\CashFlowDataExtractor;
use App\Modules\Codal\Extractors\Equities\EquityDataExtractor;
use App\Modules\Codal\Extractors\IncomeStatements\IncomeStatementDataExtractor;
use function app;

class FinancialStatementDetector
{
    public function detect(string $englishTitle, array $dataSource)
    {
        return match ($englishTitle) {
            'Balance Sheet' => app(BalanceSheetDataExtractor::class, [$dataSource]),
            'Income Statement' => app(IncomeStatementDataExtractor::class, [$dataSource]),
            'Cash Flow' => app(CashFlowDataExtractor::class, [$dataSource]),
            'Changes In Property Rights' => app(EquityDataExtractor::class, [$dataSource]),
            'Activities' => app(ActivityDataExtractor::class, [$dataSource]),
            default => throw new CannotDetectExtractorException(
                "Invalid report type: {$englishTitle}"
            ),
        };
    }
}

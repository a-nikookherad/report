<?php

namespace App\Services\DataExtractors;

use App\Models\Bourse\FinancialPeriod;
use App\Tools\PersianDate\PersianDate;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Support\Collection;
use League\Container\Exception\NotFoundException;

abstract class NormalizeAbstract
{
    protected array $dataSource;
    private collection $cells;
    private int $rowSequence;
    protected array $response = [];

    public function filterTableByName(string $tableNameEn = null, string $tableNameFa = null): self
    {
//        $tables=[];
        foreach ($this->dataSource["sheets"][0]["tables"] as $table) {
//            unset($table["cells"]);
//            $tables[]=$table;
            if (($table["title_Fa"] == $tableNameFa || $table["title_En"] == $tableNameEn)) {
                $this->cells = collect($table["cells"]);
            }
        }
//        dd($tables);
        return $this;
    }

    public function searchRowSequenceByValue(string $value): self
    {
        $rowSequence = $this->cells->where("value", $value)->first();
        $this->rowSequence = $rowSequence["rowSequence"] ?? 0;
        return $this;
    }

    public function getCelByColumnSequence($column): array|null
    {
//        dd($this->cells
//        ->where("rowSequence",$this->rowSequence));
        return $this->cells
            ->where("rowSequence", $this->rowSequence)
            ->where("columnSequence", $column)
            ->where("yearEndToDate", $this->dataSource["yearEndToDate"])
            ->where("periodEndToDate", $this->dataSource["periodEndToDate"])
            ->first();
    }

    protected function getPublicData(): void
    {
        $this->response["period_end_date"] = $this->dataSource["periodEndToDate"];
        $this->response["year_end_date"] = $this->dataSource["yearEndToDate"];
        $this->response["financial_period_id"] = $this->getFinancialPeriodId();
    }

    private function getFinancialPeriodId()
    {
        $start_date = Verta::parse($this->dataSource["yearEndToDate"])->subDays(364)->format("Y-m-d");
        $end_date = Verta::parse($this->dataSource["yearEndToDate"])->format("Y-m-d");
        $financialPeriod = FinancialPeriod::query()
            ->where("solar_start_date", ">=", $start_date)
            ->where("solar_end_date", "<=", $end_date)
            ->firstOr(function () use ($start_date, $end_date) {
                return FinancialPeriod::query()
                    ->create([
                        "solar_start_date" => $start_date,
                        "solar_end_date" => $end_date,
                        "start_date" => PersianDate::solarToGregorian($start_date),
                        "end_date" => PersianDate::solarToGregorian($end_date)
                    ]);
            });
        return $financialPeriod?->id;
    }
}

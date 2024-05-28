<?php

namespace App\Logics\Bourse\Normalize;

class ActivityDataNormalizeLogic extends NormalizeAbstract
{

    public function prepareData($data): ActivityDataNormalizeLogic
    {
        // TODO: Implement prepareData() method.

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function normalize(): array
    {

        return $this->data;
    }
}

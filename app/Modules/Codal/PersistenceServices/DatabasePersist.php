<?php

namespace App\Modules\Codal\PersistenceServices;

class DatabasePersist implements PersistenceInterface
{
    public function persist(StorableInterface $storableDTO)
    {
        $modelClass = $storableDTO->getModel();

        $model = new $modelClass();

        $model->fill($storableDTO->toArray());

        $model->save();

        return $model;
    }
}

<?php

namespace App\Modules\Codal\PersistenceServices;

interface PersistenceInterface
{
    public function persist(StorableInterface $storableDTO);
}

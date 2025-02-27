<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerExcelImport implements ToModel
{
/**
     * @param array $row
     *
     * @return Customer|null
     */
    public function model(array $row)
    {
        return new Customer([
           'id_customer' => $row[0],
           'name' => $row[1],
        ]);
    }
}

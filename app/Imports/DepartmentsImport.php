<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;

class DepartmentsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Department([
            'name'        => $row['0'],
            'description' => $row['1']
        ]);
    }
}

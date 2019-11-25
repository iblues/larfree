<?php
/**
 * Created by PhpStorm.
 * User: lanyang
 * Date: 2019/11/22
 * Time: 4:49 PM
 */

namespace Larfree\Exports;

use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LarfreeExport implements FromCollection, WithHeadings, WithMapping
{

    public $data;
    public $schema;

    public function __construct($data, $schema)
    {
        $this->data = $data;
        $this->schema = $schema;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return Arr::pluck($this->schema['fields'],'name');
    }

    public function map($data): array
    {
        $row=[];
        foreach ($this->schema['fields'] as $field){
            $row[] = $data[$field['key']];
        }
        return $row;
    }


}

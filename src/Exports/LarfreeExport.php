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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LarfreeExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{

    public $data;
    public $schema;

    public function __construct($data, $schema)
    {
//        Font::setTrueTypeFontPath(storage_path() .'/fonts/');
//        Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_EXACT);
        $this->data   = $data;
        $this->schema = $schema;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return Arr::pluck($this->schema['fields'], 'name');
    }

    public function map($data): array
    {
        $row = [];
        foreach ($this->schema['fields'] as $field) {
            $row[] = Arr::get($data,$field['key']);
        }
        return $row;
    }


}

<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class LeadImport implements ToModel, WithHeadingRow, OnEachRow
{
    protected $errors = [];
    protected $failedRecords = [];
    protected $successRecords = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // We're not using this method for insertion; it will be handled in onRow().
        return null;
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $rowData = $row->toArray();

        // Validation
        $validator = Validator::make($rowData, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'required|regex:/^\+?[0-9]{10,15}$/',
        ]);

        // If validation fails, print the error and continue
        if ($validator->fails()) {
            $this->errors[] = "Error in row {$rowIndex}: " . implode(', ', $validator->errors()->all());
            $this->failedRecords[] = "Row {$rowIndex}";
            return;
        }

        // If validation passes, create lead details
        Lead::create([
            'user_id' => auth()->id(),
            'name' => $rowData['name'],
            'email' => $rowData['email'],
            'phone' => $rowData['phone'],
        ]);
        $this->successRecords[] = "Row {$rowIndex}";
    }

    public function getErrors()
    {
        return [
            'errors' => $this->errors,
            'success_records' => $this->successRecords,
            'failed_records' => $this->failedRecords,
            'success_records_count' => count($this->successRecords),
            'failed_records_count' => count($this->failedRecords),
        ];
    }
}

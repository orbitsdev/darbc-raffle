<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Check if a record with the same DARBC ID exists
        DB::beginTransaction();

        try {
            // Check for duplicates based on DARBC ID
            if (Member::where('darbc_id', $row['darbc_id'])->exists()) {
                
                DB::rollBack(); // Roll back if duplicate is found
                return null; 
            }

            // Create the new member record
            $member = Member::create([
                'first_name' => $row['first_name'] ?? null,
                'middle_name' => $row['middle_name'] ?? null,
                'last_name' => $row['last_name'] ?? null,
                'darbc_id' => $row['darbc_id'], // Ensure unique DARBC ID
            ]);

            DB::commit(); // Commit after successful creation

            return $member;
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back on error
            throw $e; // Rethrow the exception for logging/debugging
        }
    }

    public function chunkSize(): int
    {
        return 1000; 
    }
}

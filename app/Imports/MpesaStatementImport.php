<?php

namespace App\Imports;

use App\Jobs\ProcessTransaction;
use App\Models\PendingMpesaDonation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MpesaStatementImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function __construct()
    {
        //
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $transaction = PendingMpesaDonation::where('trans_id', $row['Receipt No.'])->where('account_no', $row['A/C No.'])->first();
            if (!$transaction){
                Log::info($row['Receipt No.']);
                return
                // forward to transaction check url
                $other_party_info = explode('-', $row['Other Party Info']);
                $msisdn = trim($other_party_info[0]);
                $names = trim($other_party_info[1]);
                $content = array(
                    'content' => array(
                        'TransID' => $row['Receipt No.'],
                        'TransTime' => $row['Completion Time'],
                        'TransAmount' => $row['Paid In'],
                        'MSISDN' => $msisdn,
                        'FirstName' => explode(' ', $names)[0],
                        'LastName' => explode(' ', $names)[1],
                        'BillRefNumber' => $row['A/C No.'],
                        'BusinessShortCode' => env('BUSINESS_SHORTCODE')
                    )
                );
                ProcessTransaction::dispatch([
                    'channel' => 'mpesa',
                    'content' => $content,
                    'ip' => request()->ip()
                ])->onQueue('process-transaction')->onConnection('beanstalkd-worker001')->delay(now()->addSeconds(3));

                // TODO delete the file from server
            }
        }
    }

    public function headingRow(): int
    {
        return 7; // set the heading row to row 4
    }
    public function rules(): array
    {
        return [
            'Receipt No.' => 'required',
            'Completion Time' => 'required',
        ];
    }
    public function chunkSize(): int
    {
        return 500;
    }
}

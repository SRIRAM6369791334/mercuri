<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadTemplateService
{
    /**
     * Download sample template as XLSX or CSV.
     */
    public function downloadTemplate(string $type = 'general', string $format = 'xlsx'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(ucfirst($type) . ' Leads Template');

        if ($type === 'expo') {
            $headers = [
                'Name',
                'Company Name',
                'Phone Number',
                'Email',
                'Expo Event',
                'Product',
                'Deal Value',
                'State',
                'City',
                'Address',
                'Remarks',
                'Date'
            ];
            $sampleRows = [
                [
                    'S Ravi Kumar',
                    'Videl Gardens',
                    '9447124279',
                    'ravi@example.com',
                    'Prompt Expo',
                    '1',
                    '0',
                    'TAMIL NADU',
                    'COIMBATORE',
                    'S.M Palayam',
                    'Visited stall, enquiry for Goods Lift',
                    date('d-m-Y')
                ],
                [
                    'R Yuvaraj',
                    'Yuva Builders',
                    '9865134047',
                    '',
                    'Build Intec',
                    '1',
                    '0',
                    'TAMIL NADU',
                    'COIMBATORE',
                    'Peelamedu',
                    'Contractor requirements',
                    date('d-m-Y')
                ],
            ];
        } else {
            $headers = [
                'Name',
                'Company Name',
                'Phone Number',
                'Email',
                'Lead Status',
                'Lead Source',
                'Product',
                'Deal Value',
                'State',
                'City',
                'Address',
                'Remarks',
                'Date'
            ];
            $sampleRows = [
                [
                    'Aravind Kumar',
                    'Aravind Enterprises',
                    '9876543210',
                    'aravind@example.com',
                    'WARM',
                    'Direct',
                    '1',
                    '25000',
                    'TAMIL NADU',
                    'COIMBATORE',
                    '123 Cross Cut Road',
                    'Enquired via phone, immediate requirement',
                    date('d-m-Y')
                ],
                [
                    'Suresh Babu',
                    'Metro Logistics',
                    '9843210987',
                    'suresh@metro.com',
                    'HOT',
                    'Indiamart',
                    'Hydraulic Hand Pallet Truck',
                    '50000',
                    'TAMIL NADU',
                    'CHENNAI',
                    'Guindy Industrial Estate',
                    'Requires 2 units pallet truck',
                    date('d-m-Y')
                ],
            ];
        }

        // Set Headers
        $colIndex = 1;
        foreach ($headers as $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$colLetter}1", $header);
            $colIndex++;
        }

        // Set Sample Data Rows
        $rowIndex = 2;
        foreach ($sampleRows as $row) {
            $colIndex = 1;
            foreach ($row as $val) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                // Force phone number as text cell to preserve digits
                if ($headers[$colIndex - 1] === 'Phone Number') {
                    $sheet->getCell("{$colLetter}{$rowIndex}")->setValueExplicit($val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue("{$colLetter}{$rowIndex}", $val);
                }
                $colIndex++;
            }
            $rowIndex++;
        }

        $highestCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        // Styling for XLSX
        if ($format === 'xlsx') {
            // Header style: dark navy header with white bold text
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ];
            $sheet->getStyle("A1:{$highestCol}1")->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(28);

            // Auto-size columns
            for ($c = 1; $c <= count($headers); $c++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $filename = "sample_{$type}_leads_template." . $format;

        return response()->streamDownload(function () use ($spreadsheet, $format) {
            if ($format === 'csv') {
                $writer = new Csv($spreadsheet);
            } else {
                $writer = new Xlsx($spreadsheet);
            }
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

<?php
// Ensure Composer's autoloader is loaded
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExcelExportService {

    /**
     * Exports an array of data to an Excel file and streams it to the browser.
     *
     * @param array $data An array of associative arrays, where each associative array represents a row.
     *                    The keys of the first associative array will be used as header row.
     * @param string $filename The desired filename for the downloaded Excel file (e.g., "export.xlsx").
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function export(array $data, string $filename = 'export.xlsx'): void {
        if (empty($data)) {
            // Or handle this case as you see fit, maybe throw an exception or return an error message.
            // For now, we'll just exit if there's no data.
            error_log('ExcelExportService: No data provided for export.');
            // You might want to redirect back with an error message
            // header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=no_data_to_export');
            // exit;
            // For simplicity in this example, we'll create an empty file with headers if data is empty
            // but ideally, you should prevent calling this function with empty data.
            if (isset($_SESSION['last_headers']) && !empty($_SESSION['last_headers'])) {
                $data = [array_fill_keys($_SESSION['last_headers'], '')]; // Create one empty row with previous headers
            } else {
                 // Fallback if no headers were ever set, though this shouldn't happen in normal flow
                $data = [['No data to export']];
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = array_keys(reset($data)); // Get keys from the first row as headers
        $_SESSION['last_headers'] = $headers; // Store headers for potential empty data export
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Set data rows
        $rowNumber = 2;
        foreach ($data as $row) {
            $column = 'A';
            foreach ($row as $cellValue) {
                $sheet->setCellValue($column . $rowNumber, $cellValue);
                $column++;
            }
            $rowNumber++;
        }

        // Auto-size columns
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        // Left-align all cells for readability
        $dimension = $sheet->calculateWorksheetDimension();
        $sheet->getStyle($dimension)
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Stream the file to the browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

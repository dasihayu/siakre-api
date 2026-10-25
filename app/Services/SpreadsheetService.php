<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use ZipArchive;

class SpreadsheetService
{
    /**
     * Membaca file spreadsheet (.xlsx atau .csv) dan mengembalikan array of associative rows.
     *
     * @param  UploadedFile|string  $file
     * @return array<int, array<string, mixed>>
     *
     * @throws Exception
     */
    public function parse(UploadedFile|string $file): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $extension = strtolower(
            $file instanceof UploadedFile
                ? $file->getClientOriginalExtension()
                : pathinfo($filePath, PATHINFO_EXTENSION)
        );

        if ($extension === 'xlsx') {
            return $this->parseXlsx($filePath);
        }

        return $this->parseCsv($filePath);
    }

    /**
     * Mem-parsing file XLSX menggunakan native ZipArchive dan SimpleXML.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception('Gagal membuka file Excel (.xlsx). Pastikan file tidak rusak.');
        }

        // 1. Ekstraksi Shared Strings
        $sharedStrings = [];
        $sstXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sstXml !== false) {
            $xml = simplexml_load_string($sstXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) ($r->t ?? '');
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Temukan path worksheet pertama
        $sheetPath = 'xl/worksheets/sheet1.xml';
        $sheetXmlContent = $zip->getFromName($sheetPath);

        if ($sheetXmlContent === false) {
            // Coba cari dari workbook rels
            $wbRels = $zip->getFromName('xl/_rels/workbook.xml.rels');
            if ($wbRels !== false) {
                $relsXml = simplexml_load_string($wbRels);
                if ($relsXml && isset($relsXml->Relationship)) {
                    foreach ($relsXml->Relationship as $rel) {
                        $target = (string) $rel['Target'];
                        if (str_contains($target, 'worksheets/')) {
                            $sheetPath = 'xl/' . ltrim($target, '/');
                            $sheetXmlContent = $zip->getFromName($sheetPath);
                            if ($sheetXmlContent !== false) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        if ($sheetXmlContent === false) {
            $zip->close();
            throw new Exception('Worksheet tidak ditemukan di dalam file Excel.');
        }

        $sheetXml = simplexml_load_string($sheetXmlContent);
        $zip->close();

        if (! $sheetXml || ! isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rawRows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                preg_match('/([A-Z]+)(\d+)/', $ref, $matches);
                $colLetters = $matches[1] ?? 'A';
                $colIdx = $this->colLetterToIndex($colLetters);

                $type = (string) ($cell['t'] ?? '');
                $val = '';

                if ($type === 's') {
                    $sIdx = (int) $cell->v;
                    $val = $sharedStrings[$sIdx] ?? '';
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string) $cell->is->t;
                } elseif (isset($cell->v)) {
                    $val = (string) $cell->v;
                }

                $rowData[$colIdx] = trim($val);
            }

            if (! empty($rowData)) {
                $maxIdx = max(array_keys($rowData));
                $normalizedRow = [];
                for ($i = 0; $i <= $maxIdx; $i++) {
                    $normalizedRow[$i] = $rowData[$i] ?? '';
                }
                $rawRows[] = $normalizedRow;
            }
        }

        return $this->mapRowsToAssociative($rawRows);
    }

    /**
     * Mem-parsing file CSV / TSV dengan auto-detect pemisah (delimiter) dan handling UTF-8 BOM.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            throw new Exception('Gagal membuka file CSV.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);

            return [];
        }

        // Hapus BOM UTF-8 jika ada di baris pertama
        $firstLineClean = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

        // Deteksi delimiter: koma, titik koma, atau tab
        $delimiters = [',', ';', "\t"];
        $delimiterCounts = [];
        foreach ($delimiters as $delim) {
            $delimiterCounts[$delim] = substr_count($firstLineClean, $delim);
        }
        arsort($delimiterCounts);
        $chosenDelimiter = key($delimiterCounts);
        if ($delimiterCounts[$chosenDelimiter] === 0) {
            $chosenDelimiter = ',';
        }

        rewind($handle);

        $rawRows = [];
        $isFirst = true;

        while (($row = fgetcsv($handle, 0, $chosenDelimiter)) !== false) {
            if ($isFirst && ! empty($row)) {
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0]);
                $isFirst = false;
            }

            // Abaikan baris kosong
            if (empty($row) || (count($row) === 1 && $row[0] === null)) {
                continue;
            }

            $rawRows[] = array_map('trim', $row);
        }

        fclose($handle);

        return $this->mapRowsToAssociative($rawRows);
    }

    /**
     * Mengubah baris-baris numerik (raw) menjadi array asosiatif dengan nama header ternormalisasi.
     *
     * @param  array<int, array<int, string>>  $rawRows
     * @return array<int, array<string, mixed>>
     */
    protected function mapRowsToAssociative(array $rawRows): array
    {
        if (empty($rawRows)) {
            return [];
        }

        $headers = [];
        foreach ($rawRows[0] as $headerText) {
            $normalized = $this->normalizeHeader((string) $headerText);
            $headers[] = $normalized;
        }

        $result = [];
        $rowCount = count($rawRows);

        for ($r = 1; $r < $rowCount; $r++) {
            $row = $rawRows[$r];

            // Cek apakah seluruh kolom pada baris ini kosong
            $allEmpty = true;
            foreach ($row as $cell) {
                if ($cell !== '') {
                    $allEmpty = false;
                    break;
                }
            }

            if ($allEmpty) {
                continue;
            }

            $mapped = [];
            foreach ($headers as $colIdx => $colName) {
                if ($colName !== '') {
                    $mapped[$colName] = $row[$colIdx] ?? '';
                }
            }

            $mapped['_row_number'] = $r + 1; // Menyimpan nomor baris file untuk pelaporan error yang jelas
            $result[] = $mapped;
        }

        return $result;
    }

    /**
     * Normalisasi string header ke snake_case lowercase.
     */
    protected function normalizeHeader(string $header): string
    {
        $clean = preg_replace('/^\xEF\xBB\xBF/', '', trim($header));
        $clean = strtolower($clean);
        $clean = preg_replace('/[^a-z0-9]+/', '_', $clean);

        return trim($clean, '_');
    }

    /**
     * Konversi kode huruf kolom Excel (A, B, AA) ke 0-based index.
     */
    protected function colLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $col = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $col = $col * 26 + (ord($letters[$i]) - 64);
        }

        return $col - 1;
    }

    /**
     * Konversi 0-based column index ke kode huruf kolom Excel (0 -> A, 27 -> AB).
     */
    public function indexToColLetter(int $colIndex): string
    {
        $letter = '';
        $colIndex++;
        while ($colIndex > 0) {
            $rem = ($colIndex - 1) % 26;
            $letter = chr(65 + $rem) . $letter;
            $colIndex = (int) (($colIndex - $rem - 1) / 26);
        }

        return $letter;
    }

    /**
     * Membuat string CSV dengan BOM UTF-8 sehingga langsung rapi saat dibuka di Microsoft Excel.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public function createCsv(array $headers, array $rows): string
    {
        $fp = fopen('php://temp', 'r+');
        // UTF-8 BOM
        fwrite($fp, "\xEF\xBB\xBF");

        fputcsv($fp, $headers);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }

        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);

        return $content !== false ? $content : '';
    }

    /**
     * Membuat file Microsoft Excel (.xlsx) murni tanpa dependency eksternal.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public function createXlsx(array $headers, array $rows, string $sheetName = 'Nilai'): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_tmpl_') . '.xlsx';
        $zip = new ZipArchive();
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $sharedStrings = [];
        $stringLookup = [];

        $addString = function (string $str) use (&$sharedStrings, &$stringLookup): int {
            if (isset($stringLookup[$str])) {
                return $stringLookup[$str];
            }
            $idx = count($sharedStrings);
            $sharedStrings[] = $str;
            $stringLookup[$str] = $idx;

            return $idx;
        };

        $sheetDataXml = '<sheetData>';

        // Baris Header (Baris 1)
        $sheetDataXml .= '<row r="1">';
        foreach ($headers as $colIdx => $header) {
            $ref = $this->indexToColLetter($colIdx) . '1';
            $sIdx = $addString((string) $header);
            $sheetDataXml .= "<c r=\"{$ref}\" t=\"s\"><v>{$sIdx}</v></c>";
        }
        $sheetDataXml .= '</row>';

        // Baris Data (Baris 2 dst)
        $rowNum = 2;
        foreach ($rows as $row) {
            $sheetDataXml .= "<row r=\"{$rowNum}\">";
            foreach ($row as $colIdx => $val) {
                $ref = $this->indexToColLetter($colIdx) . $rowNum;
                if (is_numeric($val) && ! is_string($val)) {
                    $sheetDataXml .= "<c r=\"{$ref}\"><v>{$val}</v></c>";
                } else {
                    $sIdx = $addString((string) $val);
                    $sheetDataXml .= "<c r=\"{$ref}\" t=\"s\"><v>{$sIdx}</v></c>";
                }
            }
            $sheetDataXml .= '</row>';
            $rowNum++;
        }
        $sheetDataXml .= '</sheetData>';

        $sstXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $sstXml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
        foreach ($sharedStrings as $str) {
            $sstXml .= '<si><t>' . htmlspecialchars($str, ENT_XML1, 'UTF-8') . '</t></si>';
        }
        $sstXml .= '</sst>';

        $safeSheetName = htmlspecialchars($sheetName, ENT_XML1, 'UTF-8');
        $worksheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $worksheetXml .= "<worksheet xmlns=\"http://schemas.openxmlformats.org/spreadsheetml/2006/main\">\n{$sheetDataXml}\n</worksheet>";

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $contentTypes .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' . "\n";
        $contentTypes .= '  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' . "\n";
        $contentTypes .= '  <Default Extension="xml" ContentType="application/xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' . "\n";
        $contentTypes .= '  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>' . "\n";
        $contentTypes .= '</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $rels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . "\n";
        $rels .= '  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' . "\n";
        $rels .= '</Relationships>';

        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $wbRels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . "\n";
        $wbRels .= '  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' . "\n";
        $wbRels .= '  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>' . "\n";
        $wbRels .= '</Relationships>';

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $workbook .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' . "\n";
        $workbook .= '  <sheets>' . "\n";
        $workbook .= "    <sheet name=\"{$safeSheetName}\" sheetId=\"1\" r:id=\"rId1\"/>\n";
        $workbook .= '  </sheets>' . "\n";
        $workbook .= '</workbook>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/sharedStrings.xml', $sstXml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheetXml);
        $zip->close();

        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        return $content !== false ? $content : '';
    }
}

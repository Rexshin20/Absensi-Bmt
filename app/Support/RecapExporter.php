<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Options\HeaderFooter;
use OpenSpout\Writer\XLSX\Options\PageMargin;
use OpenSpout\Writer\XLSX\Options\PageOrientation;
use OpenSpout\Writer\XLSX\Options\PaperSize;
use OpenSpout\Writer\XLSX\Options\PageSetup;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecapExporter
{
    public function csv(array $report): StreamedResponse
    {
        $filename = 'rekap-absensi-'.$report['month'].'.csv';

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');

            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['REKAP ABSENSI BULANAN'], ';');
            fputcsv($out, [
                'Periode', $report['periodLabel'].($report['cutoffLabel'] ? ' ('.$report['cutoffLabel'].')' : ''),
            ], ';');

            foreach ($report['groups'] as $group) {
                fwrite($out, "\r\n");
                fputcsv($out, [$group['label']], ';');
                fputcsv($out, ['No', 'Nama Karyawan', 'Masuk Tepat Waktu', 'Masuk Terlambat', 'Tidak Masuk', 'Keterangan'], ';');

                foreach ($group['rows'] as $row) {
                    fputcsv($out, [
                        $row['no'], $row['name'], $row['present'], $row['late'], $row['absent'], $row['note'],
                    ], ';');
                }
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function xlsx(array $report): void
    {
        $fontName = 'Times New Roman';

        $options = new Options();
        $options->DEFAULT_ROW_STYLE->setFontName($fontName)->setFontSize(11);
        $options->setColumnWidth(12, 1);
        $options->setColumnWidth(30, 2);
        $options->setColumnWidth(17, 3);
        $options->setColumnWidth(17, 4);
        $options->setColumnWidth(13, 5);
        $options->setColumnWidth(38, 6);
        $options->setPageSetup(new PageSetup(PageOrientation::PORTRAIT, PaperSize::A4, 0, 1));
        $options->setPageMargin(new PageMargin());
        // OpenSpout tidak meng-escape header/footer, jadi &P dan &N harus ditulis sebagai entitas XML.
        $options->setHeaderFooter(new HeaderFooter(null, 'Halaman &amp;P dari &amp;N'));

        $writer = new Writer($options);
        $writer->openToBrowser('rekap-absensi-'.$report['month'].'.xlsx');
        $writer->getCurrentSheet()
            ->setName('Rekap Absensi')
            ->setSheetView((new SheetView())->setShowGridLines(false));

        $border = new Border(
            new BorderPart(Border::LEFT, 'FF000000', Border::WIDTH_THIN),
            new BorderPart(Border::RIGHT, 'FF000000', Border::WIDTH_THIN),
            new BorderPart(Border::TOP, 'FF000000', Border::WIDTH_THIN),
            new BorderPart(Border::BOTTOM, 'FF000000', Border::WIDTH_THIN),
        );

        $titleStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setFontSize(14)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $labelStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $valueStyle = (new Style())
            ->setFontName($fontName)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $headerStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setBackgroundColor('FF7AC143')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setShouldWrapText()
            ->setBorder($border);
        $nameStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $centerStyle = (new Style())
            ->setFontName($fontName)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);
        $noteStyle = (new Style())
            ->setFontName($fontName)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setShouldWrapText()
            ->setBorder($border);
        $officeStyle = (new Style())
            ->setFontName($fontName)
            ->setFontBold()
            ->setFontSize(12)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
        $footerStyle = (new Style())
            ->setFontName($fontName)
            ->setFontSize(9)
            ->setFontColor('6B7280')
            ->setCellAlignment(CellAlignment::RIGHT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($border);

        $row = 1;

        $options->mergeCells(0, $row, 5, $row);
        $writer->addRow((new Row([
            Cell::fromValue('REKAP ABSENSI BULANAN', $titleStyle),
            Cell::fromValue(null, $titleStyle),
            Cell::fromValue(null, $titleStyle),
            Cell::fromValue(null, $titleStyle),
            Cell::fromValue(null, $titleStyle),
            Cell::fromValue(null, $titleStyle),
        ]))->setHeight(28));
        $row++;

        $periodText = $report['periodLabel'].($report['cutoffLabel'] ? ' ('.$report['cutoffLabel'].')' : '');

        $options->mergeCells(1, $row, 5, $row);
        $writer->addRow((new Row([
            Cell::fromValue('Periode', $labelStyle),
            Cell::fromValue($periodText, $valueStyle),
            Cell::fromValue(null, $valueStyle),
            Cell::fromValue(null, $valueStyle),
            Cell::fromValue(null, $valueStyle),
            Cell::fromValue(null, $valueStyle),
        ]))->setHeight(18));
        $row++;

        $writer->addRow((new Row([Cell::fromValue(null)]))->setHeight(8));
        $row++;

        foreach ($report['groups'] as $group) {
            $options->mergeCells(0, $row, 5, $row);
            $writer->addRow((new Row([
                Cell::fromValue($group['label'], $officeStyle),
                Cell::fromValue(null, $officeStyle),
                Cell::fromValue(null, $officeStyle),
                Cell::fromValue(null, $officeStyle),
                Cell::fromValue(null, $officeStyle),
                Cell::fromValue(null, $officeStyle),
            ]))->setHeight(22));
            $row++;

            $options->mergeCells(0, $row, 0, $row + 1);
            $options->mergeCells(1, $row, 1, $row + 1);
            $options->mergeCells(2, $row, 3, $row);
            $options->mergeCells(4, $row, 4, $row + 1);
            $options->mergeCells(5, $row, 5, $row + 1);

            $writer->addRow((new Row([
                Cell::fromValue('No', $headerStyle),
                Cell::fromValue('Nama Karyawan', $headerStyle),
                Cell::fromValue('Masuk', $headerStyle),
                Cell::fromValue(null, $headerStyle),
                Cell::fromValue('Tidak Masuk', $headerStyle),
                Cell::fromValue('Keterangan', $headerStyle),
            ]))->setHeight(20));

            $writer->addRow((new Row([
                Cell::fromValue(null, $headerStyle),
                Cell::fromValue(null, $headerStyle),
                Cell::fromValue('Tepat Waktu', $headerStyle),
                Cell::fromValue('Terlambat', $headerStyle),
                Cell::fromValue(null, $headerStyle),
                Cell::fromValue(null, $headerStyle),
            ]))->setHeight(22));
            $row += 2;

            foreach ($group['rows'] as $data) {
                $writer->addRow((new Row([
                    Cell::fromValue($data['no'], $centerStyle),
                    Cell::fromValue($data['name'], $nameStyle),
                    Cell::fromValue($data['present'], $centerStyle),
                    Cell::fromValue($data['late'], $centerStyle),
                    Cell::fromValue($data['absent'], $centerStyle),
                    Cell::fromValue($data['note'], $noteStyle),
                ]))->setHeight(20));
                $row++;
            }

            $writer->addRow((new Row([Cell::fromValue(null)]))->setHeight(8));
            $row++;
        }

        $options->mergeCells(0, $row, 5, $row);
        $writer->addRow(new Row([
            Cell::fromValue('Dicetak '.$report['generatedAt'].' WIB · BMT Cinde Barokah', $footerStyle),
            Cell::fromValue(null, $footerStyle),
            Cell::fromValue(null, $footerStyle),
            Cell::fromValue(null, $footerStyle),
            Cell::fromValue(null, $footerStyle),
            Cell::fromValue(null, $footerStyle),
        ]));

        $writer->close();
    }

    public function pdf(array $report)
    {
        $pdf = Pdf::loadView('attendances.recap-pdf', $report)
            ->setPaper('a4', 'portrait');

        return $pdf->download('rekap-absensi-'.$report['month'].'.pdf');
    }
}

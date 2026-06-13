<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\PdfLoader;
use TCPDF;

class PdfReportService
{
    public function generate(array $report, array $config): void
    {
        PdfLoader::load();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator($config['name']);
        $pdf->SetAuthor($config['name']);
        $pdf->SetTitle($report['title']);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $symbol = $config['currency_symbol'] ?? 'Rs.';

        $html = '<h1 style="text-align:center;color:#333;">' . htmlspecialchars($config['name']) . '</h1>';
        $html .= '<h2 style="text-align:center;color:#666;">' . htmlspecialchars($report['title']) . '</h2>';
        $html .= '<p style="text-align:center;">Period: ' . $report['start'] . ' to ' . $report['end'] . '</p>';
        $html .= '<hr>';

        $html .= '<table cellpadding="6" border="1" style="width:100%;border-collapse:collapse;">';
        $html .= '<tr style="background-color:#f0f0f0;"><th>Metric</th><th align="right">Amount (' . $symbol . ')</th></tr>';
        $html .= '<tr><td>Total Sales</td><td align="right">' . number_format($report['total_sales'], 2) . '</td></tr>';
        $html .= '<tr><td>Total Purchases</td><td align="right">' . number_format($report['total_purchases'], 2) . '</td></tr>';
        $html .= '<tr><td>Total Expenses</td><td align="right">' . number_format($report['total_expenses'], 2) . '</td></tr>';
        $html .= '<tr style="background-color:#e8f5e9;"><td><strong>Net Profit</strong></td><td align="right"><strong>' . number_format($report['profit'], 2) . '</strong></td></tr>';
        $html .= '</table>';

        $html .= '<br><h3>Sales</h3>';
        $html .= $this->buildTransactionTable($report['sales'], 'sale_date', 'total_price', $symbol);

        $html .= '<br><h3>Purchases</h3>';
        $html .= $this->buildTransactionTable($report['purchases'], 'purchase_date', 'total_cost', $symbol);

        $html .= '<br><h3>Expenses</h3>';
        if (empty($report['expenses'])) {
            $html .= '<p>No expenses recorded.</p>';
        } else {
            $html .= '<table cellpadding="5" border="1" style="width:100%;border-collapse:collapse;font-size:10px;">';
            $html .= '<tr style="background-color:#f0f0f0;"><th>Date</th><th>Title</th><th>Category</th><th align="right">Amount</th></tr>';
            foreach ($report['expenses'] as $row) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($row['expense_date']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['title']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['category_name']) . '</td>';
                $html .= '<td align="right">' . number_format((float) $row['amount'], 2) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
        }

        $html .= '<br><p style="font-size:9px;color:#999;text-align:center;">Generated on ' . date('Y-m-d H:i:s') . '</p>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = 'report_' . date('Ymd_His') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    private function buildTransactionTable(array $rows, string $dateField, string $amountField, string $symbol): string
    {
        if (empty($rows)) {
            return '<p>No records found.</p>';
        }

        $html = '<table cellpadding="5" border="1" style="width:100%;border-collapse:collapse;font-size:10px;">';
        $html .= '<tr style="background-color:#f0f0f0;"><th>Date</th><th>Item</th><th>Qty</th><th align="right">Amount (' . $symbol . ')</th></tr>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row[$dateField]) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['item_name']) . '</td>';
            $html .= '<td>' . number_format((float) $row['quantity'], 2) . '</td>';
            $html .= '<td align="right">' . number_format((float) $row[$amountField], 2) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }
}

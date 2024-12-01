<?php
require_once __DIR__ . '/vendor/autoload.php'; // Composerのオートローダーを読み込む

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームデータを受け取る（エスケープ処理を適用）
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $trip_date = htmlspecialchars($_POST['trip_date'], ENT_QUOTES, 'UTF-8');
    $destination = htmlspecialchars($_POST['destination'], ENT_QUOTES, 'UTF-8');
    $transportation = htmlspecialchars($_POST['transportation'], ENT_QUOTES, 'UTF-8');
    $cost = htmlspecialchars($_POST['cost'], ENT_QUOTES, 'UTF-8');

    // PDFを作成するためのTCPDFオブジェクトを生成
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);

    // ヘッダーとフッターを無効にする
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // メタデータ設定
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('出張交通費申請システム');
    $pdf->SetTitle('出張交通費申請書');

    // PDFに新しいページを追加
    $pdf->AddPage();

    // フォントのパスを適宜修正
    //$font_path = 'font/ipag.ttf';
    $pdf->SetFont('ipag', '', 12, true);

    // タイトルを追加
    $pdf->Cell(0, 15, '出張交通費申請書', 0, 1, 'C'); // 中央揃え
    $pdf->Ln(5); // 少しスペースを追加

    // 課長と主任の印欄を右上に配置するための位置指定
    $pdf->SetXY(163, 15); // ページの右上に印欄を配置

    // 課長と主任の印欄
    $html_sign = "
    <table border=\"1\" cellpadding=\"5\" style=\"border-collapse: collapse;\">
        <tr>
            <td style=\"width: 50px; text-align: center; background-color: #f2f2f2;\"><strong>課長</strong></td>
            <td style=\"width: 50px; text-align: center; background-color: #f2f2f2;\"><strong>主任</strong></td>
        </tr>
        <tr>
            <td style=\"height: 50px; width: 50px; border: 1px solid black;\"></td>
            <td style=\"height: 50px; width: 50px; border: 1px solid black;\"></td>
        </tr>
    </table>
    ";

    // 課長と主任の印欄を右上に書き込む
    $pdf->writeHTML($html_sign, true, false, true, false, '');

    $pdf->SetXY(10, 47); // 左上に戻す
    // 申請書の内容（表形式に変更）
    $html_content = "
    <table border=\"1\" cellpadding=\"8\" style=\"border-collapse: collapse; width: 100%;\">
        <thead>
            <tr style=\"background-color: #f2f2f2;\">
                <th><strong>項目</strong></th>
                <th><strong>詳細</strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>名前</strong></td>
                <td>$name</td>
            </tr>
            <tr>
                <td><strong>出張日</strong></td>
                <td>$trip_date</td>
            </tr>
            <tr>
                <td><strong>目的地</strong></td>
                <td>$destination</td>
            </tr>
            <tr>
                <td><strong>交通手段</strong></td>
                <td>$transportation</td>
            </tr>
            <tr>
                <td><strong>交通費</strong></td>
                <td>¥".number_format($cost)."</td>
            </tr>
        </tbody>
    </table>
    ";

    // 申請内容をPDFに書き込む
    $pdf->writeHTML($html_content, true, false, true, false, '');

    // PDFを出力（ブラウザに表示する）
    $pdf->Output('travel_expense.pdf', 'I');
}

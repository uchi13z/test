<?php
require_once __DIR__ . '/vendor/autoload.php'; // Composerのオートローダーを読み込む

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームデータを受け取る（エスケープ処理を適用）
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $trip_date = htmlspecialchars($_POST['trip_date'], ENT_QUOTES, 'UTF-8');
    $destination = htmlspecialchars($_POST['destination'], ENT_QUOTES, 'UTF-8');

    // 配列として受け取る（複数の交通手段、駅間、交通費）
    $transportation = $_POST['transportation'];
    $from_station = $_POST['from_station'];
    $to_station = $_POST['to_station'];
    $cost = $_POST['cost'];

    // 合計金額を計算する変数
    $total_cost = 0;

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

    // フォント設定
    $pdf->SetFont('ipag', '', 12, true);

    // タイトルを追加
    $pdf->Cell(0, 15, '出張交通費申請書', 0, 1, 'C'); // 中央揃え
    $pdf->Ln(5); // 少しスペースを追加

    // 課長と主任の印欄を右上に配置するための位置指定
    $pdf->SetXY(163, 15); // ページの右上に印欄を配置

    // 課長と主任の印欄
    $html_sign = "
    <table cellpadding=\"5\" cellspacing=\"0\" style=\"border-collapse: collapse; border: 0.5pt solid black;\">
        <tr>
            <th style=\"width: 50px; height: 20px; text-align: center; background-color: #f2f2f2; border: 0.5pt solid black;\"><strong>課長</strong></th>
            <th style=\"width: 50px; height: 20px; text-align: center; background-color: #f2f2f2; border: 0.5pt solid black;\"><strong>主任</strong></th>
        </tr>
        <tr>
            <td style=\"height: 50px; width: 50px; border: 0.5pt solid black;\"></td>
            <td style=\"height: 50px; width: 50px; border: 0.5pt solid black;\"></td>
        </tr>
    </table>
    ";

    // 課長と主任の印欄を右上に書き込む
    $pdf->writeHTML($html_sign, true, false, true, false, '');

    $pdf->SetXY(10, 47); // 左上に戻す

    // 申請書の基本情報
    $html_content = "
    <table cellpadding=\"8\" cellspacing=\"0\" style=\"border-collapse: collapse; width: 100%; border: 0.5pt solid black;\">
        <thead>
            <tr style=\"background-color: #f2f2f2;\">
                <th style=\"width: 25%; border: 0.5pt solid black;\"><strong>項目</strong></th>
                <th style=\"width: 75%; border: 0.5pt solid black;\"><strong>詳細</strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style=\"width: 25%; border: 0.5pt solid black;\"><strong>名前</strong></td>
                <td style=\"width: 75%; border: 0.5pt solid black;\">$name</td>
            </tr>
            <tr>
                <td style=\"border: 0.5pt solid black;\"><strong>出張日</strong></td>
                <td style=\"border: 0.5pt solid black;\">$trip_date</td>
            </tr>
            <tr>
                <td style=\"border: 0.5pt solid black;\"><strong>目的地</strong></td>
                <td style=\"border: 0.5pt solid black;\">$destination</td>
            </tr>
        </tbody>
    </table>
    ";

    // 申請内容の基本情報をPDFに書き込む
    $pdf->writeHTML($html_content, true, false, true, false, '');

    // 交通手段の詳細をループ処理して表示
    $html_transport = "
    <table cellpadding=\"8\" cellspacing=\"0\" style=\"border-collapse: collapse; width: 100%; border: 0.5pt solid black;\">
        <thead>
            <tr style=\"background-color: #f2f2f2;\">
                <th style=\"border: 0.5pt solid black;\"><strong>交通手段</strong></th>
                <th style=\"border: 0.5pt solid black;\"><strong>何駅から</strong></th>
                <th style=\"border: 0.5pt solid black;\"><strong>何駅まで</strong></th>
                <th style=\"border: 0.5pt solid black;\"><strong>交通費 (円)</strong></th>
            </tr>
        </thead>
        <tbody>
    ";

    // 複数の交通手段を1行ずつテーブルに追加
    for ($i = 0; $i < count($transportation); $i++) {
        $safe_transportation = htmlspecialchars($transportation[$i], ENT_QUOTES, 'UTF-8');
        $safe_from_station = htmlspecialchars($from_station[$i], ENT_QUOTES, 'UTF-8');
        $safe_to_station = htmlspecialchars($to_station[$i], ENT_QUOTES, 'UTF-8');
        $safe_cost = htmlspecialchars($cost[$i], ENT_QUOTES, 'UTF-8');

        // 合計金額に加算
        $total_cost += (int)$safe_cost;

        $html_transport .= "
            <tr>
                <td style=\"border: 0.5pt solid black;\">$safe_transportation</td>
                <td style=\"border: 0.5pt solid black;\">$safe_from_station</td>
                <td style=\"border: 0.5pt solid black;\">$safe_to_station</td>
                <td style=\"border: 0.5pt solid black;\">¥" . number_format($safe_cost) . "</td>
            </tr>
        ";
    }

    // テーブル終了タグ
    $html_transport .= "
        </tbody>
    </table>
    ";

    // 交通手段の詳細をPDFに書き込む
    $pdf->writeHTML($html_transport, true, false, true, false, '');

    // 交通費の合計金額を表示
    $pdf->Ln(10); // 少しスペースを追加
    $pdf->Cell(0, 10, '交通費の合計: ¥' . number_format($total_cost), 0, 1, 'R'); // 合計金額を右寄せで表示

    // PDFを出力（ブラウザに表示する）
    $pdf->Output('travel_expense.pdf', 'I');
}

<?php
// CSVファイルを読み込む
$filename = 'sales.csv'; // CSVファイルのパス
$data = [];

if (($handle = fopen($filename, 'r')) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
        $data[] = $row;
    }
    fclose($handle);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社実田テクノロジー</title>
    <!--=============Google Font ===============-->
    <link href="https://fonts.googleapis.com/css?family=Lato:900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/parts.css">
    <link rel="stylesheet" href="css/style2.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0px;
            text-align: center;
        }
        h1 {
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .setsumei {
            font-size: 120%;
            margin: 0 auto;
            margin-top: 10px;
            margin-bottom: 30px;
            width: 95%;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
@media screen and (max-width: 767px) {
/* ここに横幅が767px以下の時に発動するスタイルを記述 */
        table {
            width: 95%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 50%;
        }
        th, td {
            padding: 2px;
            border: 1px solid #ddd;
            text-align: center;
        }
        canvas {
            width: 95%;
            /*height: 600px;*/
            margin: 30px auto;
            display: block;
        }
}
@media screen and (min-width: 767px) {
/* ここに横幅が767pxより大きい時に発動するスタイルを記述 */
        table {
            width: 900px;
            margin: 0 auto;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        canvas {
            width: 900px;
            height: 600px;
            margin: 30px auto;
            display: block;
        }
}
    </style>
</head>
<body>
    <!-- ヘッダー -->
    <div id="nav">
        <span class=""><ul>
            <li><a href="index.html">ホーム</a></li>
            <li><a href="index.html#about">会社概要</a></li>
            <li><a href="index.html#service">サービス内容</a></li>
            <li><a href="index.html#contact">お問い合わせ</a></li>
            <li><a href="blog.html">ブログ</a></li>
        </ul></span>
    </div>
    <header>
        <div class="logo zoomIn">
            <img src="img/jida2.png" alt="株式会社実田テクノロジーのロゴ">
        </div>
    </header>

    <h1>売上げデータ</h1>
    <p class="setsumei">csvファイルを読み込んで、表とグラフを表示しています</p>
    <!-- テーブル表示 -->
    <table>
        <thead>
            <tr>
                <?php
                // ヘッダーを表示
                foreach ($data[0] as $header) {
                    echo "<th>$header</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // データを表示
            for ($i = 1; $i < count($data); $i++) {
                echo "<tr>";
                foreach ($data[$i] as $cell) {
                    echo "<td>$cell</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- グラフ表示 -->
    <canvas id="salesChart"></canvas>

    <!-- フッター -->
    <footer class="footer-class">
        <div class="openbtn"><span></span><span>Menu</span><span></span></div>
        <div id="g-nav">
        <div id="g-nav-list">
            <ul>
            <li><a href="index.html">ホーム</a></li>
            <li><a href="index.html#about">会社概要</a></li>
            <li><a href="index.html#service">サービス内容</a></li>
            <li><a href="index.html#contact">お問い合わせ</a></li>
            <li><a href="blog.html">ブログ</a></li>
            </ul>
        </div>
        </div>
        <div class="footer-content">
            <div class="company-info">
                <p>株式会社実田テクノロジー</p>
                <p>所在地：〒123-4567 ○○県○○市○○町○番○号</p>
            </div>
            <div class="footer-legal">
                <a href="/privacy-policy">プライバシーポリシー</a>
                <a href="/terms-of-service">利用規約</a>
                <p>© 2024 株式会社実田テクノロジー. All rights reserved.</p>
            </div>
        </div>
    </footer>

<!--jQuery-->
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/scrollgress/2.0.0/scrollgress.min.js"></script>
<!--Vue.js-->
<script src="https://unpkg.com/vue@3.1.5"></script>
<!--自作のJS-->   
<script src="js/app2.js"></script>
<script src="js/script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // PHPデータをJavaScriptで使えるように変換
        const data = <?php echo json_encode($data); ?>;
        
        const labels = data[0].slice(1); // 月のラベル
        const datasets = [];

        for (let i = 1; i < data.length; i++) {
            const year = data[i][0];
            const sales = data[i].slice(1).map(Number); // 売り上げデータ

            datasets.push({
                label: year,
                data: sales,
                fill: false,
                borderColor: `rgba(${i * 40}, 99, 132, 1)`
            });
        }

        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,

                maintainAspectRatio: true,  // 縦横比を維持
                aspectRatio: 1.5,  // ここで縦横比を指定（1.5は横:縦が1.5:1）
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: '月'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: '売上げ'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

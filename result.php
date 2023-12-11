<?php
$countSymbols = 0;
$countLetters = 0;
$countUpRegistr = 0;
$countDownRegistr = 0;
$countSymbolStop = 0;
$countDigits = 0;
$countWords = 0;
$countIncludeSymbol = array();
$WordsData = array();

function starts_with_upper($char)
{
    if (preg_match('/[a-zA-Z]/', $char) or (preg_match('/[а-яёА-ЯЁ]+/u', iconv("cp1251", "utf-8", $char)))) {
        if (ctype_upper(mb_substr($char,0,1))) {
            return 'Up';
        }
        else return 'Down';
    }
    return '0';
}
function test_symbs($text)
{
    $symbs = array(); // массив символов текста
    $l_text = strtolower($text); // переводим текст в нижний регистр
    // последовательно перебираем все символы текста
    for ($i = 0; $i < strlen($l_text); $i++) {
        if (isset($symbs[$text[$i]])) // если символ есть в массиве 
            $symbs[$text[$i]]++; // увеличиваем счетчик повторов
        else // иначе
            $symbs[$text[$i]] = 1; // добавляем символ в массив
    }
    return $symbs;
}
function countUpDownSymbols(&$countDownRegistr, &$countUpRegistr, $text)
{
    // последовательно перебираем все символы текста
    for ($i = 0; $i < strlen($text); $i++) {
        // echo '1';
        if (starts_with_upper($text[$i]) == 'Up') {
            $countUpRegistr++;
        } else if (starts_with_upper($text[$i]) == 'Down') // иначе
            $countDownRegistr++;
    }
}
function test_it($data, &$countSymbols, &$countLetters, &$countDownRegistr, &$countUpRegistr, &$countSymbolStop, &$countDigits, &$countWords, &$countIncludeSymbol, &$WordsData)
{
    $countSymbols = strlen($data);
    countUpDownSymbols($countDownRegistr, $countUpRegistr, $data);

    $digit = array(
        '0' => true, '1' => true, '2' => true, '3' => true, '4' => true,
        '5' => true, '6' => true, '7' => true, '8' => true, '9' => true
    );
    // вводим переменные для хранения информации о: 
    $word = ''; // текущее слово

    for ($i = 0; $i < strlen($data); $i++) // перебираем все символы текста
    {
        $countIncludeSymbol = test_symbs($data);

        if (array_key_exists($data[$i], $digit)) // если встретилась цифра
            $countDigits++; // увеличиваем счетчик цифр
        else if (preg_match('/[a-zA-Z]/', $data[$i]) or (preg_match('/[а-яёА-ЯЁ]+/u', iconv("cp1251", "utf-8", $data[$i])))) {
            $countLetters++;
        } else if ($data[$i] == ',') {
            $countSymbolStop++;
        }
        if ($i == strlen($data) - 1) $word .=  $data[$i];
        // если в тексте встретился пробел или текст закончился
        if ($data[$i] == ' ' || $i == strlen($data) - 1) {
            if ($word) // если есть текущее слово
            {
                $countWords++;
                // если текущее слово сохранено в списке слов
                if (isset($WordsData[$word])) {
                    $WordsData[$word]++; // увеличиваем число его повторов
                } else {
                    $WordsData[$word] = 1; // первый повтор слова
                }
            }
            $word = ''; // сбрасываем текущее слово
        } else // если слово продолжается
            $word .= $data[$i]; //добавляем в текущее слово новый символ
    }
}
$data = "";
if (isset($_POST['data']) && $_POST['data']) // если передан текст для анализа
{
    $data = $_POST['data'];
    test_it(iconv("utf-8", "cp1251", $data), $countSymbols, $countLetters, $countDownRegistr, $countUpRegistr, $countSymbolStop, $countDigits, $countWords, $countIncludeSymbol, $WordsData);
} else // если текста нет или он пустой
    $data = "Пусто";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory 102</title>
    <link rel="stylesheet" href="styleResult.css">
</head>

<body>
    <header>
        <div>
            <h2>Laboratory 10 Analyse of text</h2>
        </div>
    </header>
    <main class="container">
        <label class="labelsInfo" for="textareaInput">Введенный текст</label>
        <textarea id="textAreaInput"><?php echo $data ?></textarea>
        <table>
            <thead>
                <tr>
                    <th colspan="2">Результат:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Количество символов в тексте(включая пробелы)</td>
                    <td><?php echo $countSymbols ?></td>
                </tr>
                <tr>
                    <td>Количество букв</td>
                    <td><?php echo $countLetters ?></td>
                </tr>
                <tr>
                    <td>Количество строчных и заглавных букв по отдельности</td>
                    <td><?php echo 'Заглавных: ' . $countUpRegistr;
                        echo '<br>';
                        echo 'Прописных: ' . $countDownRegistr;
                        ?></td>
                </tr>
                <tr>
                    <td>Количество знаков препинания</td>
                    <td><?php echo $countSymbolStop ?></td>
                </tr>
                <tr>
                    <td>Количество цифр</td>
                    <td><?php echo $countDigits ?></td>
                </tr>
                <tr>
                    <td>Количество слов</td>
                    <td><?php echo $countWords ?></td>
                </tr>
                <tr>
                    <td>Количество вхождений каждого символа текста (без различия верхнего и нижнего регистров)</td>
                    <td><?php foreach ($countIncludeSymbol as $item => $item_count) {
                            echo "Символ=" . '\'' . iconv("cp1251", "utf-8", $item) . '\'' . ", Количество=" . $item_count . '<br>';
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>Список всех слов в тексте и количество их вхождений, отсортированный по алфавиту</td>
                    <td><?php
                        ksort($WordsData);
                        foreach ($WordsData as $item => $item_count) {
                            echo "Слово=" . iconv("cp1251", "utf-8", $item) . ", Количество=" . $item_count . '<br>';
                        }
                        ?></td>
                </tr>
            </tbody>
        </table>
        <a id="linkOtherAnalyse" href="index.html">Другой анализ</a>
    </main>
    <footer>
        <ul>
            <li>Contacts</li>
            <li>Link to us</li>
            <li>Condition of use</li>
        </ul>
    </footer>
</body>

</html>
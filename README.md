# COLROW PHP SDK
COLROW PHP SDK は、PHP 環境にて [COLROW](http://colrow.net/) API を利用するための SDK です。

## インストール方法
[Composer](https://getcomposer.org/download/) を使います。まずはプロジェクトのルートディレクトリにて、以下の composer.json を作成し、php composer.phar install を実行してください。
```
{
    "require": {
        "rashiku/colrow-php-sdk" : "1.0.*"
    }
}
```
以下のように記述することで COLROW PHP SDK が利用できるようになります。
```
require_once 'vendor/autoload.php';
```
何らかの事情で Composer が使えない場合は、[ファイル一式](https://github.com/rashikucorp/colrow-php-sdk/releases)をダウンロードし、Colrow フォルダをそのままお使いください。上記の autoload.php の代わりに以下のように記述し、必要なファイル群を読み込んでください。
```
require_once 'Colrow/ColrowClient.php';
require_once 'Colrow/ColrowObject.php';
require_once 'Colrow/ColrowQuery.php';
require_once 'Colrow/ColrowDrive.php';
require_once 'Colrow/ColrowException.php';
```
※PHP のバージョンは 5.4 以上である必要があります。

## 初期化
SDK から必要なファイルを読み込んだ後、ColrowClient を初期化する必要があります（ColrowDrive だけは例外）。
```
ColrowClient::initialize(
    'your_account',
    'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
    'シート1'
);
```
3つの引数はそれぞれ「COLROW に登録した Google アカウント」「スプレッドシートの key」「ワークシート名」になります。

## 基本的な使い方
利用する機能に応じたクラスを use 文にて宣言します。大抵のケースでは、以下の3つのクラス（のうちいずれか）を宣言することになるでしょう。
```
use Colrow\ColrowClient;
use Colrow\ColrowQuery;
use Colrow\ColrowObject;
```

**オブジェクトとして全件取得**：
```
$query = new ColrowQuery();

$objects = $query->find();
```

**最初の1件を取得**：
```
$query = new ColrowQuery();

$object = $query->first();
```

**クエリ（後述）を指定して取得**：
```
$query = new ColrowQuery();
$query->equalTo('会社名', 'らしく');
$query->orderBy('日付');
$query->reverse(true);

$objects = $query->find();
```

**件数をカウント**：
```
$query = new ColrowQuery();

echo $query->count();
```

**シートに新しい行を追加**：
```
$row = new ColrowObject();

$row->set('会社名', 'らしく販売');
$row->set('担当者', '鈴木一郎');
$row->set('日付', '2016/1/1');

$row->save();
```

**行の内容を更新**：
```
$query = new ColrowQuery();

$objects = $query->find();
$row = $objects[10];

$row->set('担当者', '田中太郎');

$row->save();
```

**行を削除**：
```
$query = new ColrowQuery();

$objects = $query->find();
$row = end($objects);

$row->destroy();
```

## COLROW オブジェクト
ColrowQuery クラス経由で取得したオブジェクトは、get メソッドによって内容を参照できます。
```
foreach ($objects as $object) {
    echo $object->get('会社名') . "\n";
}
```
その他にも、以下のメソッドが利用可能です。

* **set(_label_, _value_)** ... オブジェクトの _label_（列のラベル名）に _value_ をセット
* **save()** ... オブジェクトを保存。シートの該当行の内容も更新（新規作成した場合は最終行に追加）
* **destroy()** ... オブジェクトを削除。シートの該当行も削除
* **getId()** ... オブジェクトの ID（＝該当行に割り当てられた固有の ID）を参照
* **toJson()** ... JSON 文字列に変換（デバッグ用）

## クエリ
シートの内容を抽出するため、ColrowQuery クラスのインスタンスに対して、SQL の where 句に相当する以下のメソッドが用意されています。

* **equalTo(_label_, _value_)** ... _label_ の値が _value_ に等しい
* **notEqualTo(_label_, _value_)** ... _label_ の値が _value_ に等しくない
* **lessThan(_label_, _value_)** ... _label_ の値が _value_ より小さい
* **greaterThan(_label_, _value_)** ... _label_ の値が _value_ より大きい
* **lessThanOrEqualTo(_label_, _value_)** ... _label_ の値が _value_ より小さいか等しい
* **greaterThanOrEqualTo(_label_, _value_)** ... _label_ の値が _value_ より大きいか等しい
* **exists(_label_)** ... _label_ の値が空ではない
* **doesNotExist(_label_)** ... _label_ の値が空である

上記の条件を複数列記することで AND として扱われます。
```
$query->equalTo('カテゴリ', 'A');
$query->notEqualTo('状態', '休業中');
$query->lessThanOrEqual('優先度', 5);
```

OR の場合は ColrowQuery のインスタンスを個別に作成し、orQuery メソッドを使います。
```
$query1 = new ColrowQuery();
$query1->equalTo('会社名', 'らしく');
$query2 = new ColrowQuery();
$query2->equalTo('会社名', 'らしく販売');

$query = new ColrowQuery();
$query->orQuery($query1, $query2);

$objects = $query->find();
```

その他にも以下のメソッドが利用可能です。

* **orderBy(_label_)** ... _label_ の値でソート（デフォルトは昇順）
* **reverse(_boolean_)** ... true を指定することでソートを降順に変更
* **offset(_number_)** ... 指定した列数をスキップ
* **limit(_number_)** ... 取得する列数を指定

## ColrowDrive について
Google ドライブにファイルをアップロードするために用意された ColrowDrive クラスは、前述した他のクラスとはやや使用感が異なります。
```
use Colrow\ColrowDrive;

if (isset($_FILES['image'])) {
    $file = $_FILES['image'];

    $fp = fopen($file['tmp_name'], 'r');
    $file_body = base64_encode(fread($fp, filesize($file['tmp_name'])));
    fclose($fp);

    $parent_id = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $response = ColrowDrive::upload('your_account', $file['name'], $file['type'], $file_body, $parent_id);

    echo json_encode($response);
} else {
    echo '<form method="post" action="" enctype="multipart/form-data">';
    echo '<input type="file" name="image">';
    echo '<input type="submit" value="アップロード">';
    echo '</form>';
}
```
$parent_id はファイルのアップロード先、つまり Google ドライブのフォルダの ID になります。そのフォルダのページの URL の `https://drive.google.com/drive/u/0/folders/` 以降に書かれているのがそれです。また、ColrowDrive::upload() の第1引数には、COLROW に登録した Google アカウントを指定してください。

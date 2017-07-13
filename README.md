PHPカンファレンス関西2017 PHPにおけるDSL
=============

「PHPにおけるDSL」のサンプルです。

[Composer](https://getcomposer.org/)で必要なライブラリをインストールし、ビルトインサーバーを起動します。

```
$ composer install
$ php bin/console server:run
```

http://localhost:8000 にアクセスすると動作を確認することができます。

テストも実行することができます。

```
$ php vendor/bin/phpunit tests
```

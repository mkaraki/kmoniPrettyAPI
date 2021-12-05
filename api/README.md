# kmon Pretty API Proxy

強震モニター内部APIを成型し、データベースに記述することで過去のログを確認しやすくし、アプリケーション設計を簡略化します。

## API情報

[OpenAPIデータ](../kmoniPrettyAPI.yaml)を参照して下さい。


## デプロイ

このプロジェクトは
- PHP8.0
- MySQL8.0

を前提に開発されています。


### 前提ライブラリのインストール

このプロジェクトはComposerでライブラリを管理しています。

```bash
  composer install
```

### DB接続情報の設定

`_internal/db_cred.example.php`を参考に`_internal/db_cred.php`にデータベース接続情報を記述してください。

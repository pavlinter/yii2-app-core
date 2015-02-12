Yii 2 Core Application Template (based on advanced)
===================================================

composer create-project --prefer-dist --stability=dev pavlinter/yii2-app-core projectName

 - yii migrate --migrationPath=@vendor/pavlinter/yii2-adm/adm/migrations
 - yii migrate --migrationPath=@vendor/pavlinter/yii2-adm-pages/admpages/migrations
 - yii migrate --migrationPath=@vendor/pavlinter/yii2-adm-mailing/admmailing/migrations
 - yii migrate --migrationPath=@vendor/pavlinter/yii2-adm-params/admparams/migrations
 - yii migrate --migrationPath=@vendor/pavlinter/yii2-adm-email-config/admeconfig/migrations
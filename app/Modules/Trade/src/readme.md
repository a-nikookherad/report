# How to launch Trade library

__First run migration:__

```shell
php artisan migrate --path=app/Modules/Trade/src/Database/Migrations
```

__But if you want to roll back these migrations use this command:__

```shell
php artisan migrate:rollback --path=app/Modules/Trade/src/Database/Migrations
```


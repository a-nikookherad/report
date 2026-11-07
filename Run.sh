# ساخت ایمیج
docker compose build --no-cache

# اجرا کردن همه سرویس‌ها
docker compose up -d

# اجرای migration و کش
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

DB_HOST=${DB_HOST:-$(grep DB_HOST .env | cut -d '=' -f2)}
DB_USERNAME=${DB_USERNAME:-$(grep DB_USERNAME .env | cut -d '=' -f2)}
DB_PASSWORD=${DB_PASSWORD:-$(grep DB_PASSWORD .env | cut -d '=' -f2)}

until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e 'select 1'; do
  echo "Aguardando o banco de dados..."
  sleep 2
done

php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
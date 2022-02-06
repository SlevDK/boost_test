#Deploy stages:

```bash
mkdir /path/to/www/somedir
cd /path/to/www/somedir

git clone https://github.com/SlevDK/boost_test .
```

#### Envs
```bash
cp .env.example .env
// Configure .env
    DB_CONNECTION=pgsql
    DB_HOST=boosteroid_db (container name)
    DB_PORT=5432
    
cp .env.dev.example .env.dev
// Configure .env.dev
    APP_ENV=develop
    DB_DATABASE=boosteroid
    DB_USERNAME=boosteroid_dev
    DB_PASSWORD=secret
```

#### Docker
```bash
docker-compose --env=.env.dev up
docker exec boosteroid_app php artisan migrate --seed
docker exec boosteroid_app php artisan key:generate
```

#### Front
```http
GET http://localhost:8080/ (in browser)

// Add new entry: 
GET http://localhost:8080/api/songs-create?email=some@email.me&duration=123
// email already must exist in database, otherwise 422
```

Check SongController, SongRepository for details

... Web Application - README
-------------

## PHP version

7.2.0

## System depedencies

Pastikan sudah ada composer, git, nodejs dan npm.

Setelah cloning aplikasi menggunakan perintah `git clone`, change directory ke folder project. Jadi present working directory kita ada di root folder project.

Lalu jalankan perintah `composer install` untuk meminta composer mendownload semua packages dependencies. Packages ini adalah libraries untuk server-side application.

Lalu, jalankan `npm ci`. Ini untuk proses install dependencies dan build assets.

## File preparation

Copy file `.env.staging` ke file `.env`. Bisa dengan perintah `cp .env.staging .env`. Kedua file ini ada di root folder project.

Jadi hasil akhirnya adalah `/.env`.

Ketika akan mengambil value yang terdapat pada file `/.env`, baik di controller maupun view, dianjurkan menggunakan fungsi `config()`. 

##### Penjelasan:

Pada `config/example.php` (ganti nama file `example.php` dengan nama project) :
```
'app_version' => env('APP_VERSION', '0.1.0'),

'request_api' => [
    'url' => env('API_URL' ,''),
    'timeout' => 15
]
```

Pemanggilan:
```
<label>
    {{ config('example.app_version') }}
    {{ config('example.request_api.url') }}
</label>
```

## Configuration

Jalankan `php artisan key:generate` untuk generate APP_KEY yang digunakan untuk hashing.

## Copy dependecies

Jalankan `npm run dev` untuk melakukan generate patternfly JS, CSS, dan copy CSS dan image dari patternfly.

## Dashboard icon

Apabila akan menggunakan icon pada dashboard, dianjurkan menggunakan icon dari:
https://www.flaticon.com/authors/meticulous/gradient.

Icon dapat disesuaikan dengan konteks tulisan.

## Database creation

Kita menggunakan PostgreSQL. Sesuaikan database connection pada file `.env`

Tambahkan `DB_CONNECTION=pgsql` diatas `DB_HOST=localhost`.

Sesuaikan username, password, dan database di file `.env`.

Jangan lupa untuk menyesuaikan nama database dengan database yang sudah dibuat sebelumnya. Tidak perlu membuat table dulu di database.

Hasil akhirnya seperti ini:

```
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_DATABASE=laravel-patternfly-skeleton
DB_USERNAME=yogya
DB_PASSWORD=secret
```

## Database initialization

Jalankan perintah `php artisan migrate --seed` untuk membuat table di database dan mengisi data awal.

## Directory permissions

Jalankan `chmod -R 777 storage bootstrap/cache`.

Jalankan `chmod +x bump_version.sh`.

## Commands in kernel

Jalankan `php artisan make:command name_command`

Initial Class `Command` pada `app/Console/Kernel.php` bagian `$commands`

Initial `console command` pada bagian `function schedule` untuk melakukan `execute command` yang sudah dibuat dan nanti nya akan terjadwal untuk menjalankan nya

## Cron

Jalankan `crontab -e` untuk membuat cron

Tambahkan `* * * * * php path/to/directory/artisan schedule:run`, bertujuan untuk menjalankan dan mengecek setiap menit `command` pada `Kernel.php` yang sudah dibuat

Jalankan `crontab -l` untuk melihat list cron

## Bump Version Aplication Automatic
Pastikan tersedia file ```.env```

Jalankan ```chmod +x bump_version.sh```

Jalankan ```./bump_version.sh```

## Minio
#### Untuk menyimpan file ke Server Minio
```Storage::disk('minio')->put('path/to/file/on/minio', /content/file```

## Integrasi dengan PSV Server
### Untuk sinkronisasi data store dari MDM-PSV Server
Setting file .env jika dijalankan di lokal untuk bagian SERVER_PRIVATE_KEY pada #MDM-PSV-SERVER sesuai dengan lokasi secret key pada komputer lokal. 

Tambahkan secret key server dan/atau lokal pada PSV Server bagian /home/deployer/.ssh/ untuk file authorized_keys.

Tambahkan telegram bot dengan edit file .env pada bagian TELEGRAM_HOST. Sesuaikan variabel notifikasi telegram yang dibutuhkan meliputi 'server', 'servertime', 'status', 'psv', dan 'message'.

## Sentry
Setting DSN di ```env``` setelah didapat dari aplikasi sentry, contohnya:
```dotenv
SENTRY_DSN=https://1234567890abcdef1234567890abcdeff@sentry-new.yogyagroup.com/20
```
Sentry akan aktif apabila di ```env``` setting ```APP_ENV=staging``` atau ```APP_ENV=production```, berlaku untuk environment staging atau production saja (selain itu, misalnya local di laptop atau komputer kita tidak akan jalan)


## Commit to SCM

Sebelum menyimpan projek di repository anda, ubah terlebih dahulu remote origin nya, dengan menjalankan perintah:

`git remote set-url origin <your ssh git>`


###### Copyright &copy; 2019 YOGYA Group. All rights reserved.

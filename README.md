# Deskripsi

Sistem backend (REST API) dari aplikasi SIDIA. Sistem ini menjembatani data yang diinput melalui aplikasi SIDIA (Frontend) menuju database. Beberapa fitur di aplikasi SIDIA sudah dipasangi endpoint khusus yang mana data yang diinput melalui fitur tersebut akan masuk terlebih dahulu di sistem backend (REST API) ini lalu di manage sebelum masuk secara utuh menuju database utama.

## Demo

Anda dapat melakukan clone proyek REST API ini melalui Github dan mencobanya secara lokal menggunakan Xampp atau server lokal milik anda.

## Prasyarat

Sebelum melakukan clone proyek REST API ini, anda disarankan untuk memiliki alat bantu yang sudah terinstall:

- [php](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/download/)
- [Node.js](https://nodejs.org/en/download/package-manager)
- [npm](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm)

## Instalasi

1. Fork proyek REST API melalui Github.
2. Clone proyek REST API melalui Github.
3. Navigasi kedalam direktori proyek REST API.
4. Lakukan instalasi dependesi yang terdapat pada proyek REST API dengan perintah:
```bash
composer install
```
5. Lakukan re-generate kunci aplikasi (APP_KEY) pada proyek REST API dengan perintah:
```bash
php artisan key:generate
```
6. Lakukan migrasi semua file migration pada proyek REST API dengan perintah:
```bash
php artisan migrate
```
7. Jalankan proyek REST API dengan perintah:
```bash
php artisan serve
```
8. (Opsional) Lakukan update dependesi yang terdapat pada proyek REST API apabila pengembangan proyek tidak dijalankan dalam waktu yang lama atau terdapat update terbaru pada salah satu dependesi di proyek ini dengan perintah:
```bash
composer update
```
<p align="center">
    <h1 align="center">SIPROPEN</h1>
    <br>
</p>


PERSYARATAN
------------

Persyaratan minimum oleh <i>template</i> proyek ini adalah server web yang mendukung PHP versi 5.4.0. ke atas. </br>

Namun dianjurkan pengguna untuk menggunakan PHP versi 5.6.0.

<br>


INSTALLASI
------------

### Install melalui Composer

Jika Anda tidak memiliki [Composer](http://getcomposer.org/), Anda dapat menginstalnya dengan mengikuti instruksi
di [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

Anda kemudian dapat menginstal <i>template</i> proyek ini menggunakan perintah berikut:

~~~
php composer.phar create-project --prefer-dist --stability=dev yiisoft/yii2-app-basic sipropen
~~~

Sekarang Anda dapat mengakses aplikasi melalui URL berikut:

~~~
http://localhost/sipropen/web/
~~~

### Install melalui file arsip

Ekstrak arsip yang dapat diunduh dari [yiiframework.com](http://www.yiiframework.com/download/) ke direktori bernama `sipropen` di bawah root Web (<i>htdocs</i>).

Setel <i>cookie validation key</i> di file `config/web.php` dengan angka rahasia tertentu:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

Sekarang Anda dapat mengakses aplikasi melalui URL berikut:

~~~
http://localhost/sipropen/web/
~~~

<br>

KONFIGURASI
-------------

### Database

Unduh file database yang ada di folder Database, lalu edit file `config/db.php` dengan data database anda, contoh:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yiiblog',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
```

<br>

**Catatan:**
- Yii tidak secara otomatis membuat <i>database</i> untuk Anda, <i>database</i> harus dibuat secara manual sebelum Anda dapat mengaksesnya di Yii.
- Periksa dan edit file lain di direktori `config /` untuk menyesuaikan aplikasi Anda sesuai dengan kebutuhan anda.



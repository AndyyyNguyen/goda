<?php
session_start();

use Cocur\Slugify\Slugify;

require 'vendor/autoload.php';
$slugify = new Slugify();
$router = new AltoRouter();

// import config & connectDB
require 'config.php';
require ABSPATH . 'connectDB.php';

// import models
require ABSPATH . 'bootstrap.php';

//import controller
require ABSPATH_SITE . 'load.php';

// map homepage
// tham số cuối cùng là route
// chạy file index của homecontroller
$router->map('GET', '/', ['HomeController', 'index'], 'home');

// map user details page
$router->map('GET', '/san-pham.html', ['ProductController', 'index'], 'product');

// slug: name sản phẩm, chỉ định làm đẹp 
$router->map('GET', '/san-pham/[*:slug]-[i:id].html', function ($slug, $id) {
    call_user_func_array(['ProductController', 'detail'], [$id]);
}, 'productDetail');

$router->map('GET', '/danh-muc/[*:slug]-[i:categoryId]', function ($slug, $categoryId) {
    call_user_func_array(['ProductController', 'index'], [$categoryId]);
}, 'category');

$router->map('GET', '/search', ['ProductController', 'index'], 'search');

$router->map('GET', '/chinh-sach-doi-tra.html', ['InformationController', 'returnPolicy'], 'returnPolicy');
$router->map('GET', '/chinh-sach-thanh-toan.html', ['InformationController', 'paymentPolicy'], 'paymentPolicy');
$router->map('GET', '/chinh-sach-giao-hang.html', ['InformationController', 'deliveryPolicy'], 'deliveryPolicy');

$router->map('GET', '/lien-he.html', ['ContactController', 'form'], 'contact');

// match current request url
$match = $router->match();
$routeName = $match['name'];

// call closure or throw 404 status
if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    $c = $_GET['c'] ?? 'home';
    $a = $_GET['a'] ?? 'index';
    $str = ucfirst($c) . 'Controller';
    $controller = new $str();
    $controller->$a();
}

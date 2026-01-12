<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RouterController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\Payment\PaypalController;
use App\Http\Controllers\Frontend\CrawlerController;
use App\Http\Controllers\Frontend\AuthController as FeAuthController;
use App\Http\Controllers\Frontend\ProductCatalogueController as FeProductCatalogueController;
use App\Http\Controllers\Frontend\LecturerController as FeLecturerController;
use App\Http\Controllers\Frontend\ContactController as FeContactController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\TagController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/ajax/projects', [HomeController::class, 'ajaxProject'])->name('home.project');

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::get('lien-he.html', [FeContactController::class, 'index'])->name('contact.index');
Route::get('cam-on.html', [FeContactController::class, 'thankYou'])->name('contact.thankyou');
Route::post('contact/save', [FeContactController::class, 'saveContact'])->name('contact.save');
Route::post('contact/save-roadmap', [FeContactController::class, 'saveRoadmapContact'])->name('contact.save.roadmap');
Route::post('contact/save-consultation', [FeContactController::class, 'saveConsultationContact'])->name('contact.save.consultation');

Route::get('cac-truong-dao-tao-tu-xa.html', [App\Http\Controllers\Frontend\School\SchoolCatalogueController::class, 'index'])->name('school.catalogue.index');
Route::get('cac-truong-dao-tao-tu-xa/trang-{page}.html', [App\Http\Controllers\Frontend\School\SchoolCatalogueController::class, 'index'])->name('school.catalogue.page')->where('page', '[0-9]+');

Route::get('cac-nganh-dao-tao-tu-xa.html', [App\Http\Controllers\Frontend\Major\MajorCatalogueController::class, 'index'])->name('fe.major.catalogue.index');
Route::get('cac-nganh-dao-tao-tu-xa/trang-{page}.html', [App\Http\Controllers\Frontend\Major\MajorCatalogueController::class, 'index'])->name('major.catalogue.page')->where('page', '[0-9]+');

Route::get('giao-vien'.config('apps.general.suffix'), [FeLecturerController::class, 'allLecturer'])->name('lecturer.allLecturer');

Route::get('crawler', [CrawlerController::class, 'index'])->name('crawler.index');

Route::get('/thumb', [App\Http\Controllers\ImageResizerController::class, 'resize'])
    ->name('thumb');

Route::get('tim-kiem'.config('apps.general.suffix'), [FeProductCatalogueController::class, 'search'])->name('product.catalogue.search');

Route::get('tim-kiem/trang-{page}'.config('apps.general.suffix'), [FeProductCatalogueController::class, 'search'])->name('product.catalogue.search.page')->where('page', '[0-9]+');

/* CUSTOMER  */
Route::get('customer/login'.config('apps.general.suffix'), [FeAuthController::class, 'index'])->name('fe.auth.login'); 

Route::post('customer/check/login'.config('apps.general.suffix'), [FeAuthController::class, 'login'])->name('fe.auth.dologin');

Route::get('customer/password/forgot'.config('apps.general.suffix'), [FeAuthController::class, 'forgotCustomerPassword'])->name('forgot.customer.password');

Route::get('customer/password/email'.config('apps.general.suffix'), [FeAuthController::class, 'verifyCustomerEmail'])->name('customer.password.email');

Route::get('customer/register'.config('apps.general.suffix'), [FeAuthController::class, 'register'])->name('customer.register');

Route::post('customer/reg'.config('apps.general.suffix'), [FeAuthController::class, 'registerAccount'])->name('customer.reg');

Route::get('customer/password/update'.config('apps.general.suffix'), [FeAuthController::class, 'updatePassword'])->name('customer.update.password');

Route::post('customer/password/change'.config('apps.general.suffix'), [FeAuthController::class, 'changePassword'])->name('customer.password.reset');

Route::get('danh-sach-yeu-thich'.config('apps.general.suffix'), [FeProductCatalogueController::class, 'wishlist'])->name('product.catalogue.wishlist');

Route::get('gio-hang'.config('apps.general.suffix'), [CartController::class, 'checkout'])->name('cart.checkout');

Route::get('thanh-toan'.config('apps.general.suffix'), [CartController::class, 'pay'])->name('cart.pay');

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('tag/{slug}'.config('apps.general.suffix'), [TagController::class, 'index'])->name('post.tag')->where('slug', '[a-zA-Z0-9-]+');

Route::get('{canonical}'.config('apps.general.suffix'), [RouterController::class, 'index'])->name('router.index')->where('canonical', '[a-zA-Z0-9-]+');

Route::get('{canonical}/trang-{page}'.config('apps.general.suffix'), [RouterController::class, 'page'])->name('router.page')->where('canonical', '[a-zA-Z0-9-]+')->where('page', '[0-9]+');

Route::post('cart/create', [CartController::class, 'store'])->name('cart.store');

Route::post('cart/createPay', [CartController::class, 'storePay'])->name('cart.storePay');

Route::get('cart/success'.config('apps.general.suffix'), [CartController::class, 'success'])->name('cart.success');

Route::get('giao-vien/{canonical}'.config('apps.general.suffix'), [FeLecturerController::class, 'index'])->name('lecturer.index');

/* PAYMENT */
Route::get('paypal/success'.config('apps.general.suffix'), [PaypalController::class, 'success'])->name('paypal.success');
Route::get('paypal/cancel'.config('apps.general.suffix'), [PaypalController::class, 'cancel'])->name('paypal.cancel');


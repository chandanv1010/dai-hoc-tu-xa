<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\User\UserController;
use App\Http\Controllers\Backend\User\UserCatalogueController;
use App\Http\Controllers\Backend\User\PermissionController;
use App\Http\Controllers\Backend\Customer\CustomerController;
use App\Http\Controllers\Backend\Customer\CustomerCatalogueController;
use App\Http\Controllers\Backend\Customer\SourceController;
use App\Http\Controllers\Backend\Post\PostCatalogueController;
use App\Http\Controllers\Backend\Post\PostController;
use App\Http\Controllers\Backend\Major\MajorController;
use App\Http\Controllers\Backend\Major\MajorCatalogueController;
use App\Http\Controllers\Backend\School\SchoolController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\SlideController;
use App\Http\Controllers\Backend\WidgetController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\Promotion\PromotionController;
use App\Http\Controllers\Backend\ReviewController;
use App\Http\Controllers\Backend\VoucherController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\LecturerController;
use App\Http\Controllers\Backend\IntroduceController;
use App\Http\Controllers\Backend\Product\ProductCatalogueController;
use App\Http\Controllers\Backend\Product\ProductController;
use App\Http\Controllers\Backend\Attribute\AttributeCatalogueController;
use App\Http\Controllers\Backend\Attribute\AttributeController;
use App\Http\Controllers\Backend\SystemController;
use App\Http\Controllers\Backend\TagController;

/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['admin','locale','backend_default_locale']], function () {
    Route::get('dashboard/index', [DashboardController::class, 'index'])->name('dashboard.index');

    /* USER */
    Route::group(['prefix' => 'user'], function () {
        Route::get('index', [UserController::class, 'index'])->name('user.index');
        Route::get('create', [UserController::class, 'create'])->name('user.create');
        Route::post('store', [UserController::class, 'store'])->name('user.store');
        Route::get('{id}/edit', [UserController::class, 'edit'])->where(['id' => '[0-9]+'])->name('user.edit');
        Route::post('{id}/update', [UserController::class, 'update'])->where(['id' => '[0-9]+'])->name('user.update');
        Route::get('{id}/delete', [UserController::class, 'delete'])->where(['id' => '[0-9]+'])->name('user.delete');
        Route::delete('{id}/destroy', [UserController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('user.destroy');
    });

    Route::group(['prefix' => 'user/catalogue'], function () {
        Route::get('index', [UserCatalogueController::class, 'index'])->name('user.catalogue.index');
        Route::get('create', [UserCatalogueController::class, 'create'])->name('user.catalogue.create');
        Route::post('store', [UserCatalogueController::class, 'store'])->name('user.catalogue.store');
        Route::get('{id}/edit', [UserCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('user.catalogue.edit');
        Route::post('{id}/update', [UserCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('user.catalogue.update');
        Route::get('{id}/delete', [UserCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('user.catalogue.delete');
        Route::delete('{id}/destroy', [UserCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('user.catalogue.destroy');
        Route::get('permission', [UserCatalogueController::class, 'permission'])->name('user.catalogue.permission');
        Route::post('updatePermission', [UserCatalogueController::class, 'updatePermission'])->name('user.catalogue.updatePermission');
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::get('index', [CustomerController::class, 'index'])->name('customer.index');
        Route::get('create', [CustomerController::class, 'create'])->name('customer.create');
        Route::post('store', [CustomerController::class, 'store'])->name('customer.store');
        Route::get('{id}/edit', [CustomerController::class, 'edit'])->where(['id' => '[0-9]+'])->name('customer.edit');
        Route::post('{id}/update', [CustomerController::class, 'update'])->where(['id' => '[0-9]+'])->name('customer.update');
        Route::get('{id}/delete', [CustomerController::class, 'delete'])->where(['id' => '[0-9]+'])->name('customer.delete');
        Route::delete('{id}/destroy', [CustomerController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('customer.destroy');
    });

    Route::group(['prefix' => 'customer/catalogue'], function () {
        Route::get('index', [CustomerCatalogueController::class, 'index'])->name('customer.catalogue.index');
        Route::get('create', [CustomerCatalogueController::class, 'create'])->name('customer.catalogue.create');
        Route::post('store', [CustomerCatalogueController::class, 'store'])->name('customer.catalogue.store');
        Route::get('{id}/edit', [CustomerCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('customer.catalogue.edit');
        Route::post('{id}/update', [CustomerCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('customer.catalogue.update');
        Route::get('{id}/delete', [CustomerCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('customer.catalogue.delete');
        Route::delete('{id}/destroy', [CustomerCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('customer.catalogue.destroy');
        Route::get('permission', [CustomerCatalogueController::class, 'permission'])->name('customer.catalogue.permission');
        Route::post('updatePermission', [CustomerCatalogueController::class, 'updatePermission'])->name('customer.catalogue.updatePermission');
    });

    Route::group(['prefix' => 'language'], function () {
        Route::get('index', [LanguageController::class, 'index'])->name('language.index')->middleware(['admin','locale']);
        Route::get('create', [LanguageController::class, 'create'])->name('language.create');
        Route::post('store', [LanguageController::class, 'store'])->name('language.store');
        Route::get('{id}/edit', [LanguageController::class, 'edit'])->where(['id' => '[0-9]+'])->name('language.edit');
        Route::post('{id}/update', [LanguageController::class, 'update'])->where(['id' => '[0-9]+'])->name('language.update');
        Route::get('{id}/delete', [LanguageController::class, 'delete'])->where(['id' => '[0-9]+'])->name('language.delete');
        Route::delete('{id}/destroy', [LanguageController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('language.destroy');
        Route::get('{id}/switch', [LanguageController::class, 'swicthBackendLanguage'])->where(['id' => '[0-9]+'])->name('language.switch');
        Route::get('{id}/{languageId}/{model}/translate', [LanguageController::class, 'translate'])->where(['id' => '[0-9]+', 'languageId' => '[0-9]+'])->name('language.translate');
        Route::post('storeTranslate', [LanguageController::class, 'storeTranslate'])->name('language.storeTranslate');
    });

    Route::group(['prefix' => 'system'], function () {
        Route::get('index', [SystemController::class, 'index'])->name('system.index');
        Route::post('store', [SystemController::class, 'store'])->name('system.store');
        Route::get('{languageId}/translate', [SystemController::class, 'translate'])->where(['languageId' => '[0-9]+'])->name('system.translate');
        Route::post('{languageId}/saveTranslate', [SystemController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('system.save.translate');
    });

    Route::group(['prefix' => 'introduce'], function () {
        Route::get('index', [IntroduceController::class, 'index'])->name('introduce.index');
        Route::post('store', [IntroduceController::class, 'store'])->name('introduce.store');
        Route::get('{languageId}/translate', [IntroduceController::class, 'translate'])->where(['languageId' => '[0-9]+'])->name('introduce.translate');
        Route::post('{languageId}/saveTranslate', [IntroduceController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('introduce.save.translate');
    });

    Route::group(['prefix' => 'review'], function () {
        Route::get('index', [ReviewController::class, 'index'])->name('review.index');
        Route::get('create', [ReviewController::class, 'create'])->name('review.create');
        Route::post('store', [ReviewController::class, 'store'])->name('review.store');
        Route::get('{id}/delete', [ReviewController::class, 'delete'])->where(['id' => '[0-9]+'])->name('review.delete');
        Route::delete('{id}/destroy', [ReviewController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('review.destroy');
    });

    Route::group(['prefix' => 'menu'], function () {
        Route::get('index', [MenuController::class, 'index'])->name('menu.index');
        Route::get('create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('store', [MenuController::class, 'store'])->name('menu.store');
        Route::get('{id}/edit', [MenuController::class, 'edit'])->where(['id' => '[0-9]+'])->name('menu.edit');
        Route::get('{id}/editMenu', [MenuController::class, 'editMenu'])->where(['id' => '[0-9]+'])->name('menu.editMenu');
        Route::post('{id}/update', [MenuController::class, 'update'])->where(['id' => '[0-9]+'])->name('menu.update');
        Route::get('{id}/delete', [MenuController::class, 'delete'])->where(['id' => '[0-9]+'])->name('menu.delete');
        Route::delete('{id}/destroy', [MenuController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('menu.destroy');
        Route::get('{id}/children', [MenuController::class, 'children'])->where(['id' => '[0-9]+'])->name('menu.children');
        Route::post('{id}/saveChildren', [MenuController::class, 'saveChildren'])->where(['id' => '[0-9]+'])->name('menu.save.children');
        Route::get('{languageId}/{id}/translate', [MenuController::class, 'translate'])->where(['languageId' => '[0-9]+', 'id' => '[0-9]+'])->name('menu.translate');
        Route::post('{languageId}/saveTranslate', [MenuController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('menu.translate.save');
    });

    Route::group(['prefix' => 'post/catalogue'], function () {
        Route::get('index', [PostCatalogueController::class, 'index'])->name('post.catalogue.index');
        Route::get('create', [PostCatalogueController::class, 'create'])->name('post.catalogue.create');
        Route::post('store', [PostCatalogueController::class, 'store'])->name('post.catalogue.store');
        Route::get('{id}/edit', [PostCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('post.catalogue.edit');
        Route::post('{id}/update', [PostCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('post.catalogue.update');
        Route::get('{id}/delete', [PostCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('post.catalogue.delete');
        Route::delete('{id}/destroy', [PostCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('post.catalogue.destroy');
    });

    Route::group(['prefix' => 'post'], function () {
        Route::get('index', [PostController::class, 'index'])->name('post.index');
        Route::get('create', [PostController::class, 'create'])->name('post.create');
        Route::post('store', [PostController::class, 'store'])->name('post.store');
        Route::get('{id}/edit', [PostController::class, 'edit'])->where(['id' => '[0-9]+'])->name('post.edit');
        Route::post('{id}/update', [PostController::class, 'update'])->where(['id' => '[0-9]+'])->name('post.update');
        Route::get('{id}/delete', [PostController::class, 'delete'])->where(['id' => '[0-9]+'])->name('post.delete');
        Route::delete('{id}/destroy', [PostController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('post.destroy');
    });

    Route::group(['prefix' => 'tag'], function () {
        Route::get('index', [TagController::class, 'index'])->name('tag.index');
        Route::get('create', [TagController::class, 'create'])->name('tag.create');
        Route::post('store', [TagController::class, 'store'])->name('tag.store');
        Route::get('{id}/edit', [TagController::class, 'edit'])->where(['id' => '[0-9]+'])->name('tag.edit');
        Route::post('{id}/update', [TagController::class, 'update'])->where(['id' => '[0-9]+'])->name('tag.update');
        Route::get('{id}/delete', [TagController::class, 'delete'])->where(['id' => '[0-9]+'])->name('tag.delete');
        Route::delete('{id}/destroy', [TagController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('tag.destroy');
        Route::get('getAllTags', [TagController::class, 'getAllTags'])->name('tag.getAllTags');
    });

    Route::group(['prefix' => 'major'], function () {
        Route::get('index', [MajorController::class, 'index'])->name('major.index');
        Route::get('create', [MajorController::class, 'create'])->name('major.create');
        Route::post('store', [MajorController::class, 'store'])->name('major.store');
        Route::get('{id}/edit', [MajorController::class, 'edit'])->where(['id' => '[0-9]+'])->name('major.edit');
        Route::post('{id}/update', [MajorController::class, 'update'])->where(['id' => '[0-9]+'])->name('major.update');
        Route::get('{id}/duplicate', [MajorController::class, 'duplicate'])->where(['id' => '[0-9]+'])->name('major.duplicate');
        Route::get('{id}/delete', [MajorController::class, 'delete'])->where(['id' => '[0-9]+'])->name('major.delete');
        Route::delete('{id}/destroy', [MajorController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('major.destroy');
    });

    /* MAJOR CATALOGUE */
    Route::group(['prefix' => 'major/catalogue'], function () {
        Route::get('index', [MajorCatalogueController::class, 'index'])->name('major.catalogue.index');
        Route::get('create', [MajorCatalogueController::class, 'create'])->name('major.catalogue.create');
        Route::post('store', [MajorCatalogueController::class, 'store'])->name('major.catalogue.store');
        Route::get('{id}/edit', [MajorCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('major.catalogue.edit');
        Route::post('{id}/update', [MajorCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('major.catalogue.update');
        Route::get('{id}/delete', [MajorCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('major.catalogue.delete');
        Route::delete('{id}/destroy', [MajorCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('major.catalogue.destroy');
    });

    Route::group(['prefix' => 'school'], function () {
        Route::get('index', [SchoolController::class, 'index'])->name('school.index');
        Route::get('create', [SchoolController::class, 'create'])->name('school.create');
        Route::post('store', [SchoolController::class, 'store'])->name('school.store');
        Route::get('{id}/edit', [SchoolController::class, 'edit'])->where(['id' => '[0-9]+'])->name('school.edit');
        Route::post('{id}/update', [SchoolController::class, 'update'])->where(['id' => '[0-9]+'])->name('school.update');
        Route::get('{id}/duplicate', [SchoolController::class, 'duplicate'])->where(['id' => '[0-9]+'])->name('school.duplicate');
        Route::get('{id}/delete', [SchoolController::class, 'delete'])->where(['id' => '[0-9]+'])->name('school.delete');
        Route::delete('{id}/destroy', [SchoolController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('school.destroy');
    });

    Route::group(['prefix' => 'permission'], function () {
        Route::get('index', [PermissionController::class, 'index'])->name('permission.index');
        Route::get('create', [PermissionController::class, 'create'])->name('permission.create');
        Route::post('store', [PermissionController::class, 'store'])->name('permission.store');
        Route::get('{id}/edit', [PermissionController::class, 'edit'])->where(['id' => '[0-9]+'])->name('permission.edit');
        Route::post('{id}/update', [PermissionController::class, 'update'])->where(['id' => '[0-9]+'])->name('permission.update');
        Route::get('{id}/delete', [PermissionController::class, 'delete'])->where(['id' => '[0-9]+'])->name('permission.delete');
        Route::delete('{id}/destroy', [PermissionController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('permission.destroy');
    });

    Route::group(['prefix' => 'slide'], function () {
        Route::get('index', [SlideController::class, 'index'])->name('slide.index');
        Route::get('create', [SlideController::class, 'create'])->name('slide.create');
        Route::post('store', [SlideController::class, 'store'])->name('slide.store');
        Route::get('{id}/edit', [SlideController::class, 'edit'])->where(['id' => '[0-9]+'])->name('slide.edit');
        Route::post('{id}/update', [SlideController::class, 'update'])->where(['id' => '[0-9]+'])->name('slide.update');
        Route::get('{id}/delete', [SlideController::class, 'delete'])->where(['id' => '[0-9]+'])->name('slide.delete');
        Route::delete('{id}/destroy', [SlideController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('slide.destroy');
    });

    Route::group(['prefix' => 'widget'], function () {
        Route::get('index', [WidgetController::class, 'index'])->name('widget.index');
        Route::get('create', [WidgetController::class, 'create'])->name('widget.create');
        Route::post('store', [WidgetController::class, 'store'])->name('widget.store');
        Route::get('{id}/edit', [WidgetController::class, 'edit'])->where(['id' => '[0-9]+'])->name('widget.edit');
        Route::post('{id}/update', [WidgetController::class, 'update'])->where(['id' => '[0-9]+'])->name('widget.update');
        Route::get('{id}/delete', [WidgetController::class, 'delete'])->where(['id' => '[0-9]+'])->name('widget.delete');
        Route::delete('{id}/destroy', [WidgetController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('widget.destroy');
        Route::get('{languageId}/{id}/translate', [WidgetController::class, 'translate'])->where(['id' => '[0-9]+', 'languageId' => '[0-9]+'])->name('widget.translate');
        Route::post('saveTranslate', [WidgetController::class, 'saveTranslate'])->name('widget.saveTranslate');
    });

    Route::group(['prefix' => 'source'], function () {
        Route::get('index', [SourceController::class, 'index'])->name('source.index');
        Route::get('create', [SourceController::class, 'create'])->name('source.create');
        Route::post('store', [SourceController::class, 'store'])->name('source.store');
        Route::get('{id}/edit', [SourceController::class, 'edit'])->where(['id' => '[0-9]+'])->name('source.edit');
        Route::post('{id}/update', [SourceController::class, 'update'])->where(['id' => '[0-9]+'])->name('source.update');
        Route::get('{id}/delete', [SourceController::class, 'delete'])->where(['id' => '[0-9]+'])->name('source.delete');
        Route::delete('{id}/destroy', [SourceController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('source.destroy');
    });

    Route::group(['prefix' => 'promotion'], function () {
        Route::get('index', [PromotionController::class, 'index'])->name('promotion.index');
        Route::get('create', [PromotionController::class, 'create'])->name('promotion.create');
        Route::post('store', [PromotionController::class, 'store'])->name('promotion.store');
        Route::get('{id}/edit', [PromotionController::class, 'edit'])->where(['id' => '[0-9]+'])->name('promotion.edit');
        Route::post('{id}/update', [PromotionController::class, 'update'])->where(['id' => '[0-9]+'])->name('promotion.update');
        Route::get('{id}/delete', [PromotionController::class, 'delete'])->where(['id' => '[0-9]+'])->name('promotion.delete');
        Route::delete('{id}/destroy', [PromotionController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('promotion.destroy');
    });

    Route::group(['prefix' => 'product/catalogue'], function () {
        Route::get('index', [ProductCatalogueController::class, 'index'])->name('product.catalogue.index');
        Route::get('create', [ProductCatalogueController::class, 'create'])->name('product.catalogue.create');
        Route::post('store', [ProductCatalogueController::class, 'store'])->name('product.catalogue.store');
        Route::get('{id}/edit', [ProductCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('product.catalogue.edit');
        Route::post('{id}/update', [ProductCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('product.catalogue.update');
        Route::get('{id}/delete', [ProductCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('product.catalogue.delete');
        Route::delete('{id}/destroy', [ProductCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('product.catalogue.destroy');
    });

    Route::group(['prefix' => 'product'], function () {
        Route::get('index', [ProductController::class, 'index'])->name('product.index');
        Route::get('create', [ProductController::class, 'create'])->name('product.create');
        Route::post('store', [ProductController::class, 'store'])->name('product.store');
        Route::get('{id}/edit', [ProductController::class, 'edit'])->where(['id' => '[0-9]+'])->name('product.edit');
        Route::post('{id}/update', [ProductController::class, 'update'])->where(['id' => '[0-9]+'])->name('product.update');
        Route::get('{id}/delete', [ProductController::class, 'delete'])->where(['id' => '[0-9]+'])->name('product.delete');
        Route::delete('{id}/destroy', [ProductController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('product.destroy');
    });

    Route::group(['prefix' => 'attribute/catalogue'], function () {
        Route::get('index', [AttributeCatalogueController::class, 'index'])->name('attribute.catalogue.index');
        Route::get('create', [AttributeCatalogueController::class, 'create'])->name('attribute.catalogue.create');
        Route::post('store', [AttributeCatalogueController::class, 'store'])->name('attribute.catalogue.store');
        Route::get('{id}/edit', [AttributeCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.edit');
        Route::post('{id}/update', [AttributeCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.update');
        Route::get('{id}/delete', [AttributeCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.delete');
        Route::delete('{id}/destroy', [AttributeCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.destroy');
    });
    
    Route::group(['prefix' => 'attribute'], function () {
        Route::get('index', [AttributeController::class, 'index'])->name('attribute.index');
        Route::get('create', [AttributeController::class, 'create'])->name('attribute.create');
        Route::post('store', [AttributeController::class, 'store'])->name('attribute.store');
        Route::get('{id}/edit', [AttributeController::class, 'edit'])->where(['id' => '[0-9]+'])->name('attribute.edit');
        Route::post('{id}/update', [AttributeController::class, 'update'])->where(['id' => '[0-9]+'])->name('attribute.update');
        Route::get('{id}/delete', [AttributeController::class, 'delete'])->where(['id' => '[0-9]+'])->name('attribute.delete');
        Route::delete('{id}/destroy', [AttributeController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('attribute.destroy');
    });

    Route::group(['prefix' => 'order'], function () {
        Route::get('index', [OrderController::class, 'index'])->name('order.index');
        Route::get('{id}/detail', [OrderController::class, 'detail'])->where(['id' => '[0-9]+'])->name('order.detail');
    });

    Route::group(['prefix' => 'voucher'], function () {
        Route::get('index', [VoucherController::class, 'index'])->name('voucher.index');
        Route::get('create', [VoucherController::class, 'create'])->name('voucher.create');
        Route::post('store', [VoucherController::class, 'store'])->name('voucher.store');
        Route::get('{id}/edit', [VoucherController::class, 'edit'])->where(['id' => '[0-9]+'])->name('voucher.edit');
        Route::post('{id}/update', [VoucherController::class, 'update'])->where(['id' => '[0-9]+'])->name('voucher.update');
        Route::get('{id}/delete', [VoucherController::class, 'delete'])->where(['id' => '[0-9]+'])->name('voucher.delete');
        Route::delete('{id}/destroy', [VoucherController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('voucher.destroy');
    });

    Route::group(['prefix' => 'report'], function () {
        Route::get('time', [ReportController::class, 'time'])->name('report.time');
        Route::get('product', [ReportController::class, 'product'])->name('report.product');
        Route::get('customer', [ReportController::class, 'customer'])->name('report.customer');
    });

    Route::group(['prefix' => 'contact'], function () {
        Route::get('index', [ContactController::class, 'index'])->name('contact.index');
        Route::get('{id}/delete', [ContactController::class, 'delete'])->where(['id' => '[0-9]+'])->name('contact.delete');
        Route::delete('{id}/destroy', [ContactController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('contact.destroy');
    });
   
    Route::group(['prefix' => 'lecturer'], function () {
        Route::get('index', [LecturerController::class, 'index'])->name('lecturer.index');
        Route::get('create', [LecturerController::class, 'create'])->name('lecturer.create');
        Route::post('store', [LecturerController::class, 'store'])->name('lecturer.store');
        Route::get('{id}/edit', [LecturerController::class, 'edit'])->where(['id' => '[0-9]+'])->name('lecturer.edit');
        Route::post('{id}/update', [LecturerController::class, 'update'])->where(['id' => '[0-9]+'])->name('lecturer.update');
        Route::get('{id}/delete', [LecturerController::class, 'delete'])->where(['id' => '[0-9]+'])->name('lecturer.delete');
        Route::delete('{id}/destroy', [LecturerController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('lecturer.destroy');
    });
});


<?php

use App\Http\Controllers\API\BulkEditController;
use App\Http\Controllers\API\BulkStoreController;
use App\Http\Controllers\API\LinkCheckController;
use App\Http\Controllers\API\LinkController;
use App\Http\Controllers\API\LinkNotesController;
use App\Http\Controllers\API\ListController;
use App\Http\Controllers\API\ListLinksController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\TagLinksController;
use App\Http\Controllers\API\TrashController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('version', fn() => json_encode(['version' => config('app.api_version')]));

Route::prefix('v2')
    ->middleware(['auth:sanctum', 'throttle:' . config('app.api_rate_limit')])
    ->group(function () {

        Route::get('links/check', LinkCheckController::class)
            ->name('api.links.check');

        Route::apiResource('links', LinkController::class)
            ->names([
                'index' => 'api.links.index',
                'show' => 'api.links.show',
                'store' => 'api.links.store',
                'update' => 'api.links.update',
                'destroy' => 'api.links.destroy',
            ]);

        Route::get('links/{link}/notes', LinkNotesController::class)
            ->name('api.links.notes');

        Route::apiResource('lists', ListController::class)
            ->names([
                'index' => 'api.lists.index',
                'show' => 'api.lists.show',
                'store' => 'api.lists.store',
                'update' => 'api.lists.update',
                'destroy' => 'api.lists.destroy',
            ]);

        Route::get('lists/{list}/links', ListLinksController::class)
            ->name('api.lists.links');

        Route::apiResource('tags', TagController::class)
            ->names([
                'index' => 'api.tags.index',
                'show' => 'api.tags.show',
                'store' => 'api.tags.store',
                'update' => 'api.tags.update',
                'destroy' => 'api.tags.destroy',
            ]);

        Route::get('tags/{tag}/links', TagLinksController::class)
            ->name('api.tags.links');

        Route::apiResource('notes', NoteController::class)
            ->names([
                'store' => 'api.notes.store',
                'update' => 'api.notes.update',
                'destroy' => 'api.notes.destroy',
            ])
            ->except(['index', 'show']);

        Route::post('bulk/links', [BulkStoreController::class, 'storeLinks'])->name('api.bulk.links.store');
        Route::post('bulk/lists', [BulkStoreController::class, 'storeLists'])->name('api.bulk.lists.store');
        Route::post('bulk/tags', [BulkStoreController::class, 'storeTags'])->name('api.bulk.tags.store');

        Route::patch('bulk/links', [BulkEditController::class, 'updateLinks'])->name('api.bulk.links.update');
        Route::patch('bulk/lists', [BulkEditController::class, 'updateLists'])->name('api.bulk.lists.update');
        Route::patch('bulk/tags', [BulkEditController::class, 'updateTags'])->name('api.bulk.tags.update');
        Route::delete('bulk/delete', [BulkEditController::class, 'delete'])->name('api.bulk.delete');

        Route::get('search/links', [SearchController::class, 'searchLinks'])
            ->name('api.search.links');
        Route::get('search/tags', [SearchController::class, 'searchByTags'])
            ->name('api.search.tags');
        Route::get('search/lists', [SearchController::class, 'searchByLists'])
            ->name('api.search.lists');

        Route::get('trash/links', [TrashController::class, 'getLinks'])
            ->name('api.trash.links');
        Route::get('trash/lists', [TrashController::class, 'getLists'])
            ->name('api.trash.lists');
        Route::get('trash/tags', [TrashController::class, 'getTags'])
            ->name('api.trash.tags');
        Route::get('trash/notes', [TrashController::class, 'getNotes'])
            ->name('api.trash.notes');

        Route::delete('trash/clear', [TrashController::class, 'clear'])
            ->name('api.trash.clear');
        Route::patch('trash/restore', [TrashController::class, 'restore'])
            ->name('api.trash.restore');
    });

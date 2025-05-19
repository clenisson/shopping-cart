<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GraphQLController;

Route::post('/graphql', [GraphQLController::class, 'handle']);
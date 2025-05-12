// Rota para o Dashboard
Route::middleware('auth:sanctum')->get('/dashboard/stats', 'App\Http\Controllers\Api\DashboardController@stats'); 
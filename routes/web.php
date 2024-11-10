    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\MandiriTransactionController;
use App\Http\Middleware\DetectSuspiciousTransaction;

    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/mandiri/get-auth', function(){
        $mtc = new MandiriTransactionController;
        $authResponse = $mtc->getAuth();
        return view('MandiriView', ['authResponse' => $authResponse]);    
    });
    Route::get('/mandiri/get-transaction/{accountNumber}', function($accountNumber){
        $mtc = new MandiriTransactionController;
        $fromDateTime = request('from');
        $toDateTime = request('to');
        $pageNumber = request('page', 1);
        $transactionResponse = $mtc->getTransactionsHistory($accountNumber, $fromDateTime, $toDateTime, $pageNumber);
        return view('MandiriView', ['transactionResponse'=> $transactionResponse]);
    });

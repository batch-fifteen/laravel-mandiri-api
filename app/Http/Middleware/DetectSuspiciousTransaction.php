<!-- <?php

// app/Http/Middleware/DetectSuspiciousTransaction.php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Mail;
// use App\Models\Transaction;
// use Carbon\Carbon;

// class DetectSuspiciousTransaction
// {
//     // Configure these parameters based on your criteria
//     protected $maxTransactionsPerDay = 100; // Max transactions allowed per day
//     protected $maxTransactionAmount = 10000; // Max amount allowed per transaction
//     protected $velocityLimit = 10; // Max allowed transactions per minute

//     public function handle(Request $request, Closure $next)
//     {
//         $accountNumber = $request->route('accountNumber');
//         $fromDateTime = $request->input('from');
//         $toDateTime = $request->input('to');
//         $pageNumber = $request->input('page', 1);

//         $currentTimestamp = Carbon::now();

//         // Get the transaction count, total amount, and velocity
//         $transactionCount = $this->getTransactionCount($accountNumber, $fromDateTime, $toDateTime);
//         $totalAmount = $this->getTotalTransactionAmount($accountNumber, $fromDateTime, $toDateTime);
//         $velocity = $this->getTransactionVelocity($accountNumber, $fromDateTime, $toDateTime);

//         if ($transactionCount > $this->maxTransactionsPerDay || 
//             $totalAmount > $this->maxTransactionAmount || 
//             $velocity > $this->velocityLimit ||
//             $pageNumber > 10) {
            
//             Log::warning("Suspicious transaction detected for account $accountNumber from $fromDateTime to $toDateTime. 
//                           Transaction count: $transactionCount, Total amount: $totalAmount, 
//                           Velocity: $velocity, Page number: $pageNumber");

//             $this->sendEmailAlert($accountNumber, $fromDateTime, $toDateTime, $transactionCount, $totalAmount, $velocity, $pageNumber);
//         }

//         return $next($request);
//     }

//     protected function getTransactionCount($accountNumber, $fromDateTime, $toDateTime)
//     {
//         // Count transactions in the given period
//         return Transaction::where('account_number', $accountNumber)
//                            ->whereBetween('created_at', [$fromDateTime, $toDateTime])
//                            ->count();
//     }

//     protected function getTotalTransactionAmount($accountNumber, $fromDateTime, $toDateTime)
//     {
//         // Sum the total amount of transactions in the given period
//         return Transaction::where('account_number', $accountNumber)
//                            ->whereBetween('created_at', [$fromDateTime, $toDateTime])
//                            ->sum('amount');
//     }

//     protected function getTransactionVelocity($accountNumber, $fromDateTime, $toDateTime)
//     {
//         // Calculate the transaction velocity (transactions per minute)
//         $transactions = Transaction::where('account_number', $accountNumber)
//                                    ->whereBetween('created_at', [$fromDateTime, $toDateTime])
//                                    ->get();
        
//         $totalMinutes = $transactions->isEmpty() ? 1 : $transactions->pluck('created_at')->diffInMinutes($transactions->last()->created_at);
//         return $transactions->count() / $totalMinutes;
//     }

//     protected function sendEmailAlert($accountNumber, $fromDateTime, $toDateTime, $transactionCount, $totalAmount, $velocity, $pageNumber)
//     {
//         $details = [
//             'subject' => 'Suspicious Transaction Alert',
//             'body' => "Suspicious transaction detected for account $accountNumber from $fromDateTime to $toDateTime. 
//                        Transaction count: $transactionCount, Total amount: $totalAmount, 
//                        Velocity: $velocity, Page number: $pageNumber"
//         ];

//         Mail::raw($details['body'], function ($message) use ($details) {
//             $message->to('admin@example.com')
//                     ->subject($details['subject']);
//         });
//     }
// } 

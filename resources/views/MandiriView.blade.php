<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>

    <h1>Transaction History Response</h1>
    @if(isset($transactionResponse))
        <pre>{{ json_encode($transactionResponse, JSON_PRETTY_PRINT) }}</pre>
    @else
        <p>No transaction history response available.</p>
    @endif
</body>
</html>
<html>
<head>
    <title>Reset Password E-Learning Terpadu Cakra Krisna Manggala</title>
</head>
<body>
    <h1>Reset Password E-Learning Terpadu Cakra Krisna Manggala</h1>
    <p>Silahkan mengakses link berikut untuk memperbarui</p>
    <form action="{{ route('form_reset') }}" method="get">
        <input type="text" name="token" value="{{ $data['token'] }}">
        <button type="submit">Buka Link</button>
    </form>

    <p>Terimakasih, <br><b>Staf IT</b></p>
</body>
</html>

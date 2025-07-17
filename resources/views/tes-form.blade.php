<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tes Form POST</title>
</head>
<body>

    <h1>Halaman Tes Super Minimal</h1>
    <p>Klik tombol di bawah ini.</p>

    <form action="/tes-form-submit" method="POST">
        @csrf
        <button type="submit" style="padding: 10px; font-size: 16px;">Kirim Form POST</button>
    </form>

</body>
</html>
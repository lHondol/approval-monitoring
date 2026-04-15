<!DOCTYPE html>
<html>
<head>
    <title>Notice Pembuatan/Update Sample</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Yth. Ibu/Bapak {{ $name }},</p>

    <p>Terdapat sample dengan <b>SO Number: {{ $so_number }}</b> yang sudah <strong>dibuat/diupdate</strong></p>

    <p>Silahkan klik link di bawah ini untuk membuat proses:</p>

    <p>
        <a href="{{ $link }}" style="padding: 12px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">Create Sample Process</a>
    </p>

    <p>Demikian pemberitahuan ini. Terima kasih atas perhatian dan kerjasamanya.</p>

    <p>Email ini dikirim oleh sistem {{ config('app.name', 'Laravel') }}</p>
</body>
</html>
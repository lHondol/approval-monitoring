<!DOCTYPE html>
<html>
<head>
    <title>Permintaan Konfirmasi Margin</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Yth. Ibu/Bapak {{ $name }},</p>

    @if ($so_number === null)
        <p>Terdapat dokumen yang memerlukan tindakan Anda untuk <strong>konfirmasi margin</strong>.</p>    
    @else
        <p>Terdapat dokumen dengan <b>SO Number: {{ $so_number }}</b> yang memerlukan tindakan Anda untuk <strong>konfirmasi margin</strong>.</p>    
    @endif

    <p>Silahkan klik link di bawah ini untuk menuju ke situs proses penyetujuan:</p>

    <p>
        <a href="{{ $link }}" style="padding: 12px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">Approving Monitoring</a>
    </p>

    <p>Demikian pemberitahuan ini. Terima kasih atas perhatian dan kerjasamanya.</p>

    <p>Email ini dikirim oleh sistem {{ config('app.name', 'Laravel') }}</p>
</body>
</html>
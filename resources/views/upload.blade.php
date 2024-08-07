<!-- resources/views/admin/addons/upload.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Upload Addon</title>
</head>

<body>
    <h1>Upload Addon</h1>
    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif
    <form action="{{ route('addons.upload.post') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="addon" required>
        <button type="submit">Upload Addon</button>
    </form>
</body>

</html>

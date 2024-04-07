<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <h1>CDE Rearsch</h1>
    <h2>Xin chào bạn {{ $data->user->name }}</h2>
    <p>Bạn được giao nhiệm vụ mang tên {{ $data->name }}</p>
    <p>Làm ơn kéo xuống dưới để có thể biết thêm chi tiết về nhiệm vụ này</p>
    <h4>Title : <span>{{ $data->title }}</span></h4>
    <h4>Description : <span>{{ $data->descriptions }}</span></h4>
    @if($data->file)
        <h4>File : </h4>
        <div style="width:500px; display: flex; justify-content: flex-start;">
            @php
                $array = explode('.', $data->file->name);
                $type_file = $array[count($array) - 1];
                $src = 'http://127.0.0.1:8000/FileThumbnails/file.jpg';
                if ($type_file == 'jpg' || $type_file == 'png' || $type_file == 'jpeg') {
                    $url = $data->file->url;
                    $fileContent = Storage::disk('google')->get($url);
                    $src = $FileThumbnails; // you need to provide a valid value for $FileThumbnails
                } elseif ($type_file == 'pdf') {
                    $src = 'http://127.0.0.1:8000/FileThumbnails/pdf.png';
                } elseif ($type_file == 'docx') {
                    $src = 'http://127.0.0.1:8000/FileThumbnails/word.jpg';
                }
            @endphp
            <img src="{{ $src }}" width="150px" alt="">
            <div style="margin-left: 10px;">
                <p>{{ $data->file->name }}</p>
                <p>{{ $data->file->user->name }}</p>
                <p>{{ $data->created_at }}</p>
            </div>
        </div>
    @endif
    <button  type="button" class="btn btn-primary"><a href="">View Todo</a></button>
    <p>Nếu bạn là người mới ở CDE Rearsch hãy đăng kí tại <a href="#">đây</a></p>
    <p>Làm ơn không trả lời tin email này. Vì nó là email tự động được gửi khi có người được mời vào dự án</p>
</body>
</html>

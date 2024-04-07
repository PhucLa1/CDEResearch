<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <h1>CDE Rearsch</h1>
    <h2>Hi {{ $data->file->user->name }}</h2>
    <p>Bạn được giao nhiệm vụ mang tên {{ $data->title }}</p>
    <p>Làm ơn kéo xuống dưới để có thể biết thêm chi tiết về nhiệm vụ này</p>
    <button  type="button" class="btn btn-outline-primary"><a href="http://127.0.0.1:8000/api/teams/join/{{ $data['project']->id }}/{{ $data['userReceive']->id }}">Tham gia</a></button>
    <p>Nếu bạn là người mới ở CDE Rearsch hãy đăng kí tại <a href="#">đây</a></p>
    <p>Làm ơn không trả lời tin email này. Vì nó là email tự động được gửi khi có người được mời vào dự án</p>
</body>
<script>
</script>

</html>

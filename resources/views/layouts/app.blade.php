<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scraping</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Scraping Mercado Livre</a>
            <div class="ms-auto d-flex">
                <form class="d-flex me-2" id="scrapeForm" method="GET">
                    @csrf
                    <button class="btn btn-success" type="button" id="scrapeBtn">Realizar Scraping</button>
                </form>
                <form class="d-flex" id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="button" id="deleteBtn">Apagar Todos os Itens</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $('#scrapeBtn').on('click', function() {
          $.ajax({
            url: "{{ route('products.scrape') }}",
            type: 'POST',
            success: function(response) {
              alert('Scraping realizado com sucesso!');
                    location.reload();
                },
                error: function(response) {
                    alert('Erro ao realizar scraping.');
                }
            });
        });

        $('#deleteBtn').on('click', function() {
            $.ajax({
                url: "{{ route('products.deleteAll') }}",
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Todos os itens foram apagados com sucesso!');
                    location.reload();
                },
                error: function(response) {
                }
            });
        });
    </script>
</body>
</html>

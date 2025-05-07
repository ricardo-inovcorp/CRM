<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin-bottom: 5px;
            color: #333;
        }
        .header p {
            margin-top: 5px;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Data de geração: {{ $data_geracao }}</p>
    </div>

    <div class="info">
        <p><strong>Filtros aplicados:</strong> Estado: {{ ucfirst($filtros['estado']) }}</p>
        <p><strong>Total de entidades:</strong> {{ $total }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>NIF</th>
                <th>Estado</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Contactos</th>
                <th>Atividades</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entidades as $entidade)
            <tr>
                <td>{{ $entidade->id }}</td>
                <td>{{ $entidade->nome }}</td>
                <td>{{ $entidade->nif }}</td>
                <td>{{ $entidade->estado }}</td>
                <td>{{ $entidade->email }}</td>
                <td>{{ $entidade->telefone }}</td>
                <td>{{ $entidade->contactos_count }}</td>
                <td>{{ $entidade->atividades_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Relatório gerado por CRM - Sistema de Gestão de Relacionamento com Clientes</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 
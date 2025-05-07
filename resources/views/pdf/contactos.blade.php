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
        .entity-group {
            margin-top: 15px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .entity-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Data de geração: {{ $data_geracao }}</p>
    </div>

    <div class="info">
        <p><strong>Filtros aplicados:</strong> 
            Estado: {{ ucfirst($filtros['estado']) }}
            @if($filtros['entidade_id'])
            | Entidade: #{{ $filtros['entidade_id'] }}
            @endif
        </p>
        <p><strong>Total de contactos:</strong> {{ $total }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Entidade</th>
                <th>Função</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Estado</th>
                <th>Atividades</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contactos as $contacto)
            <tr>
                <td>{{ $contacto->id }}</td>
                <td>{{ $contacto->nome }}</td>
                <td>{{ $contacto->entidade->nome }}</td>
                <td>{{ $contacto->funcao->nome ?? 'N/A' }}</td>
                <td>{{ $contacto->email }}</td>
                <td>{{ $contacto->telefone }}</td>
                <td>{{ $contacto->estado }}</td>
                <td>{{ $contacto->atividades_count }}</td>
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
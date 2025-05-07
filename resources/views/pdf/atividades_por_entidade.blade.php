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
        .chart-container {
            margin-top: 20px;
            margin-bottom: 20px;
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
            Período: {{ \Carbon\Carbon::parse($filtros['data_inicio'])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($filtros['data_fim'])->format('d/m/Y') }}
        </p>
        <p><strong>Total de entidades com atividades:</strong> {{ $total_entidades }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Entidade</th>
                <th>Total de Atividades</th>
                <th>Duração Total (min)</th>
                <th>Média (min/atividade)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atividades_por_entidade as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->nome }}</td>
                <td>{{ $item->total_atividades }}</td>
                <td>{{ $item->duracao_total ?? 0 }}</td>
                <td>{{ $item->duracao_total ? round($item->duracao_total / $item->total_atividades, 1) : 0 }}</td>
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
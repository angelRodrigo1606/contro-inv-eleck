<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de {{ $type === 'entry' ? 'entradas' : 'salidas' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Reporte de {{ $type === 'entry' ? 'entradas' : 'salidas' }}</h2>
    <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Cantidad</th>
                @if($type === 'entry')
                    <th>Proveedor</th>
                @endif
                <th>Usuario</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $movement->product->name }}</td>
                    <td>{{ $movement->product->category->name }}</td>
                    <td>{{ $movement->quantity }}</td>
                    @if($type === 'entry')
                        <td>{{ $movement->product->supplier->name }}</td>
                    @endif
                    <td>{{ $movement->user->name }}</td>
                    <td>{{ $movement->reference }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

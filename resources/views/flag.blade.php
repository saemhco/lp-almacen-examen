<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Almacén</title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: Georgia, serif;
                background: #f4f1ea;
                color: #1c1915;
            }
            main {
                width: min(44rem, calc(100% - 2rem));
                padding: 2.5rem 2rem;
                background: #fff;
                border: 1px solid #d9d3c7;
                box-sizing: border-box;
            }
            p {
                margin: 0 0 1.5rem;
                font-size: 1.15rem;
                line-height: 1.5;
            }
            code {
                display: block;
                box-sizing: border-box;
                max-width: 100%;
                padding: 1rem 1.25rem;
                font-family: ui-monospace, monospace;
                font-size: 1.35rem;
                line-height: 1.4;
                overflow-wrap: anywhere;
                background: #1c1915;
                color: #f4f1ea;
                user-select: all;
            }
        </style>
    </head>
    <body>
        <main>
            <p>{{ $mensaje }}</p>
            @if ($flag !== null)
                <code>{{ $flag }}</code>
            @endif
        </main>
    </body>
</html>

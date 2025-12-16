{{-- resources/views/menu_publico.blade.php --}}
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Carta - Menú Público</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> {{-- tu css global --}}
    <style>
        :root {
            --color-primary: #0A3A5F;
            /* Azul oscuro del logo */
            --color-secondary: #4DA3D9;
            /* Azul claro del logo */
            --color-white: #ffffff;
            --color-background: #F1F7FB;
            /* Fondo azul grisáceo suave */
            --color-dark: #0A3A5F;
            --color-dark-variant: #5f7b92;
            --box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background: var(--color-background);
            color: var(--color-dark);
        }

        .container {
            max-width: 1100px;
            margin: 1.6rem auto;
            padding: 1rem 1.2rem;
        }

        /* LIBRETA / TARJETA */
        .menu-notebook {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(10, 58, 95, 0.10);
            padding: 1.2rem;
            border: 2px solid #d9e6f2;
        }

        .menu-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.6rem 0;
        }

        .brand {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .brand img {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            object-fit: contain;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background: #fff;
            padding: 6px;
        }

        .brand h1 {
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            gap: 0.6rem;
            align-items: center;
            color: var(--color-primary);
        }

        .brand small {
            display: block;
            font-size: 0.78rem;
            color: var(--color-secondary);
            font-weight: 600;
        }

        /* BOTONES DE CATEGORÍA */
        .categories {
            display: flex;
            gap: 0.6rem;
            flex-wrap: nowrap;
            overflow: auto;
            padding: 0.6rem 0.2rem;
            margin-top: 0.6rem;
        }

        .cat-btn {
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            background: #e8f2fa;
            color: var(--color-primary);
            border: 1px solid #c3d7e6;
            cursor: pointer;
            white-space: nowrap;
            font-size: 0.95rem;
            font-weight: 500;
            transition: 0.2s;
        }

        .cat-btn:hover {
            background: #d7eaf9;
        }

        .cat-btn.active {
            background: var(--color-primary);
            color: var(--color-white);
            border-color: transparent;
        }

        /* SECCIONES DE CATEGORÍA */
        .menu-sections {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        .section {
            background: var(--color-white);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: var(--box-shadow);
            border: 1px solid #d5e3ef;
        }

        .section h2 {
            margin: 0 0 0.4rem 0;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--color-primary);
        }

        .section .desc {
            font-size: 0.92rem;
            color: var(--color-dark-variant);
            margin-bottom: 0.6rem;
        }

        /* ÍTEMS */
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 0.9rem;
        }

        .item-card {
            padding: 0.8rem;
            border-radius: 10px;
            background: white;
            border: 1px solid #e1edf7;
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            min-height: 90px;
            position: relative;
            box-shadow: 0 4px 14px rgba(10, 58, 95, 0.07);
        }

        .item-name {
            font-weight: 600;
            font-size: 0.98rem;
            color: var(--color-primary);
        }

        .item-desc {
            font-size: 0.9rem;
            color: var(--color-dark-variant);
            margin: 0;
        }

        .item-price {
            color: var(--color-secondary);
            font-weight: 700;
            font-size: 1rem;
        }

        .menu-empty {
            text-align: center;
            padding: 2rem;
            color: var(--color-dark-variant);
        }

        /* imagenes de item: ajustar al contenedor y mantener crop */
        .item-image-wrapper {
            width: 100%;
            aspect-ratio: 16/9;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 0.6rem;
            background: #f6f8fa;
        }

        .item-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        /* RESPONSIVE */
        @media(max-width:700px) {
            .menu-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.6rem;
            }

            .items-grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 0.6rem;
            }
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="menu-notebook">
            <header class="menu-header">
                <div class="brand">
                    {{-- Asegúrate de enviar $restaurante->logo o usa favicon --}}
                    <img src="{{ $restaurante->logo ?? asset('favicon.ico') }}" alt="Logo Restaurante">
                    <div>
                        <h1>Carta pública <small>| {{ $restaurante->nombre ?? 'Restaurante' }}</small></h1>
                        <p style="margin:0; font-size:0.88rem; color:var(--color-dark-variant)">Solo mostramos las
                            categorías e ítems seleccionados por el administrador.</p>
                    </div>
                </div>

            </header>

            {{-- BOTONES DE CATEGORÍAS PARA FILTRAR CLIENT-SIDE --}}
            <nav class="categories" id="categories">
                <button class="cat-btn active" data-id="all">Todas</button>
                @foreach ($categorias as $cat)
                    <button class="cat-btn" data-id="{{ $cat->id }}">{{ $cat->nombre }}</button>
                @endforeach
            </nav>

            <main class="menu-sections" id="menu-sections">
                @forelse($categorias as $cat)
                    {{-- Cada categoría ya debe traer solo los items visibles --}}
                    <section class="section" data-cat-id="{{ $cat->id }}">
                        <h2>🍴 {{ $cat->nombre }}</h2>
                        @if (!empty($cat->descripcion))
                            <div class="desc">{{ $cat->descripcion }}</div>
                        @endif

                        @if ($cat->items && $cat->items->count())
                            <div class="items-grid">
                                @foreach ($cat->items as $item)
                                    @php
                                        $img = $item->imagen_url ?? null;
                                        if (! $img && ! empty($item->imagen)) {
                                            $img = \Illuminate\Support\Facades\Storage::url($item->imagen);
                                        }
                                    @endphp
                                    <article class="item-card" data-item-id="{{ $item->id }}" data-cat="{{ $cat->id }}">
                                        @if($img)
                                            <div class="item-image-wrapper">
                                                <img src="{{ $img }}" alt="{{ $item->nombre }}">
                                            </div>
                                        @endif
                                        <div class="item-top">
                                            <div>
                                                <div class="item-name">{{ $item->nombre }}</div>
                                                <div class="item-cat" style="display:none;">{{ $cat->nombre }}</div>
                                            </div>
                                            <div class="item-price">${{ number_format($item->precio, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <p class="item-desc">{{ $item->descripcion }}</p>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="menu-empty">No hay ítems disponibles en esta categoría.</div>
                        @endif
                    </section>
                @empty
                    <div class="menu-empty">El menú público aún no tiene categorías visibles.</div>
                @endforelse
            </main>
        </div>
    </div>

    <script>
        // Filtrado simple del cliente: oculta/mostrar secciones por categoría
        (function() {
            const catBtns = document.querySelectorAll('.cat-btn');
            const sections = document.querySelectorAll('.section');

            function setActiveButton(btn) {
                catBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            function showAll() {
                sections.forEach(s => s.style.display = '');
            }

            function filterByCat(id) {
                sections.forEach(s => {
                    if (String(s.dataset.catId) === String(id)) {
                        s.style.display = '';
                    } else {
                        s.style.display = 'none';
                    }
                });
            }

            catBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    setActiveButton(btn);
                    if (id === 'all') {
                        showAll();
                    } else {
                        filterByCat(id);
                        // Si la categoría no tiene items, el servidor ya muestra el mensaje de vacío
                    }
                    // scroll suave a la sección superior
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
        })();
    </script>
</body>

</html>

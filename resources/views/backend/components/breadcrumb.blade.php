{{-- Puedes guardar esto como un partial: resources/views/components/breadcrumb.blade.php --}}
<nav aria-label="breadcrumb">
    
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm align-items-center col-12">
        
    <li class="breadcrumb-item">
            <a href="{{ route('home') }}" class="text-decoration-none text-muted fw-bold">
                <i class="fas fa-home me-1"></i> Inicio
            </a>
        </li>

        @isset($section)

            <li class="breadcrumb-item">
                <a href="{{ route($section['route']) }}" class="text-decoration-none text-success fw-bold">
                    <i class="{{ $section['icon'] }} me-1"></i> {{ $section['label'] }}
                </a>
            </li>

        @endisset

    </ol>
    
</nav>
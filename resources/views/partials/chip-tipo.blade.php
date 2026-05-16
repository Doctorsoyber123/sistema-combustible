@php
    $chipMap = [
        'Retroexcavadora'  => 'chip-amber',
        'Camion'           => 'chip-blue',
        'Camión'           => 'chip-blue',
        'Volquete'         => 'chip-blue',
        'Cargador frontal' => 'chip-green',
        'Motoniveladora'   => 'chip-orange',
        'Otro'             => 'chip-red',
    ];
    $chipClass = $chipMap[$tipo] ?? 'chip-gray';
@endphp
<span class="chip {{ $chipClass }}">{{ $tipo }}</span>

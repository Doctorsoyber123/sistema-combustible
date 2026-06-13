@php
    $isEditing = old('_edit_id') == $c->id;
    $vehSel    = $isEditing ? old('vehiculo_id', $c->vehiculo_id) : $c->vehiculo_id;
    $tramoSel  = $isEditing ? old('tramo_id', $c->tramo_id) : $c->tramo_id;
    $fechaSel  = $isEditing ? old('fecha', $c->fecha->format('Y-m-d')) : $c->fecha->format('Y-m-d');
@endphp
<div class="modal-overlay" id="modal-edit-consumo-{{ $c->id }}">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="ti ti-edit"></i> Editar consumo &mdash; {{ $c->fecha->format('d/m/Y') }} / {{ $c->vehiculo->codigo ?? '-' }}</div>
            <button type="button" class="modal-close" onclick="closeModal('modal-edit-consumo-{{ $c->id }}')"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('consumos.update', $c) }}" method="POST" data-confirm="¿Guardar los cambios de este consumo?">
            @csrf
            @method('PUT')
            <input type="hidden" name="_edit_id" value="{{ $c->id }}">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Vehiculo</label>
                        <select name="vehiculo_id" required>
                            @foreach($vehiculos as $v)
                                <option value="{{ $v->id }}" @selected($vehSel == $v->id)>{{ $v->codigo }} &mdash; {{ $v->tipo }}</option>
                            @endforeach
                        </select>
                        @if($isEditing) @error('vehiculo_id') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Tramo</label>
                        <select name="tramo_id" required>
                             @foreach($tramos as $t)
                                 <option value="{{ $t->id }}"
                                         data-galones="{{ $t->galones ?? '' }}"
                                         @selected($tramoSel == $t->id)>{{ $t->nombre }}</option>
                             @endforeach
                        </select>
                        @if($isEditing) @error('tramo_id') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:6px">
                            Galones usados
                            <span style="font-size:10px;font-weight:500;padding:1px 7px;
                                         border-radius:20px;background:#fff4e6;color:#c06000;
                                         border:1px solid #f5c589">
                                <i class="ti ti-arrows-transfer-down" style="font-size:10px"></i> del tramo
                            </span>
                        </label>
                        <input type="number" name="galones" step="0.01" min="0.01"
                               value="{{ $isEditing ? old('galones', $c->galones) : $c->galones }}"
                               readonly required
                               style="background:var(--surface2,#f5f5f3);cursor:not-allowed;color:var(--text2)">
                        @if($isEditing) @error('galones') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="fecha" value="{{ $fechaSel }}" required>
                        @if($isEditing) @error('fecha') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Operador / Chofer</label>
                        <input type="text" name="operador" list="trabajadores-list" autocomplete="off"
                               value="{{ $isEditing ? old('operador', $c->operador) : $c->operador }}"
                               placeholder="Escribe para buscar...">
                        @if($isEditing) @error('operador') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <input type="text" name="observaciones" value="{{ $isEditing ? old('observaciones', $c->observaciones) : $c->observaciones }}">
                        @if($isEditing) @error('observaciones') <span class="field-error">{{ $message }}</span> @enderror @endif
                    </div>
                </div>
                <hr>
                {{-- Toggle boletas --}}
                <label style="display:flex;align-items:center;gap:10px;padding:14px 20px;
                                cursor:pointer;user-select:none" for="chk-boletas-{{ $c->id }}"
                       onmouseover="this.style.background='var(--surface2)'"
                       onmouseout="this.style.background='none'">
                    <div style="position:relative;width:36px;height:20px;flex-shrink:0">
                        <input type="checkbox" id="chk-boletas-{{ $c->id }}" onchange="toggleBoletasEdit(this, {{ $c->id }})"
                               style="opacity:0;width:0;height:0;position:absolute" @checked($c->boletas->isNotEmpty())>
                        <div id="switch-track-{{ $c->id }}"
                             style="position:absolute;inset:0;background:{{ $c->boletas->isNotEmpty() ? '#e85d04' : 'var(--border)' }};
                                    border-radius:20px;transition:.2s"></div>
                        <div id="switch-thumb-{{ $c->id }}"
                             style="position:absolute;width:14px;height:14px;left:{{ $c->boletas->isNotEmpty() ? '19px' : '3px' }};top:3px;
                                    background:#fff;border-radius:50%;transition:.2s"></div>
                    </div>
                    <span style="font-size:13px;font-weight:500;display:flex;align-items:center;gap:6px">
                        <i class="ti ti-receipt" style="font-size:16px;color:var(--text2)"></i>
                        Asociar boletas
                        <span id="boletas-count-{{ $c->id }}"
                              style="display:{{ $c->boletas->isNotEmpty() ? 'inline' : 'none' }};font-size:11px;padding:2px 8px;border-radius:20px;
                                     background:#e6f1fb;color:#185fa5">{{ $c->boletas->count() }}</span>
                    </span>
                    <span style="font-size:11px;color:var(--text3);margin-left:auto">Opcional</span>
                </label>

                {{-- Sección boletas (oculta por defecto si no hay boletas) --}}
                <div id="seccion-boletas-{{ $c->id }}" style="display:{{ $c->boletas->isNotEmpty() ? 'block' : 'none' }};padding:0 20px 20px">
                    <div style="background:var(--surface2,#f8f8f6);border:0.5px solid var(--border);
                                border-radius:var(--radius-lg,12px);padding:12px">

                        <div id="boletas-list-{{ $c->id }}" style="display:flex;flex-direction:column;gap:6px">
                            <div id="boletas-empty-{{ $c->id }}"
                                 style="text-align:center;padding:12px 0;color:var(--text3);font-size:12px;display:{{ $c->boletas->isEmpty() ? 'block' : 'none' }}">
                                <i class="ti ti-receipt-off" style="font-size:20px;display:block;margin-bottom:4px"></i>
                                Sin boletas aún
                            </div>
                            @foreach($c->boletas as $b)
                                <div class="boleta-item" style="display:flex;align-items:center;gap:8px;padding:7px 10px;
                                     background:var(--surface,#fff);border-radius:var(--radius,8px);
                                     border:0.5px solid var(--border)">
                                    <i class="ti ti-link" style="font-size:15px;color:var(--text2);flex-shrink:0"></i>
                                    <div style="flex:1">
                                        <div style="font-size:13px;font-weight:500">{{ $b->numero_boleta }}</div>
                                        <div style="font-size:11px;color:var(--text3)">{{ $b->proveedor }} &middot; {{ number_format($b->galones,2) }} gal</div>
                                    </div>
                                    <button type="button" onclick="removeExistenteEdit({{ $loop->index }}, {{ $c->id }})"
                                            style="border:none;background:none;cursor:pointer;padding:2px 6px;
                                                   border-radius:var(--radius,8px);color:var(--text3);font-size:15px"
                                            onmouseover="this.style.color='#a32d2d'"
                                            onmouseout="this.style.color='var(--text3)'">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div style="height:0.5px;background:var(--border);margin:10px 0"></div>

                        <div style="display:flex;gap:8px">
                            <button type="button" class="btn" style="flex:1" onclick="toggleBoletaFormEdit('nueva', {{ $c->id }})">
                                <i class="ti ti-plus"></i> Nueva boleta
                            </button>
                            <button type="button" class="btn" style="flex:1" onclick="toggleBoletaFormEdit('existente', {{ $c->id }})">
                                <i class="ti ti-link"></i> Boleta existente
                            </button>
                        </div>

                        {{-- Formulario inline: Nueva boleta --}}
                        <div id="form-boleta-nueva-{{ $c->id }}"
                             style="display:none;margin-top:10px;background:var(--surface,#fff);
                                    border:0.5px solid var(--border);border-radius:var(--radius,8px);padding:12px">
                            <div style="font-size:11px;font-weight:500;color:var(--text3);
                                        text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">
                                Crear nueva boleta
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="text" id="nb_numero_{{ $c->id }}" placeholder="001-00123456">
                                </div>
                                <div class="form-group">
                                    <label>Proveedor</label>
                                    <input type="text" id="nb_proveedor_{{ $c->id }}" placeholder="Petroperu, Primax…">
                                </div>
                                <div class="form-group">
                                    <label>Galones</label>
                                    <input type="number" step="0.01" id="nb_galones_{{ $c->id }}" placeholder="0.00">
                                </div>
                                <div class="form-group">
                                    <label>Precio unitario</label>
                                    <input type="number" step="0.01" id="nb_precio_{{ $c->id }}" placeholder="0.00">
                                </div>
                                <div class="form-group" style="grid-column:1/-1">
                                    <label>Fecha</label>
                                    <input type="date" id="nb_fecha_{{ $c->id }}" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px">
                                <button type="button" class="btn" onclick="toggleBoletaFormEdit('nueva', {{ $c->id }})">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="saveNewBoletaEdit({{ $c->id }})">Guardar boleta</button>
                            </div>
                        </div>

                        {{-- Formulario inline: Boleta existente --}}
                        <div id="form-boleta-existente-{{ $c->id }}"
                             style="display:none;margin-top:10px;background:var(--surface,#fff);
                                    border:0.5px solid var(--border);border-radius:var(--radius,8px);padding:12px">
                            <div style="font-size:11px;font-weight:500;color:var(--text3);
                                        text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">
                                Asociar boleta existente
                            </div>

                            {{-- Buscador --}}
                            <div style="position:relative;margin-bottom:8px">
                                <i class="ti ti-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text3);font-size:14px;pointer-events:none"></i>
                                <input type="text" id="ex_search_{{ $c->id }}"
                                       placeholder="Buscar por número, proveedor…"
                                       autocomplete="off"
                                       oninput="filterBoletasExistentesEdit({{ $c->id }})"
                                       style="width:100%;padding:7px 10px 7px 32px;box-sizing:border-box">
                            </div>

                            {{-- Lista scrollable --}}
                            <div id="ex_lista_{{ $c->id }}"
                                 style="max-height:180px;overflow-y:auto;display:flex;flex-direction:column;
                                        gap:4px;border:0.5px solid var(--border);border-radius:var(--radius,8px);
                                        padding:6px;background:var(--surface2,#f8f8f6)">
                                @forelse($boletasDisponibles ?? [] as $b)
                                    <div class="ex-boleta-row"
                                         data-id="{{ $b->id }}"
                                         data-numero="{{ $b->numero_boleta }}"
                                         data-proveedor="{{ $b->proveedor }}"
                                         data-galones="{{ $b->galones }}"
                                         data-search="{{ strtolower($b->numero_boleta . ' ' . $b->proveedor) }}"
                                         data-hidden="{{ $c->boletas->contains($b->id) ? '1' : '0' }}"
                                         onclick="selectBoletaExistenteEdit(this, {{ $c->id }})"
                                         style="display:{{ $c->boletas->contains($b->id) ? 'none' : 'flex' }};align-items:center;gap:10px;padding:7px 10px;
                                                border-radius:calc(var(--radius,8px) - 2px);
                                                cursor:pointer;background:var(--surface,#fff);
                                                border:0.5px solid transparent;transition:.15s">
                                        <i class="ti ti-receipt" style="font-size:15px;color:var(--text3);flex-shrink:0"></i>
                                        <div style="flex:1;min-width:0">
                                            <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                                {{ $b->numero_boleta }}
                                            </div>
                                            <div style="font-size:11px;color:var(--text3)">
                                                {{ $b->proveedor }} &middot; {{ number_format($b->galones, 2) }} gal
                                            </div>
                                        </div>
                                        <i class="ti ti-circle-check ex-check" style="font-size:17px;color:transparent"></i>
                                    </div>
                                @empty
                                    <div style="text-align:center;padding:16px 0;color:var(--text3);font-size:12px">
                                        <i class="ti ti-receipt-off" style="font-size:20px;display:block;margin-bottom:4px"></i>
                                        Sin boletas disponibles
                                    </div>
                                @endforelse
                            </div>

                            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px">
                                <button type="button" class="btn" onclick="toggleBoletaFormEdit('existente', {{ $c->id }})">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="saveExistingBoletaEdit({{ $c->id }})">Asociar</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="hidden-new-boletas-{{ $c->id }}"></div>
                <div id="hidden-boletas-existing-{{ $c->id }}"></div>

                <script>
                window.editModalsState = window.editModalsState || {};
                window.editModalsState[{{ $c->id }}] = {
                    boletasNuevas: [],
                    boletasExistentes: [
                        @foreach($c->boletas as $b)
                        {
                            id: {{ $b->id }},
                            numero: "{{ $b->numero_boleta }}",
                            proveedor: "{{ $b->proveedor }}",
                            galones: {{ $b->galones ?? 0 }},
                            total: {{ $b->total ?? ($b->galones * $b->precio_galon ?? 0) }}
                        },
                        @endforeach
                    ]
                };
                // Initial sync to ensure that if nothing is changed but form is submitted, existing boletas stay associated
                setTimeout(() => {
                    syncHiddenInputsEdit({{ $c->id }});
                }, 50);
                </script>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeModal('modal-edit-consumo-{{ $c->id }}')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<div id="orders-grid" class="menu-grid">
    @if (isset($comandas) && $comandas->count())
        @foreach ($comandas as $comanda)
            <div class="order-card estado-{{ strtolower($comanda->estado ?? 'pendiente') }}" 
                data-order-id="{{ $comanda->id }}" data-estado="{{ $comanda->estado ?? 'pendiente' }}">
                <div class="card-content">
                    <div class="card-header">
                        <h3>Mesa {{ $comanda->mesa->nombre ?? 'N/A' }}</h3>
                        <span class="time-badge" data-created-at="{{ $comanda->created_at->toIso8601String() }}">{{ $comanda->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="order-items" style="margin:1rem 0; max-height:200px; overflow-y:auto;">
                        @forelse ($comanda->detalles as $detalle)
                            <div class="order-item"
                                style="padding:0.75rem; border-left:3px solid #3b82f6; margin-bottom:0.5rem; background:#f8fafc;">
                                <div style="display:flex; justify-content:space-between; align-items:start;">
                                    <div>
                                        <strong>{{ $detalle->item->nombre ?? 'Item' }}</strong>
                                        <p style="font-size:0.9rem; color:#64748b; margin:0.25rem 0 0 0;">
                                            Cantidad: {{ $detalle->cantidad }}
                                        </p>
                                        @if ($detalle->observaciones)
                                            <p style="font-size:0.85rem; color:#f59e0b; margin:0.25rem 0 0 0; font-weight:500;">
                                                📝 {{ $detalle->observaciones }}
                                            </p>
                                        @endif
                                    </div>
                                    <span class="item-status"
                                        style="padding:0.3rem 0.6rem; border-radius:4px; font-size:0.8rem; background:#e2e8f0;">
                                        {{ $detalle->estado ?? 'pendiente' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p style="color:#94a3b8;">Sin detalles</p>
                        @endforelse
                    </div>

                    @if ($comanda->observaciones_global)
                        <div style="background:#fef3c7; padding:0.75rem; border-radius:6px; margin:0.75rem 0; border-left:3px solid #f59e0b;">
                            <p style="font-size:0.9rem; margin:0; color:#92400e;">
                                <strong>Nota:</strong> {{ $comanda->observaciones_global }}
                            </p>
                        </div>
                    @endif

                    <div class="card-footer"
                        style="display:flex; justify-content:space-between; align-items:center; margin-top:1rem;">
                        <span class="status estado-{{ strtolower($comanda->estado ?? 'pendiente') }}"
                            style="padding:0.4rem 0.8rem; border-radius:6px;">
                            {{ ucfirst($comanda->estado ?? 'Pendiente') }}
                        </span>
                    </div>

                    <div class="card-actions" style="margin-top:1rem; display:flex; gap:0.5rem;">
                        @if (($comanda->estado ?? 'pendiente') !== 'en_preparacion')
                            <button class="btn-action" wire:click="markPreparing({{ $comanda->id }})"
                                style="flex:1; padding:0.6rem; border:none; border-radius:6px; background:#3b82f6; color:#fff; cursor:pointer;">
                                <span class="material-icons-sharp" style="font-size:1rem;">schedule</span>
                                En Preparación
                            </button>
                        @endif

                        @if (($comanda->estado ?? '') === 'en_preparacion')
                            <button class="btn-action" wire:click="markReady({{ $comanda->id }})"
                                style="flex:1; padding:0.6rem; border:none; border-radius:6px; background:#10b981; color:#fff; cursor:pointer;">
                                <span class="material-icons-sharp" style="font-size:1rem;">check_circle</span>
                                Marcar Listo
                            </button>
                        @endif

                        <button class="btn-action" onclick="viewOrderDetails({{ $comanda->id }})"
                            style="flex:1; padding:0.6rem; border:none; border-radius:6px; background:#8b5cf6; color:#fff; cursor:pointer;">
                            <span class="material-icons-sharp" style="font-size:1rem;">visibility</span>
                            Detalles
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div style="grid-column:1/-1; text-align:center; padding:2rem;">
            <span class="material-icons-sharp" style="font-size:3rem; color:#cbd5e1;">inbox</span>
            <p style="color:#64748b; margin-top:1rem;">No hay órdenes pendientes</p>
        </div>
    @endif
</div>

<div wire:poll>
    <div class="flex items-end justify-between mb-6">
        <div class="flex flex-col justify-between content-between">
            <h1 class="text-3xl font-semibold mb-1">Impressoras</h1>
            <span class="text-sm text-gray-500 block">
                Selecione a impressora desejada na lista abaixo. Caso nenhuma impressora seja selecionada, será
                utilizada a impressora padrão do sistema.
            </span>
        </div>
    </div>

    <div class="flex flex-col space-y-5 max-w-[600px]">
        @if (count($printers) > 0)
            @foreach($printers as $printer)
                <div class="border-1 border-gray-100 dark:border-zinc-700 rounded-md p-4 min-h-[8rem] flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <div class="flex gap-x-2 items-center">
                            <h2 class="text-lg text-gray-800 dark:text-gray-200 font-semibold">
                                Nome: {{ $printer['displayName'] }}
                            </h2>
                        </div>

                        <div class="flex items-center h-10">
                            @if ($printer['isSelected'])
                                <span class="text-sm dark:text-gray-400 text-gray-800 font-medium">
                                    Impressora Selecionada
                                </span>
                            @else
                                <x-button wire:click="selectPrinter('{{ $printer['displayName'] }}')" class="btn-sm btn-primary">
                                    Selecionar
                                </x-button>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2">
                        <span class="text-sm text-gray-500">
                            Descrição: {{ $printer['description'] ?? 'Sem descrição' }}
                        </span>
                    </div>

                    <div class="mt-4 flex gap-2">
                        @if($printer['isDefault'])
                            <span class="badge badge-sm badge-primary">
                                impressora padrão
                            </span>
                        @endif
                        @if($printer['status'] === 1)
                            <span class="badge badge-sm badge-warning">
                                impressora pausada
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="min-h-[8rem] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="flex gap-1 items-start flex-col">
                        <span class="text-md text-gray-800 dark:text-gray-200 font-semibold">
                            Nenhuma impressora encontrada.
                        </span>

                        <span class="text-sm text-gray-500">
                            Talvez seja necessário reiniciar o aplicativo após a instalação
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
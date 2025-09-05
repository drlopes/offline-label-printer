<div x-data="{ selectedLabels: [] }">
    <div class="flex items-end justify-between mb-8">
        <div class="flex flex-col justify-between content-between">
            <h1 class="text-3xl font-semibold mb-1">Impressão de Etiquetas</h1>
            <span class="text-sm text-gray-500 block">
                Preencha os campos abaixo e clique em "Gerar Etiquetas".
            </span>
        </div>

        <div class="flex gap-x-2">
            <x-button type="button" icon="o-printer" label="Imprimir Seleção" class="btn btn-sm btn-primary"
                x-cloak x-show="selectedLabels.length > 0" @click="$wire.printLabels(selectedLabels); selectedLabels = []" />
            <x-button type="button" icon="o-printer" label="Imprimir Todas as Etiquetas" class="btn btn-sm btn-success"
                x-cloak x-show="$wire.generatedLabels.length > 0" @click="$wire.printLabels(); selectedLabels = []" />
            <x-button type="submit" icon="o-play" label="Gerar Etiquetas" class="btn btn-sm" form="labelCreationForm" />
        </div>
    </div>

    <div class="flex">
        {{-- form --}}
        <div class="w-full p-2 border-r-1 border-gray-200 dark:border-zinc-700 pr-4">
            {{-- form fields --}}
            <x-form wire:submit="generateLabels" class="w-ful" id="labelCreationForm">
                <div class="flex gap-4">
                    <div class="w-full">
                        <x-input name="paletNumber" label="NUMPAL" wire:model.blur="paletNumber" />
                    </div>
                    <div class="w-full">
                        <x-input name="codpro" label="Código do Produto" wire:model.blur="codpro" />
                    </div>
                </div>

                <x-input name="ds1pro" label="Descrição do Produto" wire:model.blur="ds1pro" />

                <div class="flex gap-4">
                    <div class="w-full">
                        <x-input name="requestNumber" label="Número do Pedido"
                            wire:model.blur="requestNumber" />
                    </div>
                    <div class="w-full">
                        <x-select name="productionLine" label="Linha de Produção"
                            wire:model.blur="productionLine" :options="$productionLines" single
                            option-value="value" option-label="label" placeholder="Selecione uma linha" />
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-full">
                        <x-input name="vendorbatch" label="Lote do Fornecedor"
                            wire:model.blur="vendorbatch" />
                    </div>
                    <div class="w-full">
                        <x-input name="sapbatch" label="SAP Batch" wire:model.blur="sapbatch" />
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-full">
                        <x-input name="paletWeight" label="Peso Real do Palete"
                            wire:model.blur="paletWeight" hint="Peso em Kg vezes 100" />
                    </div>
                    <div class="w-full">
                        <x-input name="boxWeight" label="Peso Nominal por Caixa"
                            wire:model.blur="boxWeight" hint="Peso em Kg vezes 100"
                            :disabled="$paletWeight == null" />
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-full">
                        <x-datetime name="shelfLife" label="Data de Validade"
                            wire:model.blur="shelfLife" />
                    </div>
                    <div class="w-full">
                    </div>
                </div>
            </x-form>

            <div class="pt-6 flex justify-start w-full">
                <x-button icon="o-trash" label="Limpar Formulário" class="btn btn-sm" wire:click="clearForm" />
            </div>
        </div>

        {{-- generated label list --}}
        <div class="min-w-[455px] py-2 px-4 overflow-y-scroll max-h-[80vh]">
            @if (count($generatedLabels) > 0)
                <div class="w-[405px] space-y-3 text-gray-900">
                    @foreach ($generatedLabels as $label => $weight)
                        <div x-on:click="selectedLabels.includes({{ $label }}) ? selectedLabels = selectedLabels.filter(id => id !== {{ $label }}) : selectedLabels.push({{ $label }}); console.log(selectedLabels)"
                            class="flex items-center gap-x-4 justify-between hover:ring-2 hover:ring-indigo-400 cursor-pointer relative">
                            <div x-cloak x-show="selectedLabels.includes({{ $label }})"
                                class="absolute top-2 left-2 bg-indigo-400 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                <span class="text-sm font-bold">✓</span>
                            </div>
                            <div class="border-t-4 border-b-4 border-black h-[250px] bg-white flex flex-col justify-start items-center">
                                <div class="flex border-b-1 w-full font-bold">
                                    <div class="w-full flex flex-col ps-2 text-[14px]">
                                        <span>ITEM: {{ str_pad($codpro, 17, '0', STR_PAD_LEFT) }}</span>
                                        <span class="text-[13px]">{{ $ds1pro }}</span>
                                    </div>
                                    <div class="w-34 flex flex-col">
                                        <span class="text-[14px]">{{ $productionLine }}</span>
                                        <span class="text-[13px]">Etiqueta:
                                            {{ str_pad(($loop->index + 1), 2, '0', STR_PAD_LEFT) }}/{{ count($generatedLabels) }}</span>
                                    </div>
                                </div>

                                <div class="flex border-b-1 w-full font-bold text-[12px]">
                                    <div class="w-[120px] flex items-center ps-2 border-r-1">
                                        <span class="text-[11px] w-[50px]">Peso Palete:</span>
                                        <span
                                            class="text-[13px] text-nowrap">{{ $paletWeight ? str_pad($paletWeight / 100, 4, '0', STR_PAD_LEFT) : '0000' }}
                                            Kg</span>
                                    </div>
                                    <div class="w-[140px] flex items-center justify-center text-[13px]">
                                        <span class="text-nowrap text-[12px]">SAPBATCH: {{ $sapbatch }}</span>
                                    </div>
                                    <div class="w-[120px] flex items-center ps-1 border-l-1">
                                        <span class="text-[11px] w-[50px]">Peso Unitário:</span>
                                        <span class="text-[13px] text-nowrap">{{ str_pad($weight, 4, '0', STR_PAD_LEFT) }}
                                            Kg</span>
                                    </div>
                                </div>

                                <div class="flex border-b-1 w-full font-bold text-[12px]">
                                    <div class="w-full flex items-center justify-start ps-2 text-[13px] border-r-1">
                                        <span class="text-nowrap">LOTE FOR: {{ $vendorbatch }}</span>
                                    </div>
                                    <div class="w-full flex items-center justify-start ps-2 text-[13px]">
                                        <span class="text-nowrap">VALIDATE:
                                            {{ Carbon\Carbon::parse($shelfLife)->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                <div class="flex w-full h-full">
                                    <div class="border-r-1 w-full flex flex-col">
                                        <div class="flex flex-col items-center justify-center border-b-1 pb-3">
                                            <span class="text-[16px] font-bold">E-KANBAN</span>
                                            <span>{!! DNS1D::getBarcodeSVG($requestNumber, 'C128', 2, 40) !!}</span>
                                        </div>
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-[16px] font-bold">NUMPAL</span>
                                            <span>{!! DNS1D::getBarcodeSVG($paletNumber, 'C128', 2, 40) !!}</span>
                                        </div>
                                    </div>
                                    <div class="w-[200px] flex items-center justify-center">
                                        {!! DNS2D::getBarcodeHTML('(91)'.str_pad($codpro, 17, '0', STR_PAD_LEFT).'(10)'.str_pad($paletNumber, 9, '0', STR_PAD_LEFT).'-'.str_pad(($loop->index + 1), 2, '0', STR_PAD_LEFT).'(15)'.$datfvi.'(37)'.str_pad($weight, 4, '0', STR_PAD_LEFT), 'QRCODE', 3, 3) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;
use Native\Laravel\Facades\Settings;
use Native\Laravel\Facades\System;

class Home extends Component
{
    use Toast;

    #[Validate('required', message: 'Campo obrigatório')]
    #[Validate('numeric', message: 'Campo deve ser numérico')]
    public ?string $codpro = null;

    #[Validate('required', message: 'Campo obrigatório')]
    public ?string $ds1pro = null;

    #[Validate('required', message: 'Campo obrigatório')]
    public ?string $sapbatch = null;

    #[Validate('required', message: 'Campo obrigatório')]
    public ?string $vendorbatch = null;

    #[Validate('required', message: 'Campo obrigatório')]
    public ?string $shelfLife = null;

    #[Validate('required', message: 'Campo obrigatório')]
    #[Validate('numeric', message: 'Campo deve ser numérico')]
    #[Validate('max_digits:9', message: 'Campo deve ter no máximo 9 dígitos')]
    public ?string $requestNumber = null;

    #[Validate('required', message: 'Campo obrigatório')]
    #[Validate('numeric', message: 'Campo deve ser numérico')]
    #[Validate('max_digits:9', message: 'Campo deve ter no máximo 9 dígitos')]
    public ?string $paletNumber = null;

    #[Validate('required', message: 'Campo obrigatório')]
    #[Validate('numeric', message: 'Campo deve ser numérico')]
    #[Validate('min:100', message: 'O valor mínimo deste campo é 100')]
    public ?string $paletWeight = null;

    #[Validate('required', message: 'Campo obrigatório')]
    #[Validate('numeric', message: 'Campo deve ser numérico')]
    #[Validate('min:100', message: 'O valor mínimo deste campo é 100')]
    public ?string $boxWeight = null;

    #[Validate('required', message: 'Campo obrigatório')]
    #[Validate('in:OPTIMA 01,OPTIMA 02', message: 'Linha de produção inválida')]
    public ?string $productionLine = null;

    public array $generatedLabels = [];

    public array $productionLines = [
        ['value' => 'OPTIMA 01', 'label' => 'OPTIMA 01'],
        ['value' => 'OPTIMA 02', 'label' => 'OPTIMA 02'],
    ];

    public array $selectedLabels = [];

    public string $datfvi;

    public function mount()
    {
        $this->codpro = Settings::get('codpro');
        $this->ds1pro = Settings::get('ds1pro');
        $this->sapbatch = Settings::get('sapbatch');
        $this->vendorbatch = Settings::get('vendorbatch');
        $this->shelfLife = Settings::get('shelfLife');
        $this->requestNumber = Settings::get('requestNumber');
        $this->paletNumber = Settings::get('paletNumber');
        $this->paletWeight = Settings::get('paletWeight');
        $this->boxWeight = Settings::get('boxWeight');
        $this->productionLine = Settings::get('productionLine');

        if ($this->codpro != null
            && $this->ds1pro != null
            && $this->sapbatch != null
            && $this->vendorbatch != null
            && $this->shelfLife != null
            && $this->requestNumber != null
            && $this->paletNumber != null
            && $this->paletWeight != null
            && $this->boxWeight != null
            && $this->productionLine != null
        ) {
            $this->generateLabels();
        }
    }

    public function render()
    {
        return view('livewire.home');
    }

    public function generateLabels()
    {
        $this->validate();

        $paletWeight = $this->paletWeight;
        $boxWeight = $this->boxWeight;
        $this->datfvi = $this->makeDatfvi();

        $convertedPaletWeight = $paletWeight / 100;
        $convertedBoxWeight = $boxWeight / 100;

        $numberOfLabels = floor($convertedPaletWeight / $convertedBoxWeight) > 0
            ? floor($convertedPaletWeight / $convertedBoxWeight)
            : 1;

        if ($numberOfLabels == 1 && $convertedPaletWeight > $convertedBoxWeight) {
            $numberOfLabels = 2;
        }

        $this->generatedLabels = [];
        $totalGeneratedWeight = 0;

        if ($convertedPaletWeight < $convertedBoxWeight) {
            $this->generatedLabels[1] = $convertedPaletWeight;
        } else {
            for ($label = 1; $label <= $numberOfLabels; $label++) {
                $labelWeight = $label == $numberOfLabels
                    ? $convertedPaletWeight - $totalGeneratedWeight
                    : $convertedBoxWeight;

                $this->generatedLabels[$label] = $labelWeight;
                $totalGeneratedWeight += $labelWeight;
            }
        }

        $this->generatedLabels = array_reverse($this->generatedLabels);
    }

    public function clearForm()
    {
        $this->codpro = '';
        Settings::set('codpro', null);

        $this->ds1pro = '';
        Settings::set('ds1pro', null);

        $this->sapbatch = '';
        Settings::set('sapbatch', null);

        $this->vendorbatch = '';
        Settings::set('vendorbatch', null);

        $this->shelfLife = '';
        Settings::set('shelfLife', null);

        $this->requestNumber = '';
        Settings::set('requestNumber', null);

        $this->paletNumber = '';
        Settings::set('paletNumber', null);

        $this->paletWeight = '';
        Settings::set('paletWeight', null);

        $this->boxWeight = '';
        Settings::set('boxWeight', null);

        $this->productionLine = '';
        Settings::set('productionLine', null);
    }

    public function updated($name, $value)
    {
        if ($name !== 'selectedLabels') {
            Settings::set($name, $value);

            if ($this->codpro != null
                && $this->ds1pro != null
                && $this->sapbatch != null
                && $this->vendorbatch != null
                && $this->shelfLife != null
                && $this->requestNumber != null
                && $this->paletNumber != null
                && $this->paletWeight != null
                && $this->boxWeight != null
                && $this->productionLine != null
            ) {
                $this->datfvi = $this->makeDatfvi();
                $this->generateLabels();
            }
        }
    }

    private function makeDatfvi(): string
    {
        $date = $this->shelfLife;
        return Carbon::parse($date)->format('ymd');
    }

    public function selectLabel(int $label): void
    {
        if (in_array($label, $this->selectedLabels)) {
            $this->selectedLabels = array_diff($this->selectedLabels, [$label]);
        } else {
            $this->selectedLabels[] = $label;
        }
    }

    public function printLabels(?array $labels = null): void
    {
        $labelsToPrint = $labels ?? array_keys($this->generatedLabels);
        $this->js("console.log(".json_encode($labelsToPrint).");");

        $codpro = $this->codpro;
        $padded_codpro = str_pad($codpro, 17, '0', STR_PAD_LEFT);
        $ds1pro = $this->ds1pro;
        $sapbatch = $this->sapbatch;
        $vendorbatch = $this->vendorbatch;
        $shelfLife = $this->shelfLife;
        $requestNumber = $this->requestNumber;
        $paletNumber = $this->paletNumber;
        $padded_paletNumber = str_pad($paletNumber, 7, '0', STR_PAD_LEFT);
        $paletWeight = $this->paletWeight / 100;
        $padded_paletWeight = str_pad($paletWeight, 4, '0', STR_PAD_LEFT);
        $productionLine = $this->productionLine;
        $totalLabels = count($this->generatedLabels);

        $zplTemplate = "^XA
        ^A0,1,1
        ^FO0,1^GB1000,8,8^FS

        ^A0N,30,30^FO10,25^FD ITEM: ^FS
        ^A0N,40,40^FO90,20^FD :codpro ^FS
        ^A0N,40,40^FO596,20^FD :linha ^FS
        ^A0N,40,35^FO8,65^FD :ds1pro ^FS
        ^A0N,30,30^FO599,70^FD Etiqueta: :etiqueta_atual/:total_etiquetas ^FS

        ^FO0,110^GB1000,2,2^FS

        ^A0N,27,27^FO14,120^FD Peso ^FS
        ^A0N,27,27^FO14,150^FD Palete: ^FS
        ^A0N,37,37^FO100,130^FD :peso_total Kg ^FS
        ^FO255,110^GB2,070,2^FS
        ^A0N,34,27^FO270,134^FD SAPBATCH: :sapbatch ^FS
        ^FO553,110^GB2,070,2^FS
        ^A0N,27,27^FO560,120^FD Peso ^FS
        ^A0N,27,27^FO560,150^FD Unitario: ^FS
        ^A0N,37,37^FO655,130^FD :peso_unitario Kg ^FS

        ^FO0,180^GB1000,2,2^FS

        ^A0N,30,30^FO10,194^FD LOTE FOR: :lote ^FS
        ^FO400,180^GB2,049,2^FS
        ^A0N,30,30^FO410,194^FD VALIDADE: :validade ^FS

        ^FO0,227^GB1000,2,2^FS

        ^A0N,27,27^FO210,246^FD E-KANBAN ^FS
        ^FO65,277^BY3^B3,,72^FD:cpe_id^FS

        ^A0N,27,27^FO225,415^FD NUMPAL ^FS
        ^FO90,445^BY3^B3,,100^FD:numpal^FS

        ^FO0,395^GB 565,2,2^FS
        ^FO565,229^GB2,360,2^FS

        ^FO590,305^BY2^BQN,2,6^FD:qrcode_info^FS

        ^FO0,590^GB1000,8,8^FS
        ^XZ";

        $zplTemplate = str_replace([
            ':codpro',
            ':ds1pro',
            ':linha',
            ':total_etiquetas',
            ':peso_total',
            ':sapbatch',
            ':lote',
            ':validade',
            ':cpe_id',
            ':numpal',
        ], [
            $padded_codpro,
            $ds1pro,
            $productionLine,
            $totalLabels,
            $padded_paletWeight,
            $sapbatch,
            strtoupper($vendorbatch),
            Carbon::parse($shelfLife)->format('d/m/Y'),
            $requestNumber,
            $paletNumber,
        ], $zplTemplate);

        $labels = '';
        foreach ($labelsToPrint as $label) {
            $labelWeight = $this->generatedLabels[$label];
            $padded_labelWeight = str_pad($labelWeight, 4, '0', STR_PAD_LEFT);
            $labelNumber = $label + 1;
            $qrcodeContent = ']Q3(91)'.$padded_codpro.'(10)'.$padded_paletNumber.'-'.str_pad($labelNumber, 2, '0', STR_PAD_LEFT).'(15)'.Carbon::parse($this->shelfLife)->format('ymd').'(37)'.$padded_labelWeight;

            $label = str_replace([
                ':etiqueta_atual',
                ':peso_unitario',
                ':qrcode_info',
            ], [
                $labelNumber,
                str_pad($labelWeight, 4, '0', STR_PAD_LEFT),
                $qrcodeContent,
            ], $zplTemplate);

            $labels .= $label;
        }

        if ($labels) {
            $selectedPrinter = Settings::get('selected_printer');
            $systemPrinters = System::printers();

            if (! $selectedPrinter) {
                $defaultPrinter = null;
                foreach ($systemPrinters as $printer) {
                    if ($printer->isDefault) {
                        $defaultPrinter = $printer->displayName;
                        break;
                    }
                }

                $selectedPrinter = $defaultPrinter;
            }

            if ($selectedPrinter) {
                $printer = null;
                foreach ($systemPrinters as $p) {
                    if ($p->displayName === $selectedPrinter) {
                        $printer = $p;
                        break;
                    }
                }

                System::print($labels, $printer);

                $this->toast(
                    type: 'success',
                    title: 'Sucesso!',
                    description: "Etiquetas impressas com sucesso",
                    position: 'toast-bottom toast-end',
                    icon: 'o-printer',
                    css: 'alert-success',
                    timeout: 3000,
                    redirectTo: null,
                );
            } else {
                $this->toast(
                    type: 'error',
                    title: 'Erro!',
                    description: "Impressora não encontrada",
                    position: 'toast-bottom toast-end',
                    icon: 'o-printer',
                    css: 'alert-danger',
                    timeout: 3000,
                    redirectTo: null,
                );
            }
        } else {
            $this->toast(
                type: 'error',
                title: 'Erro!',
                description: "Nenhuma etiqueta para imprimir",
                position: 'toast-bottom toast-end',
                icon: 'o-printer',
                css: 'alert-danger',
                timeout: 3000,
                redirectTo: null,
            );
        }
    }
}

<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Actions\CurationAction;
use Closure;
use Exception;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasColor;
use Filament\Actions\Concerns\HasSize;
use Filament\Forms\Components\Field;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Contracts\Support\Htmlable;

class CuratorEditor extends Field
{
    use HasColor;
    use HasSize;
    use CanBeOutlined;

    protected string $view = 'curator::components.forms.curation';

    protected string|Htmlable|Closure|null $buttonLabel = null;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->buttonLabel = __('curator::views.picker.button');
        $this->size = 'md';
        $this->color = 'primary';
        $this->isOutlined = true;

        $this->registerActions([
            CurationAction::make(),
        ]);
    }

    public function buttonLabel(string|Htmlable|Closure|null $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function getButtonLabel(): string|Htmlable|null
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function getPresets(): array|null
    {
        return app('curator')->getCurationPresets();
    }
}

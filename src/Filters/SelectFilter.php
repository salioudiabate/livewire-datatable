<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

use Salioudiabate\LivewireDatatable\DataSources\DataSource;

final class SelectFilter extends Filter
{
    /**
     * @var array<int|string, string>
     */
    private array $options = [];

    /**
     * @param  array<int|string, string>  $options
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<int|string, string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function view(): string
    {
        return 'livewire-datatable::filters.select';
    }

    public function defaultValue(): mixed
    {
        return '';
    }

    protected function applyDefault(DataSource $dataSource, mixed $value): DataSource
    {
        if ($value === null || $value === '') {
            return $dataSource;
        }

        return $dataSource->applyWhere($this->key, '=', $value);
    }
}

<?php

namespace App\View\Components\Card;

use Illuminate\View\Component;
use Illuminate\View\View;

class Card extends Component
{
    /**
     * Les propriétés du composant
     */
    public string $type;
    public ?string $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $type = 'default',
        ?string $class = null
    ) {
        $this->type = $type;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.card.card');
    }

    /**
     * Détermine la classe CSS basée sur le type
     */
    public function typeClass(): string
    {
        return match ($this->type) {
            'primary' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-800',
            'success' => 'bg-green-50 dark:bg-green-900/20 border-green-100 dark:border-green-800',
            'warning' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-800',
            'danger' => 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800',
            'purple' => 'bg-purple-50 dark:bg-purple-900/20 border-purple-100 dark:border-purple-800',
            default => 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700',
        };
    }
}

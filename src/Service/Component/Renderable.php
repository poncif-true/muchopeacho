<?php


namespace App\Service\Component;

/**
 * Interface Renderable
 *
 * @package App\Service\Component
 */
interface Renderable
{
    /**
     * Returns template name
     * Template MUST be located inside the project's templates directory
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Returns the context linked to template
     * @return array
     */
    public function getContext(): array;
}

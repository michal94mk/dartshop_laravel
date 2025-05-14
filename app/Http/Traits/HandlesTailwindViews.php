<?php

namespace App\Http\Traits;

trait HandlesTailwindViews
{
    /**
     * Determine if the request is for a Tailwind view.
     *
     * @return bool
     */
    protected function isTailwindRequest(): bool
    {
        return request()->has('tailwind');
    }
    
    /**
     * Get the appropriate view based on whether Tailwind is requested.
     *
     * @param string $defaultView
     * @param string|null $tailwindView
     * @return string
     */
    protected function getViewType(string $defaultView, ?string $tailwindView = null): string
    {
        if ($this->isTailwindRequest() && $tailwindView) {
            return $tailwindView;
        }
        
        return $defaultView;
    }
    
    /**
     * Append tailwind parameter to URL if it was in the original request.
     *
     * @param array $parameters
     * @return array
     */
    protected function appendTailwindParam(array $parameters = []): array
    {
        if ($this->isTailwindRequest()) {
            $parameters['tailwind'] = 1;
        }
        
        return $parameters;
    }
} 
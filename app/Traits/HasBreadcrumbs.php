<?php

namespace App\Traits;

trait HasBreadcrumbs
{
    protected function encodeBreadcrumbs($breadcrumbs)
    {
        return base64_encode(json_encode($breadcrumbs));
    }

    protected function decodeBreadcrumbs($encodedBreadcrumbs)
    {
        if (!$encodedBreadcrumbs) {
            return null;
        }

        try {
            $decoded = base64_decode($encodedBreadcrumbs);
            $breadcrumbs = json_decode($decoded, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $breadcrumbs;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}

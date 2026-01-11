<?php

namespace App\Livewire\Settings\Traits;

/**
 * Main trait for Theme Settings.
 * This trait orchestrates properties, actions, and validation for theme management.
 * 
 * @see HasThemeProperties
 * @see HasThemeActions
 * @see HasThemeValidation
 */
trait HasThemeSettings
{
    use HasThemeProperties, HasThemeActions, HasThemeValidation;
}

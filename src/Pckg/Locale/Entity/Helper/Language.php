<?php namespace Pckg\Locale\Entity\Helper;

use Pckg\Locale\Entity\Languages;

trait Language
{

    public function language()
    {
        return $this->belongsTo(Languages::class)
                    ->foreignKey('language_id')
                    ->primaryKey('slug');
    }

}

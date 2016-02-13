<?php

require 'vendor/autoload.php';

use Sami\Sami;
use Sami\Parser\Filter\TrueFilter;
use Sami\Reflection\MethodReflection;
use Sami\Reflection\PropertyReflection;
use Symfony\Component\Finder\Finder;

$dir = 'Schnittstabil';

$composerJson = json_decode(file_get_contents(dirname(__FILE__).'/composer.json', true));

$title = strtr(ucwords(strtr($composerJson->name, '/-', '  ')), ' ', '\\').' API';

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir);

$sami = new Sami(
    $iterator,
    array(
        'title' => $title,
        'build_dir' => __DIR__.'/doc',
        'cache_dir' => __DIR__.'/build/cache',
        'default_opened_level' => 2,
    )
);

class Filter extends TrueFilter
{
    public function acceptMethod(MethodReflection $method)
    {
        return $method->isPublic() || $method->isProtected();
    }

    public function acceptProperty(PropertyReflection $property)
    {
        return $property->isPublic() || $property->isProtected();
    }
}

$sami['filter'] = function () {
    return new Filter();
};

return $sami;

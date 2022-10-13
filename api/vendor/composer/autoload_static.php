<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfef3be781c6e7daca8f01e44e06a326a
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfef3be781c6e7daca8f01e44e06a326a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfef3be781c6e7daca8f01e44e06a326a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfef3be781c6e7daca8f01e44e06a326a::$classMap;

        }, null, ClassLoader::class);
    }
}
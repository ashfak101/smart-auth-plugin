<?php

$classmap = [
    'AK\SMARTAUTH' => __DIR__ . '/app',
];

spl_autoload_register( function( string $classname ) use ( $classmap ) {
    $parts = explode( '\\', $classname );

    $part_1 = array_shift( $parts );
    $part_2 = array_shift( $parts );
    $namespace = $part_1.'\\'.$part_2;
    $classfile = array_pop( $parts ) . '.php';

    if ( ! array_key_exists( $namespace, $classmap ) ) {
        return;
    }

    $path = implode( DIRECTORY_SEPARATOR, $parts );
    $file = $classmap[$namespace] . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $classfile;

    if ( ! file_exists( $file ) && ! class_exists( $classname ) ) {
        return;
    }

    require_once $file;
} );
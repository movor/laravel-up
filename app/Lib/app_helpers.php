<?php

/**
 * Fetch random base64 image
 *
 * @param int $width  Desired image width
 * @param int $height Desired image height
 *
 * @return string
 */
function fetchRandomBase64Image($width = 1000, $height = 1000)
{
    static $providerKey = 0;
    static $imageNo = 1;
    static $blackListedProviders = [];

    $message = '!!! FAILED(using hardcoded image) | ';

    $providers = [
        "https://loremflickr.com/g/$width/$height/paris",
        "http://lorempixel.com/$width/$height/",
        "https://picsum.photos/$width/$height?random",
        "https://source.unsplash.com/random/{$width}x{$height}",
        "https://unsplash.it/$width/$height/?random",
        "https://placebear.com/g/$width/$height",
        "https://dummyimage.com/{$width}x{$height}/000/fff",
        "http://fpoimg.com/{$width}x{$height}?text=" . str_random(6),
        "https://baconmockup.com/$width/$height/"
    ];

    // Unset previously blacklisted providers
    foreach ($blackListedProviders as $blackListedProvider) {
        unset($providers[$blackListedProvider]);
    }

    // Use provider or blacklist it on fail
    if (!empty($providers)) {
        try {
            $image = base64_encode(file_get_contents($providers[$providerKey]));
            $message = '';
        } catch (\Exception $e) {
            dump('!!! Error: ' . $e->getMessage());
            dump('!!! Blacklisting provider: ' . $providers[$providerKey]);
            $blackListedProviders[] = $providerKey;
        }
    }

    // Reset provider key if all providers are used
    if ($providerKey == count($providers) - 1) {
        $providerKey = 0;
    } else {
        $providerKey++;
    }

    // Dump
    dump("Image no:. $imageNo | Provider: {$providers[$providerKey]}");
    $imageNo++;

    return 'data:image/jpg;base64,'
        . ($message == ''
            ? $image
            : '/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAAAAD/4QMxaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzEzOCA3OS4xNTk4MjQsIDIwMTYvMDkvMTQtMDE6MDk6MDEgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE3IChNYWNpbnRvc2gpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjk1QTdBMzZGMkM3ODExRTk4MDYxQzU1OUI2QTlCNEMxIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjk1QTdBMzcwMkM3ODExRTk4MDYxQzU1OUI2QTlCNEMxIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6OTVBN0EzNkQyQzc4MTFFOTgwNjFDNTU5QjZBOUI0QzEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6OTVBN0EzNkUyQzc4MTFFOTgwNjFDNTU5QjZBOUI0QzEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAAbGhopHSlBJiZBQi8vL0JHPz4+P0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHAR0pKTQmND8oKD9HPzU/R0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0f/wAARCAABAAEDASIAAhEBAxEB/8QASwABAQAAAAAAAAAAAAAAAAAAAAYBAQAAAAAAAAAAAAAAAAAAAAAQAQAAAAAAAAAAAAAAAAAAAAARAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/AKYAH//Z');
}
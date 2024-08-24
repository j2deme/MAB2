@props(['estatus'])

<x-badge color="{{ $estatus->color() }}" class="" label="{{ $estatus->value }}" />
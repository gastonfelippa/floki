@extends('layouts.template',[
  'modComandas'       => session('modComandas'),
  'modConsignaciones' => session('modConsignaciones'),
  'modViandas'        => session('modViandas'),
  'modDelivery'       => session('modDelivery'),
  'modClubes'         => session('modClubes'),
  'comercioTipo'      => session('tipoComercio')
])
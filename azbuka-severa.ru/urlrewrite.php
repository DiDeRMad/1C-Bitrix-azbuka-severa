<?php
$arUrlRewrite=array (
  9 => 
  array (
    'CONDITION' => '#^/community/recepties/(.*)/.*#',
    'RULE' => 'ELEMENT_ID=$1',
    'ID' => 'news.detail',
    'PATH' => '/community/recepties/detail.php',
    'SORT' => 100,
  ),
  5 => 
  array (
    'CONDITION' => '#^/community/articles/(.*)/.*#',
    'RULE' => 'ELEMENT_CODE=$1',
    'ID' => 'news.detail',
    'PATH' => '/community/articles/detail.php',
    'SORT' => 100,
  ),
  11 => 
  array (
    'CONDITION' => '#^/acrit.exportproplus/(.*)#',
    'RULE' => 'path=$1',
    'ID' => NULL,
    'PATH' => '/acrit.exportproplus/index.php',
    'SORT' => 100,
  ),
  6 => 
  array (
    'CONDITION' => '#^/community/news/(.*)/.*#',
    'RULE' => 'ELEMENT_ID=$1',
    'ID' => 'news.detail',
    'PATH' => '/community/news/detail.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/personal/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.section',
    'PATH' => '/personal/index.php',
    'SORT' => 100,
  ),
  12 => 
  array (
    'CONDITION' => '#^/loyalty/#',
    'RULE' => NULL,
    'ID' => 'skyweb24:loyaltyprogram',
    'PATH' => '/loyalty/index.php',
    'SORT' => 100,
  ),
  13 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  14 => 
  array (
    'CONDITION' => '#^/katalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/katalog/index.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/tags/#',
    'RULE' => '',
    'ID' => 'fouro:tags',
    'PATH' => '/tags/index.php',
    'SORT' => 100,
  ),
);

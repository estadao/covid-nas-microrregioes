<?php

require 'project-functions.php';

$view = isset( $_GET[ 'view' ] ) ? true : false ;
$path = dirname(__FILE__) . '/pages/' . id();

if ( $view )
  include 'view/open.php';

if ( file_exists( $path ) ) {
  $page = json( $path . '/page' );
  $base = base();
  $template = 'https://arte.estadao.com.br/share/template/conteudo/';

  echo '<section class="arte-content" data-arte-page="' . id() . '">';

  echo '<link rel="stylesheet" href="' . $template . 'styles/app.min.css?v=' . $app->version . '">';

  if ( file_exists( 'styles/custom.min.css' ) )
    echo '<link rel="stylesheet" href="' . $base . 'styles/custom.min.css?v=' . $app->version . '">';

  if ( $page->background )
    echo '<style>:root { --arte-background: ' . $page->background . ' }</style>';

  foreach ( $page->content as $block )
    render( $block );

  $dependencies = json_decode('{"type":"include","size":"s","class":"","style":"","align":"","content":"dependencies"}');
  render( $dependencies );

  echo '<script>let arte = { ads: true, theme: "light" }</script>';

  if ( property_exists( $page, 'ads' ) && $page->ads === false )
    echo '<script>arte.ads = false</script>';

  if ( property_exists( $page, 'theme' ) )
    echo '<script>arte.theme = "' . $page->theme . '"</script>';

  echo '<script src="' . $template . 'scripts/app.min.js?v=' . $app->version . '"></script>';

  if ( file_exists( 'scripts/custom.min.js' ) )
    echo '<script src="' . $base . 'scripts/custom.min.js?v=' . $app->version . '"></script>';

  echo '</section>';

}

if ( $view )
  include 'view/close.php';

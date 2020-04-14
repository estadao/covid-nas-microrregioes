<?php

function localhost() {

  $localhost = array( '127.0.0.1', '::1' );

  if ( in_array( $_SERVER[ 'REMOTE_ADDR' ], $localhost ) )
    return true;

  return false;

}

function curl( $url ) {

  $referer    = 'http://www.google.com';
  $user_agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0';

  $ch = curl_init( $url );

  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true        );
  curl_setopt( $ch, CURLOPT_AUTOREFERER,    true        );
  curl_setopt( $ch, CURLOPT_REFERER,        $referer    );
  curl_setopt( $ch, CURLOPT_USERAGENT,      $user_agent );
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false       );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true        );

  $response = curl_exec( $ch );

  curl_close( $ch );

  return $response;

}

function id() {

  return isset( $_GET[ 'id'] ) ? $_GET[ 'id' ] : 'page';

}

function json( $path ) {

  $path = preg_match( '/.json$/', $path ) ? $path : $path . '.json';

  if ( file_exists( $path ) ) {

    $string = file_get_contents( $path );
    return json_decode( $string );

  }

}

function base() {

  $url = 'http' . ( localhost() ? '' : 's' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $url = preg_replace( '#[^/]*$#', '', $url );

  return $url;

}


function render( $block ) {

  global $app;

  $root = 'https://arte.estadao.com.br';

  echo
  '<div ' .
    'id="'                . ( isset( $block->anchor ) ? $block->anchor : '' ) . '" ' .
    'class="arte-column-' . $block->size                                      . '" ' .
    'data-contains="'     . $block->type                                      . '" ' .
    'data-align="'        . ( isset( $block->align ) ? $block->align : '' )   . '">';

  if ( property_exists( $block, 'content' ) && is_array( $block->content ) && $block->type !== 'split' ) {

    foreach ( $block->content as $inner_block ) {

      render( $inner_block );

    }

  } else {

    switch ( $block->type ) {


      case 'navigation':

        if ( $block->mode == 'all' ) {

          echo
          '<nav ' .
            'class="' . $block->class . '" ' .
            'style="' . $block->style . '" ' .
            'data-mode="' . $block->mode . '">' .
            '<div>' .
              '<ul>';

              foreach ( $block->chapters as $chapter ) {
                 
                if ( property_exists( $chapter, 'clickable' ) && $chapter->clickable === true ) {
                  $class = '';
                } else {
                  $class = 'arte-chapter-disabled';
                }

                echo
                '<li class="' . $class . '">' .
                  '<a href=" ' . $chapter->url . '">' .
                    '<div>' .
                      '<div>' . $chapter->number . '</div>' .
                      '<div>' . $chapter->title . '</div>' .
                    '</div>' .
                    '<img src="' . ( $chapter->media ? $chapter->media : get_open_graph( $chapter->url ) ) . '">' .
                  '</a>' .
                '</li>';

              }

              echo
              '</ul>' .
            '</div>' .
          '</nav>';

        }

        if ( $block->mode == 'next' ) {

          echo
          '<nav ' .
            'class="' . $block->class . '" ' .
            'style="' . $block->style . '" ' .
            'data-mode="' . $block->mode . '">' .
            '<div>' .
              '<ul>';

              foreach ( $block->chapters as $chapter ) {

                echo
                '<li>' .
                  '<a href=" ' . $chapter->url . '">' .
                    '<div>' .
                      '<div>' . $chapter->number . '</div>' .
                      '<div>' . $chapter->title . '</div>' .
                    '</div>' .
                    '<img src="' . ( $chapter->media ? $chapter->media : get_open_graph( $chapter->url ) ) . '">' .
                    '<div>Próximo capítulo →</div>' .
                  '</a>' .
                '</li>';

              }

              echo
              '</ul>' .
            '</div>' .
          '</nav>';

        }

        break;

      case 'kicker':

        echo
        '<h4 ' .
          'class="' . $block->class . '" ' .
          'style="' . $block->style . '">' .

          $block->content .

        '</h4>';

        break;

      case 'heading':

        echo
        '<h3 ' .
          'class="' . $block->class . '" ' .
          'style="' . $block->style . '">' .

          $block->content .

        '</h3>';

        break;

      case 'paragraph':
      case 'annotation':
      case 'indentation':
      case 'box':

        echo
        '<p ' .
          'class="' . $block->class . '" ' .
          'style="' . $block->style . '">' .

          $block->content .

        '</p>';

        break;

      case 'quotes':

        echo
        '<blockquote ' .
          'class="' . $block->class . '" ' .
          'style="' . $block->style . '">' .

          '<div>' . $block->content . '</div>' .
          '<div>' . $block->author . '</div>' .

        '</blockquote>';

        break;

      case 'divider':

        echo
        '<hr ' .
          'class="' . $block->class . '" ' .
          'style="' . $block->style . '" ' .
          '>';

        break;

      case 'image':

        if ( $block->provider == 'local' ) {

          $path = '';

          if ( !localhost() ) {

            if ( $app->category === '' )
              $path .= $root . '/' . $app->section . '/' . $app->slug . '/';

            else
              $path = $root . '/' . $app->section . '/' . $app->category . '/' . $app->slug . '/';

          }

          $path .= 'media/images/';

          echo
          '<div ' .
            'class="arte-media ' . $block->class . '" ' .
            'style="' . $block->style . '">' .

            '<figure>' .
              '<img src="' . $path . $block->id . '">' .
              '<figcaption>' .
                '<span class="arte-image-caption">' . $block->options->caption . '</span>' .
                '<span class="arte-image-credit">' . $block->options->credit . '</span>' .
              '</figcaption>' .
            '</figure>' .

          '</div>';

        }

        if ( $block->provider == 'agile' ) {

          echo
          '<div ' .
            'class="image-agile ' . $block->class . ' mm_conteudo blog-multimidia foto" ' .
            'style="' . $block->style . '" ' .
            'data-caption="' . json_encode( $block->options->caption ) . '" ' .
            'data-credit="'  . json_encode( $block->options->credit  ) . '" ' .
            'data-config=\'{"tipo":"FOTO","id":"' . $block->id . '","provider":"AGILE"}\'></div>';

        }

        break;

      case 'video':

        if ( $block->provider == 'youtube' ) {

          $params = array(
            'color' => 'white',
            'rel'   => '0',
          );

          $query = http_build_query( $params );

          $src  = 'https://www.youtube.com/embed/';
          $src .= $block->id;
          $src .= '?';
          $src .= $query;

          echo
          '<div ' .
            'class="arte-media arte-image-local' . $block->class . '" ' .
            'style="' . $block->style . '">' .

            '<figure>' .

              '<div class="youtube-container">' .
                '<iframe src="' . $src . '" allowfullscreen></iframe>' .
              '</div>' .

              '<figcaption>' .
                '<span class="arte-image-caption">' . $block->options->caption . '</span>' .
                '<span class="arte-image-credit">' . $block->options->credit . '</span>' .
              '</figcaption>' .

            '</figure>' .

          '</div>';

        }

        if ( $block->provider == 'agile' ) {

          echo
          '<div ' .
            'class="' . $block->class . ' mm_conteudo video video-noticia-blog" ' .
            'style="' . $block->style . '" ' .
            ( $block->options->autoplay ? 'autoplay ' : '' ) .
            'data-config=\'{"tipo":"VIDEO","id":"' . $block->id . '","provider":"AGILE"}\'></div>';

        }

        break;

      case 'gallery':

        if ( $block->provider == 'agile' ) {

          echo
          '<div ' .
            'class="' . $block->class . ' mm_conteudo blog-multimidia galeria" ' .
            'style="' . $block->style . '" ' .
            'data-config=\'{"tipo":"GALERIA","id":"' . $block->id . '","provider":"AGILE"}\'></div>';

        }

        break;

      case 'graphic':

        if ( $block->provider == 'uva' ) {

          echo
          '<script ' .
            'data-uva-id="'           . $block->id                                  . '" ' .
            'data-show-title="'       . json_encode( $block->options->title )       . '" ' .
            'data-show-description="' . json_encode( $block->options->description ) . '" ' .
            'data-show-brand="'       . json_encode( $block->options->brand )       . '" ' .
            'src="https://arte.estadao.com.br/uva/scripts/embed.min.js" '           .
            '></script>';

        }

        break;

      case 'link':

        echo
        '<a ' .
          'class="' . $block->class . '" ' .
          'style="' . $block->style . '" ' .
          'href="'  . $block->href  . '" ' .
          'target="_blank">' .

          $block->content .

        '</a>';

        break;

      case 'credits':

        echo
        '<p><dl ' .
          'class="' . $block->class . '" ' .
          'style="' . $block->style . '">';

          foreach ( $block->roles as $entry ) {

            echo
            '<dt>' . $entry->role . '</dt>' .
            '<dd>' . $entry->people . '</dd>';

          }

        echo '</dl></p>';

        break;

      case 'html':

        echo
        '<div ' .
          'class="' . $block->class . '" ' .
          'style="' . $block->style . '">' .

          $block->content .

        '</div>';

        break;

      case 'split':

        echo
        '<div ' .
          'class="' . $block->class . ' arte-split" ' .
          'style="' . $block->style . '" ' .
          'data-fraction="' . $block->options->fraction . '" ' .
          'data-blocks="' . count( $block->content ) .'" >';

          foreach ( $block->content as $content ) {
            render( $content );
          }

        echo '</div>';

        break;

      case 'include':

        $string = file_get_contents( 'include/' . $block->content . '.php' );
        $string = str_replace( array( "\r", "\n" ), '', $string );
        $string = trim( $string );

        echo $string;

        break;

    }

  }

  echo '</div>';

}

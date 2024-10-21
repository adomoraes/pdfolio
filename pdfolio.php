<?php
/**
 * Plugin Name: PDFolio
 * Description: Um plugin para cadastrar e listar portfólios com título e data de criação.
 * Version: 1.0
 * Author: Eduardo Moraes
 */

// Evita acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Registrar o Custom Post Type
function pdfolio_register_portfolio() {
    $args = array(
        'label' => 'Portfólios',
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'rewrite' => array('slug' => 'portfolio'),
    );
    register_post_type( 'portfolio', $args );
}
add_action( 'init', 'pdfolio_register_portfolio' );

// Criar um shortcode para listar os portfólios
function pdfolio_list_portfolios( $atts ) {
    $args = array(
        'post_type' => 'portfolio',
        'posts_per_page' => -1, // Listar todos os portfólios
    );
    $query = new WP_Query( $args );

    $output = '<ul class="pdfolio-portfolio-list">';
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $output .= '<li>';
            $output .= '<strong>' . get_the_title() . '</strong><br>';
            $output .= 'Data de Criação: ' . get_the_date() . '<br>';
            $output .= '</li>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<li>Nenhum portfólio encontrado.</li>';
    }
    $output .= '</ul>';

    return $output;
}
add_shortcode( 'pdfolio_list', 'pdfolio_list_portfolios' );

// Incluir o Dompdf
require_once plugin_dir_path(__FILE__) . 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

function pdfolio_generate_pdf($post_id) {
    // Verificar se o post é do tipo 'portfolio'
    if (get_post_type($post_id) !== 'portfolio') {
        return;
    }

    // Criar uma nova instância do Dompdf
    $dompdf = new Dompdf();

    // Obter os dados do portfólio
    $title = get_the_title($post_id);
    $site_name = get_bloginfo('name');
    $site_url = get_bloginfo('url');
    // $content = get_post($post_id)->post_content; // Conteúdo do portfólio
    // $date = get_the_date('', $post_id); // Data de criação

    // Obter as imagens da galeria
    $image_ids = get_post_meta($post_id, 'pdfolio_image_gallery', true);
    $image_ids = !empty($image_ids) ? explode(',', $image_ids) : [];

    // Montar o HTML para o PDF com o layout de mosaico
    $html = "
        <h1>{$site_name}</h1>
        <div>{$title}</div>";

    // Função para definir a classe CSS e largura das imagens por página
    function get_layout_for_page($page, $index) {
        $layouts = [
            1 => ['large'],                    // Página 1: 1 imagem grande
            2 => ['small', 'small', 'small', 'small'], // Página 2: 4 imagens pequenas (25% da largura)
            3 => ['small', 'small', 'small', 'small', 'small', 'small', 'small', 'small', 'small'], // Página 3: 9 imagens (33% cada)
            4 => ['large', 'large'],           // Página 4: 2 imagens grandes
        ];

        // Verifica se há um layout para a página, caso contrário usa o layout da última página definida
        return $layouts[$page][$index % count($layouts[$page])] ?? 'small';
    }

    // Início da galeria de imagens
    $html .= "<div style='display: flex; flex-wrap: wrap; width: 100%;'>";

    // Contador para controlar as imagens
    $image_count = count($image_ids);
    $page = 1;
    $image_index = 0;
    $images_per_page = [1, 4, 9, 2]; // Definição do número de imagens por página

    foreach ($image_ids as $index => $image_id) {
        // Recupera a URL da imagem
        $image_url = wp_get_attachment_url($image_id);
        
        // Definir o layout da imagem para a página atual
        $size_class = get_layout_for_page($page, $image_index);

        // Definir o estilo baseado na classe da imagem
        switch ($size_class) {
            case 'small':
                $width = '25%'; // Pequena (25% da largura)
                break;
            case 'medium':
                $width = '33%'; // Média (33% da largura)
                break;
            case 'large':
                $width = '100%'; // Grande (100% da largura)
                break;
        }

        // Adicionar a imagem ao HTML com o estilo adequado
        $html .= "<div style='width: {$width}; margin: 5px;'><img src='{$image_url}' style='width: 100%; height: auto;'></div>";
        
        $image_index++;

        // Verifica se o limite de imagens por página foi alcançado
        if ($image_index >= $images_per_page[$page - 1]) {
            $page++;
            $image_index = 0;

            // Quebra de página ao atingir o limite de imagens
            if ($page <= 4 && $index + 1 < $image_count) {
                $html .= "</div><div style='page-break-before: always;'><h2>Página {$page}</h2><div style='display: flex; flex-wrap: wrap;'>";
            }
        }
    }

    // Fechar o último bloco
    $html .= "</div>"; 

    // Fechar o HTML
    $html .= "</div>";

    // Configurar opções do Dompdf
    $options = $dompdf->getOptions();
    $options->set('isRemoteEnabled', true);
    $dompdf->setOptions($options);

    // Carregar o HTML no Dompdf
    $dompdf->loadHtml($html);

    // Definir o tamanho do papel e a orientação
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar o PDF
    $dompdf->render();

    // Enviar o PDF para download
    $dompdf->stream("{$title}.pdf", array("Attachment" => true));
}


// Adicionar botão para gerar PDF na tela de edição do portfólio
function pdfolio_add_pdf_button($post) {
    if (get_post_type($post) === 'portfolio') {
        $url = admin_url('admin-ajax.php?action=pdfolio_generate_pdf&post_id=' . $post->ID);
        echo '<a href="' . esc_url($url) . '" class="button button-primary" target="_blank">Gerar PDF</a>';
    }
}
add_action('edit_form_after_title', 'pdfolio_add_pdf_button');

// Ação AJAX para gerar o PDF
function pdfolio_ajax_generate_pdf() {
    $post_id = intval($_GET['post_id']);
    pdfolio_generate_pdf($post_id);
    exit;
}
add_action('wp_ajax_pdfolio_generate_pdf', 'pdfolio_ajax_generate_pdf');

// Adicionar uma meta box para a galeria de imagens
function pdfolio_add_image_gallery_meta_box() {
    add_meta_box(
        'pdfolio_image_gallery',
        'Galeria de Imagens',
        'pdfolio_image_gallery_callback',
        'portfolio',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'pdfolio_add_image_gallery_meta_box');

function pdfolio_image_gallery_callback($post) {
    // Adicionar um nonce para segurança
    wp_nonce_field('pdfolio_save_image_gallery', 'pdfolio_image_gallery_nonce');

    // Obter os IDs das imagens salvas
    $image_ids = get_post_meta($post->ID, 'pdfolio_image_gallery', true);
    $image_ids = !empty($image_ids) ? explode(',', $image_ids) : [];

    // Botão para adicionar imagens
    echo '<div id="pdfolio-gallery-container">';
    foreach ($image_ids as $image_id) {
        $image_url = wp_get_attachment_url($image_id);
        echo '<div class="pdfolio-gallery-image" style="display:inline-block; margin-right:10px; position:relative;">
                <img src="' . esc_url($image_url) . '" style="max-width:100px; max-height:100px;">
                <input type="hidden" name="pdfolio_gallery_images[]" value="' . esc_attr($image_id) . '">
                <button class="remove-image-button" style="position:absolute; top:0; right:0;">&times;</button>
              </div>';
    }
    echo '</div>';
    echo '<button id="pdfolio-add-image" class="button">Adicionar Imagens</button>';
}

// Salvar a galeria de imagens quando o portfólio for salvo
function pdfolio_save_image_gallery($post_id) {
    // Verificar nonce
    if (!isset($_POST['pdfolio_image_gallery_nonce']) || !wp_verify_nonce($_POST['pdfolio_image_gallery_nonce'], 'pdfolio_save_image_gallery')) {
        return;
    }

    // Verificar se o usuário tem permissão para salvar
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Salvar os IDs das imagens
    if (isset($_POST['pdfolio_gallery_images'])) {
        $image_ids = array_map('intval', $_POST['pdfolio_gallery_images']);
        update_post_meta($post_id, 'pdfolio_image_gallery', implode(',', $image_ids));
    } else {
        delete_post_meta($post_id, 'pdfolio_image_gallery');
    }
}
add_action('save_post', 'pdfolio_save_image_gallery');


// Enfileirar scripts e estilos necessários
function pdfolio_enqueue_admin_scripts() {
    wp_enqueue_media(); // Necessário para a biblioteca de mídia
    wp_enqueue_script('pdfolio-admin-script', plugin_dir_url(__FILE__) . 'js/pdfolio-admin.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'pdfolio_enqueue_admin_scripts');

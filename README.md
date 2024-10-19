# Portfolio - Plugin WordPress

**Portfolio** é um plugin para WordPress que permite criar portfólios em formato PDF a partir de imagens selecionadas da biblioteca de mídia. O plugin organiza as imagens em um layout de mosaico, intercalando tamanhos de imagens e gerando PDFs clicáveis com hyperlinks. Tudo isso pode ser gerenciado diretamente no painel administrativo do WordPress.

## Funcionalidades

- Criação de portfólios em PDF com até 3 a 4 imagens por página.
- Layout de mosaico com intercalamento de tamanhos: pequena, média e grande.
- Cada imagem no PDF é clicável, redirecionando para o hyperlink especificado.
- Interface intuitiva no admin do WordPress para criar, editar e deletar portfólios.
- Ações para gerar e visualizar PDFs diretamente no admin.
- Compatível com o WordPress 5.2.2 e superior.

## Instalação

1. Faça o download deste repositório ou clone-o para o diretório de plugins do WordPress:

   ```bash
   git clone https://github.com/seu-usuario/portfolio-plugin.git wp-content/plugins/portfolio
   ```

2. Acesse o painel administrativo do WordPress e vá para **Plugins > Plugins Instalados**.
3. Ative o plugin **Portfolio**.

## Como Usar

### Criar um Portfólio

1. No painel do WordPress, acesse **Portfólios > Adicionar Novo**.
2. Insira o **título** do portfólio.
3. Selecione as **imagens** da biblioteca de mídia ou faça o upload diretamente.
4. Insira o **hyperlink** que será associado às imagens (por padrão, a URL base do site será usada).
5. Clique em **Publicar** para salvar o portfólio.
6. Clique em **Gerar PDF** na lista de portfólios para gerar o arquivo PDF.

### Listar, Editar ou Excluir Portfólios

- Acesse **Portfólios > Todos os Portfólios** para ver a lista de todos os portfólios criados.
- Na tela de listagem, você pode:
  - **Visualizar o PDF** gerado.
  - **Editar** ou **Deletar** portfólios já criados.
  - **Gerar ou regenerar PDFs**.

## Estrutura de Arquivos

```plaintext
portfolio/
├── assets/
│   ├── js/
│   │   └── portfolio-admin.js         # Script de interação com a Media Library no admin
│   └── css/
│       └── portfolio-admin.css        # Estilos personalizados para o admin
├── includes/
│   ├── portfolio-cpt.php              # Registro do Custom Post Type
│   ├── portfolio-metabox.php          # Metabox e campos personalizados (título, imagens, hyperlink)
│   ├── portfolio-pdf.php              # Funções para geração de PDF
│   ├── portfolio-list.php             # Funções para listar, editar e deletar portfólios
│   └── portfolio-scripts.php          # Carregamento de scripts e estilos no admin
├── tcpdf/                             # Biblioteca TCPDF para geração de PDFs
│   └── (arquivos da biblioteca TCPDF)
├── portfolio.php                      # Arquivo principal do plugin
└── readme.txt                         # Documentação básica para WordPress
```

## Requisitos

- WordPress 5.2.2 ou superior.
- PHP 7.0 ou superior.

## Contribuições

Contribuições são bem-vindas! Para contribuir:

1. Faça um fork deste repositório.
2. Crie um branch para sua funcionalidade ou correção de bug: `git checkout -b minha-funcionalidade`.
3. Faça commit de suas alterações: `git commit -m 'Adicionar nova funcionalidade'`.
4. Envie seu branch para o repositório: `git push origin minha-funcionalidade`.
5. Abra um Pull Request.

## Licença

Este projeto é distribuído sob a licença MIT. Consulte o arquivo `LICENSE` para mais detalhes.

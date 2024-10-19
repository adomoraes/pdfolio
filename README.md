# PDFOLIO - WordPress Plugin

## Descrição

O **PDFOLIO** é um plugin para WordPress que permite criar portfolios e gerar PDFs dos mesmos. Ele permite que o usuário crie posts do tipo "portfolio", adicione imagens e informações sobre o projeto, e gere um arquivo PDF contendo essas informações, tudo diretamente a partir da interface administrativa do WordPress.

## Funcionalidades

- Criação de um Custom Post Type chamado "Portfolio".
- Interface de administração para adicionar imagens e conteúdos ao portfolio.
- Geração de PDF a partir de um post de portfolio.
- Integração via API REST para geração de PDF via AJAX.
- Estrutura modular e escalável com classes separadas para cada funcionalidade.

## Requisitos

- WordPress 5.0 ou superior.
- PHP 7.2 ou superior.

## Instalação

### Manualmente

1. Baixe o plugin e extraia o conteúdo.
2. Envie a pasta `pdfolio` para o diretório `wp-content/plugins/` do seu site WordPress.
3. No painel administrativo do WordPress, vá para **Plugins** e ative o **PDFOLIO**.

### Via Interface do WordPress

1. No painel do WordPress, vá até **Plugins > Adicionar Novo**.
2. Envie o arquivo `.zip` do plugin.
3. Instale e ative o plugin.

## Estrutura do Projeto

```bash
pdfolio/
├── assets/
│   ├── css/
│   │   └── pdfolio-admin.css         # Estilos personalizados para o painel admin
│   ├── js/
│   │   └── pdfolio-admin.js          # Scripts para AJAX e interações do painel
│   └── images/                       # Imagens usadas no plugin (opcional)
├── includes/
│   ├── class-pdfolio-post-type.php   # Classe para registro do Custom Post Type "Portfolio"
│   ├── class-pdfolio-pdf-generator.php # Classe para geração de PDFs
│   └── class-pdfolio-rest-api.php    # Classe para registro da API REST
├── templates/
│   └── pdfolio-template.php          # Template usado para a geração de PDFs
├── pdfolio.php                       # Arquivo principal do plugin
└── uninstall.php                     # Script de desinstalação do plugin
```

## Como Usar

1. Após a ativação, um novo tipo de post chamado **Portfolio** estará disponível no painel.
2. Adicione um novo portfolio com título, conteúdo e imagens.
3. No editor de posts, clique no botão **Gerar PDF** para criar um PDF do portfolio.
4. As imagens e o conteúdo serão incluídos automaticamente no PDF.

## Geração de PDF via API REST

O plugin expõe uma rota via API REST para gerar PDFs:

- **Método**: `POST`
- **Rota**: `/wp-json/pdfolio/v1/generate-pdf/{post_id}`
- **Parâmetro**: `post_id` - ID do post do tipo `portfolio`.

### Exemplo de Chamada AJAX:

```javascript
jQuery(document).ready(function ($) {
	$("#pdfolio-generate-pdf").on("click", function (e) {
		e.preventDefault()

		var post_id = $("#post_ID").val()

		$.ajax({
			url: pdfolioAjax.ajax_url + post_id,
			method: "POST",
			beforeSend: function (xhr) {
				xhr.setRequestHeader("X-WP-Nonce", pdfolioAjax.nonce)
			},
			success: function (response) {
				if (response.pdf_url) {
					window.open(response.pdf_url, "_blank")
				} else {
					alert("Erro ao gerar o PDF.")
				}
			},
			error: function (xhr, status, error) {
				alert("Falha na requisição: " + error)
			},
		})
	})
})
```

## Contribuição

1. Fork o projeto.
2. Crie uma branch com a nova funcionalidade (`git checkout -b feature/nova-funcionalidade`).
3. Commit suas alterações (`git commit -am 'Adiciona nova funcionalidade'`).
4. Faça o push da branch (`git push origin feature/nova-funcionalidade`).
5. Abra um Pull Request.

## Licença

Este projeto está licenciado sob a licença MIT. Consulte o arquivo `LICENSE` para mais detalhes.

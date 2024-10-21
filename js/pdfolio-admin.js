jQuery(document).ready(function ($) {
	var galleryContainer = $("#pdfolio-gallery-container")

	// Função para adicionar imagens
	$("#pdfolio-add-image").on("click", function (e) {
		e.preventDefault()

		// Abrir a biblioteca de mídia
		var mediaUploader = wp
			.media({
				title: "Adicionar Imagens",
				button: {
					text: "Adicionar Imagem",
				},
				multiple: true, // Permitir seleção múltipla
			})
			.on("select", function () {
				var attachments = mediaUploader.state().get("selection").toJSON()
				$.each(attachments, function (index, attachment) {
					// Adicionar imagem ao container
					galleryContainer.append(
						'<div class="pdfolio-gallery-image" style="display:inline-block; margin-right:10px; position:relative;">' +
							'<img src="' +
							attachment.url +
							'" style="max-width:100px; max-height:100px;">' +
							'<input type="hidden" name="pdfolio_gallery_images[]" value="' +
							attachment.id +
							'">' +
							'<button class="remove-image-button" style="position:absolute; top:0; right:0;">&times;</button>' +
							"</div>"
					)
				})
			})
			.open()
	})

	// Função para remover imagens
	$(document).on("click", ".remove-image-button", function () {
		$(this).parent().remove()
	})
})

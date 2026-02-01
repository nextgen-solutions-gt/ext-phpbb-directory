/**
* @package phpBB Directory
* @copyright (c) 2025 nextgen
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

(function ($) {  // Evitar conflictos con otras librerías

    // Cambio de banderas
    $('#dir_flag').change(function() {
        var src_image = dir_flag_path + encodeURI($(this).val());
        $('#flag_image').attr('src', src_image);
    });

    // Callback para votos
    phpbb.addAjaxCallback('phpbbdirectory.add_vote', function(data) {
        var link_id = data.LINK_ID;
        if(link_id) {
            $('#dir_note' + link_id).html(data.NOTE);
            $('#dir_vote' + link_id).html(data.NB_VOTE);
            $(this).text('');
        }
        phpbb.closeDarkenWrapper(3000);
    });

    // Callback para borrar sitio
    phpbb.addAjaxCallback('phpbbdirectory.delete_site', function(data) {
        var link_id = data.LINK_ID;
        if(link_id) {
            $('#l' + link_id).remove();
            $('.dir_total_links').html(data.TOTAL_LINKS);
        }
        phpbb.closeDarkenWrapper(3000);
    });

    // Callback para borrar comentario (desde el botón nativo de phpBB)
    phpbb.addAjaxCallback('phpbbdirectory.delete_comment', function(data) {
        var comment_id = data.COMMENT_ID;
        if(comment_id) {
            $('#p' + comment_id).remove();
            $('.dir_total_comments').html(data.TOTAL_COMMENTS);
        }
        phpbb.closeDarkenWrapper(3000);
    });

})(jQuery); 

(function () {

    // Cargar / Mostrar comentarios (Toggle)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-dir-comments');
        if (!btn) return;

        e.preventDefault();

        const linkId = btn.dataset.linkId;
        const container = document.getElementById('dir-comments-' + linkId);
        if (!container) return;

        if (container.dataset.loaded === '1') {
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
            return;
        }

        // Indicador de carga compatible con phpBB 3.3
        if (typeof phpbb.loading_indicator !== 'undefined') phpbb.loading_indicator.show();

        fetch(btn.href + (btn.href.includes('?') ? '&ajax=1' : '?ajax=1'), {
            credentials: 'same-origin'
        })
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
            container.dataset.loaded = '1';
            container.style.display = 'block';
        })
        .finally(() => {
            if (typeof phpbb.loading_indicator !== 'undefined') phpbb.loading_indicator.hide();
        });
    });

    // Envío de formulario de comentarios vía AJAX
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.js-dir-comment-form');
        if (!form) return;

        e.preventDefault();

        const linkId = form.dataset.linkId;
        const container = document.getElementById('dir-comments-' + linkId);
        if (!container) return;

        if (typeof phpbb.loading_indicator !== 'undefined') phpbb.loading_indicator.show();

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Actualizar el token de formulario para permitir envíos consecutivos
            if (data.new_token) {
                const tokenInput = form.querySelector('input[name="form_token"]');
                if (tokenInput) tokenInput.value = data.new_token;
            }

            const list = container.querySelector('.js-dir-comments-list');
            if (list) {
                list.innerHTML = data.html;
            }

            // Actualizar contadores en el DOM
            container.querySelectorAll('.dir_total_comments')
                .forEach(el => {
                    el.textContent = data.total + ' comments';
                });

            const linkCounter = document.querySelector(
                '.js-dir-comments[data-link-id="' + linkId + '"] strong'
            );

            if (linkCounter) {
                linkCounter.textContent = data.total + ' comments';
            }

            form.reset();
        })
        .finally(() => {
            if (typeof phpbb.loading_indicator !== 'undefined') phpbb.loading_indicator.hide();
        });
    });

    // Paginación de comentarios vía AJAX
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination a');
        if (!link) return;

        const container = link.closest('.dir-comments-container');
        if (!container) return;

        e.preventDefault();

        const ajaxBox = container.querySelector('.js-dir-comments-ajax');
        if (!ajaxBox) return;

        if (typeof phpbb.loading_indicator !== 'undefined') phpbb.loading_indicator.show();

        const url = link.href + (link.href.includes('?') ? '&ajax=1' : '?ajax=1');

        fetch(url, { credentials: 'include' })
            .then(r => r.text())
            .then(html => {
                const tmp = document.createElement('div');
                tmp.innerHTML = html;

                const newAjaxBox = tmp.querySelector('.js-dir-comments-ajax');
                if (!newAjaxBox) return;

                ajaxBox.replaceWith(newAjaxBox);
            })
            .finally(() => {
                if (typeof phpbb.loading_indicator !== 'undefined') phpbb.loading_indicator.hide();
            });
    });

})();
/**
 * Bloque "Repositorio documental" para el editor de WordPress.
 * Solo permite elegir la sección; el contenido se genera desde los Documentos.
 */
(function (wp) {
  var el = wp.element.createElement;
  var useSelect = wp.data.useSelect;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var PanelBody = wp.components.PanelBody;
  var SelectControl = wp.components.SelectControl;
  var Disabled = wp.components.Disabled;
  var Notice = wp.components.Notice;
  var ServerSideRender = wp.serverSideRender;

  wp.blocks.registerBlockType('uleam/repositorio-documental', {
    edit: function (props) {
      var seccion = props.attributes.seccion;
      var terms = useSelect(function (select) {
        return select('core').getEntityRecords('taxonomy', 'seccion', { per_page: 100, hide_empty: false });
      }, []);

      // Opciones con sangría según la jerarquía (Área › Subsección).
      var options = [{ label: '— Elige una sección —', value: 0 }];
      if (terms) {
        var byParent = {};
        terms.forEach(function (t) { (byParent[t.parent] = byParent[t.parent] || []).push(t); });
        (function add(parent, depth) {
          (byParent[parent] || []).forEach(function (t) {
            options.push({ label: '— '.repeat(depth) + t.name, value: t.id });
            add(t.id, depth + 1);
          });
        })(0, 0);
      }

      return el('div', useBlockProps(),
        el(InspectorControls, null,
          el(PanelBody, { title: 'Repositorio documental' },
            el(SelectControl, {
              label: 'Sección a mostrar',
              help: 'Sus subsecciones aparecerán como pestañas.',
              value: seccion,
              options: options,
              onChange: function (v) { props.setAttributes({ seccion: parseInt(v, 10) || 0 }); }
            }),
            el('p', null,
              el('a', { href: 'edit.php?post_type=documento', target: '_blank' }, 'Añadir o editar documentos ↗')
            )
          )
        ),
        el(Notice, { status: 'info', isDismissible: false },
          'Este listado se genera solo. Para agregar o cambiar archivos ve a ',
          el('a', { href: 'edit.php?post_type=documento', target: '_blank' }, 'Documentos'),
          '.'
        ),
        el(Disabled, null,
          el(ServerSideRender, { block: 'uleam/repositorio-documental', attributes: props.attributes })
        )
      );
    },
    save: function () { return null; }
  });
})(window.wp);

(function($) {
  // Plugin default options
  const defaultOptions = {};

  // If the plugin is a button
  function buildButtonDef(trumbowyg) {
    return {
      fn() {
        // Plugin button logic
        trumbowyg.openModalInsert(
          trumbowyg.lang.addID,
          {
            id: {
              label: 'ID (must be unique)',
              required: true,
            },
          },
          function(v) {
            // Get the highlighted text
            const text = trumbowyg.getRangeText();
            console.log(trumbowyg);
            trumbowyg.execCmd(
              'insertHtml',
              `<span id="${v.id}"></span>${text}`
            );
            // Close the modal
            return true;
          }
        );
      },
      hasIcon: false,
      text: '#',
      title: trumbowyg.lang.addID,
    };
  }

  $.extend(true, $.trumbowyg, {
    // Add some translations
    langs: {
      en: {
        addID: 'Add ID',
      },
    },
    // Register plugin in Trumbowyg
    plugins: {
      addID: {
        // Code called by Trumbowyg core to register the plugin
        init(trumbowyg) {
          // Fill current Trumbowyg instance with the plugin default options
          trumbowyg.o.plugins.addID = $.extend(
            true,
            {},
            defaultOptions,
            trumbowyg.o.plugins.addID || {}
          );

          // If the plugin is a button
          trumbowyg.addBtnDef('addID', buildButtonDef(trumbowyg));
        },
        // Return a list of button names which are active on current element
        tagHandler(element, trumbowyg) {
          return [];
        },
        destroy(trumbowyg) {},
      },
    },
  });
})(jQuery);

require('materialize-css');

class Import {
    constructor() {
        this.form = $('.import-config');
        this.initImportMappingSelectChangeListener();
        this.initFieldSelectChangeListener();
    }

    initImportMappingSelectChangeListener() {
        $('.import-mapping').off('change');
        $('.import-mapping').on('change', event => {
            let config = JSON.parse($(event.currentTarget).val());

            if (config) {
                // Empty current config
                this.emptyFields();

                // Update fields
                for (let i=0; i<config.length; i++) {
                    $($(`select[name="fields[]"] option[value="${config[i].field}"]`, this.form)[i]).prop('selected', true);
                    $($('input[name="defaults[]"]', this.form)[i]).val(config[i].default);
                }

                // Update selection
                $('select[name="fields[]"]').formSelect();
            }
        });
    }

    initFieldSelectChangeListener() {
        $('select[name="fields[]"]').off('change');
        $('select[name="fields[]"]').on('change', event => {
            let tr = $(event.currentTarget).parents('tr:first');
            let field = $(event.currentTarget).val();

            // Empty cells
            $('td.default', tr).html('');
            $('td.config', tr).html('');

            // Display loade
            $('.progress', tr).show();

            let url = $('meta[name="field-config-url"]').attr('content').replace('FIELD', field);
            $.get(url).then(response => {
                let contentEl = $(response)
                let configEl = $('.config', contentEl).html();
                let defaultEl = $('.default', contentEl).html()

                // Update html content
                $('td.config', tr).html(configEl);
                $('td.default', tr).html(defaultEl);

                // Reload JS libraries
                this.reloadJsLibrairies();

                // Hide loader
                $('.progress', tr).hide();

                // Show field config column if there are custom content
                if ($('td.config :not(input[type="hidden"])', this.form).length > 0) {
                    $('th.config, td.config', this.form).show();
                } else {
                    $('th.config, td.config', this.form).hide();
                }
            });
        });
    }

    reloadJsLibrairies() {
        // Reload materialize
        let event = new CustomEvent('js.init.materialize');
        dispatchEvent(event);

        // Reload librairies used for fields
        event = new CustomEvent('js.init.field.libraries');
        dispatchEvent(event);
    }

    emptyFields() {
        $('input[name="defaults[]"], select[name="fields[]"]', this.form).val('');
    }
}

new Import();

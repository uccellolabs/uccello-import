require('materialize-css');

class Import {
    constructor() {
        this.form = $('.import-config');
        this.initImportMappingSelectChangeListener();
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
                    console.log(config[i])
                    $($(`select[name="fields[]"] option[value="${config[i].field}"]`, this.form)[i]).prop('selected', true);
                    $($('input[name="defaults[]"]', this.form)[i]).val(config[i].default);
                }

                // Update selection
                $('select[name="fields[]"]').formSelect();
            }
        });
    }

    emptyFields() {
        $('input[name="defaults[]"], select[name="fields[]"]', this.form).val('');
    }
}

new Import();

$(document).ready(function () {
    console.log("Loaded Data Anywhere config")
    var $modal = $('#external-modules-configure-modal');
    $modal.on('show.bs.modal', function () {
        // Making sure we are overriding this modules's modal only.
        if ($(this).data('module') !== "DataAnywhere") return;

        if (typeof ExternalModules.Settings.prototype.resetConfigInstancesOld === 'undefined')
            ExternalModules.Settings.prototype.resetConfigInstancesOld = ExternalModules.Settings.prototype.resetConfigInstances;

        ExternalModules.Settings.prototype.resetConfigInstances = function () {
            ExternalModules.Settings.prototype.resetConfigInstancesOld();
            if ($modal.data('module') !== "DataAnywhere") return;

            // Cleanup layout and do some branching logic
            $modal.find('thead').remove();
            $modal.find('input[name^=source-all___]').on('click', function () {
                $(this).closest('tr').nextUntil('tr[field=destination-all]').show();
                if ($(this).val() == "1")
                    $(this).closest('tr').nextUntil('tr[field=destination-all]').hide();
            });
            $modal.find('input[name^=destination-all___]').on('click', function () {
                $(this).closest('tr').nextUntil('.sub_end').show();
                if ($(this).val() == "1")
                    $(this).closest('tr').nextUntil('.sub_end').hide();
            });
            $modal.find('input:checked').click();

        };
    });

    $modal.on('hide.bs.modal', function () {
        // Making sure we are overriding this modules's modal only.
        if ($(this).data('module') !== "DataAnywhere") return;
        if (typeof ExternalModules.Settings.prototype.resetConfigInstancesOld !== 'undefined')
            ExternalModules.Settings.prototype.resetConfigInstances = ExternalModules.Settings.prototype.resetConfigInstancesOld;
    });
});
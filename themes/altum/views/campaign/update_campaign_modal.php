<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="update_campaign" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('update_campaign_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="update_campaign" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="update" />
                    <input type="hidden" name="campaign_id" value="" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('campaigns.input.name') ?></label>
                        <input type="text" class="form-control" name="name"  />
                    </div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-sm fa-network-wired text-muted mr-1"></i> <?= l('campaigns.input.domain') ?></label>
                        <input type="text" class="form-control" name="domain" placeholder="<?= l('campaigns.input.domain_placeholder') ?>" required="required" />
                        <small class="form-text text-muted"><?= l('campaigns.input.domain_help') ?></small>
                        <small class="form-text text-muted"><?= l('campaigns.input.domain_help2') ?></small>
                    </div>

                    <div class="custom-control custom-switch">
                        <input
                                type="checkbox"
                                class="custom-control-input"
                                name="include_subdomains"
                                id="campaign_update_include_subdomains"
                        >
                        <label class="custom-control-label clickable" for="campaign_update_include_subdomains"><?= l('campaigns.input.include_subdomains') ?></label>
                        <small class="form-text text-muted"><?= l('campaigns.input.include_subdomains_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#update_campaign').on('show.bs.modal', event => {
        let campaign_id = $(event.relatedTarget).data('campaign-id');
        let name = $(event.relatedTarget).data('name');
        let domain = $(event.relatedTarget).data('domain');
        let include_subdomains = $(event.relatedTarget).data('include-subdomains');

        $(event.currentTarget).find('input[name="campaign_id"]').val(campaign_id);
        $(event.currentTarget).find('input[name="name"]').val(name);
        $(event.currentTarget).find('input[name="domain"]').val(domain).trigger('change');
        $(event.currentTarget).find('input[name="include_subdomains"]').prop('checked', include_subdomains);
    });

    $('form[name="update_campaign"]').on('submit', event => {
        let notification_container = event.currentTarget.querySelector('.notification-container');
        notification_container.innerHTML = '';
        pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

        $.ajax({
            type: 'POST',
            url: `${url}campaigns-ajax`,
            data: $(event.currentTarget).serialize(),
            dataType: 'json',
            success: (data) => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                if (data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Hide modal */
                    $('#update_campaign').modal('hide');

                    /* Clear input values */
                    $('form[name="update_campaign"] input').val('');

                    /* Redirect */
                    redirect(`dashboard`);

                }
            },
            error: () => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));
                display_notifications(<?= json_encode(l('global.error_message.basic')) ?>, 'error', notification_container);
            },
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="custom_branding_campaign" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('custom_branding_campaign_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="custom_branding_campaign" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="custom_branding" />
                    <input type="hidden" name="campaign_id" value="" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="custom_branding_name"><i class="fa fa-fw fa-sm fa-random text-muted mr-1"></i> <?= l('custom_branding_campaign_modal.input.name') ?></label>
                        <input id="custom_branding_name" type="text" class="form-control" name="name" value="<?= $data->campaign->branding->name ?? '' ?>" maxlength="128" />
                        <small class="form-text text-muted"><?= l('custom_branding_campaign_modal.input.name_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="custom_branding_url"><i class="fa fa-fw fa-sm fa-link text-muted mr-1"></i> <?= l('custom_branding_campaign_modal.input.url') ?></label>
                        <input id="custom_branding_url" type="url" class="form-control" name="url" value="<?= $data->campaign->branding->url ?? '' ?>" maxlength="2048" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.update') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#custom_branding_campaign').on('show.bs.modal', event => {
        let campaign_id = $(event.relatedTarget).data('campaign-id');
        let branding_name = $(event.relatedTarget).data('branding-name');
        let branding_url = $(event.relatedTarget).data('branding-url');

        $(event.currentTarget).find('input[name="campaign_id"]').val(campaign_id);
        $(event.currentTarget).find('input[name="name"]').val(branding_name);
        $(event.currentTarget).find('input[name="url"]').val(branding_url);
    });

    $('form[name="custom_branding_campaign"]').on('submit', event => {
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

                if(data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {
                    display_notifications(data.message, 'success', notification_container);

                    setTimeout(() => {
                        /* Remove the notification */
                        notification_container.innerHTML = '';
                    }, 3000);
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

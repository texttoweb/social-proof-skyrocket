<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div role="dialog" class="altumcode-wrapper altumcode-wrapper-<?= $notification->settings->border_radius ?> <?= $notification->settings->shadow ? 'altumcode-wrapper-shadow' : null ?> <?= ($notification->settings->direction ?? 'ltr') == 'rtl' ? 'altumcode-rtl' : null ?> altumcode-email-collector-wrapper" style='font-family: <?= $notification->settings->font ?? 'inherit' ?>!important;background-color: <?= $notification->settings->background_color ?>;border-width: <?= $notification->settings->border_width ?>px;border-color: <?= $notification->settings->border_color ?>;<?= $notification->settings->background_pattern_svg ? 'background-image: url("' . $notification->settings->background_pattern_svg . '")' : null ?>;'>
    <div class="altumcode-email-collector-content">
        <div class="altumcode-email-collector-header">
            <p class="altumcode-email-collector-title" style="color: <?= $notification->settings->title_color ?>"><?= $notification->settings->title ?></p>

            <button class="altumcode-close" style="color: <?= $notification->settings->close_button_color ?>;">×</button>
        </div>
        <p class="altumcode-email-collector-description" style="color: <?= $notification->settings->description_color ?>"><?= $notification->settings->description ?></p>


        <form id="altumcode-email-collector-form" class="altumcode-email-collector-form" name="" action="" method="POST" role="form">
            <div class="altumcode-email-collector-row">
                <input type="text" class="" name="name" placeholder="<?= $notification->settings->name_placeholder ?>" aria-label="<?= $notification->settings->name_placeholder ?>" required="required" />
                <input type="email" class="" name="email" placeholder="<?= $notification->settings->email_placeholder ?>" aria-label="<?= $notification->settings->email_placeholder ?>" required="required" />
            </div>

            <div class="altumcode-email-collector-row">
            </div>

            <button type="submit" name="button" class="altumcode-email-collector-button" style="color: <?= $notification->settings->button_color ?>; background: <?= $notification->settings->button_background_color ?>"><?= $notification->settings->button_text ?></button>

            <?php if($notification->settings->show_agreement): ?>
                <div class="altumcode-agreement-checkbox">
                    <input type="checkbox" id="agreement" class="altumcode-agreement-checkbox-input" name="agreement" required="required" />
                    <label for="agreement" class="altumcode-agreement-checkbox-text" style="color: <?= $notification->settings->description_color ?>">
                        <a href="<?= $notification->settings->agreement_url ?>" target="_blank">
                            <?= $notification->settings->agreement_text ?>
                        </a>
                    </label>
                </div>
            <?php endif ?>
        </form>

        <?php if($notification->settings->display_branding): ?>
            <?php if(isset($notification->branding, $notification->branding->name, $notification->branding->url) && !empty($notification->branding->name) && !empty($notification->branding->url)): ?>
                <a href="<?= $notification->branding->url ?>" class="altumcode-site"><?= $notification->branding->name ?></a>
            <?php else: ?>
                <a href="<?= url() ?>" class="altumcode-site"><?= settings()->notifications->branding ?></a>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
new AltumCodeManager({
    should_show: !localStorage.getItem('notification_<?= $notification->notification_id ?>_converted'),
    content: <?= json_encode($html) ?>,
    display_mobile: <?= json_encode($notification->settings->display_mobile) ?>,
    display_desktop: <?= json_encode($notification->settings->display_desktop) ?>,
    display_trigger: <?= json_encode($notification->settings->display_trigger) ?>,
    display_trigger_value: <?= json_encode($notification->settings->display_trigger_value) ?>,
    duration: <?= $notification->settings->display_duration === -1 ? -1 : $notification->settings->display_duration * 1000 ?>,
    close: <?= json_encode($notification->settings->display_close_button) ?>,
    display_frequency: <?= json_encode($notification->settings->display_frequency) ?>,
    position: <?= json_encode($notification->settings->display_position) ?>,
    trigger_all_pages: <?= json_encode($notification->settings->trigger_all_pages) ?>,
    triggers: <?= json_encode($notification->settings->triggers) ?>,
    on_animation: <?= json_encode($notification->settings->on_animation) ?>,
    off_animation: <?= json_encode($notification->settings->off_animation) ?>,
    animation: <?= json_encode($notification->settings->animation) ?>,
    animation_interval: <?= (int) $notification->settings->animation_interval ?>,

    notification_id: <?= $notification->notification_id ?>
}).initiate({
    displayed: main_element => {

        /* Form submission */
        main_element.querySelector('#altumcode-email-collector-form').addEventListener('submit', event => {

            let name = event.currentTarget.querySelector('[name="name"]').value;
            let email = event.currentTarget.querySelector('[name="email"]').value;
            let notification_id = main_element.getAttribute('data-notification-id');


            if(email.trim() != '') {

                /* Data collection from the form */
                send_tracking_data({
                    notification_id: notification_id,
                    type: 'collector',
                    email,
                    name
                });

                AltumCodeManager.remove_notification(main_element);

                /* Make sure to let the browser know of the conversion so that it is not shown again */
                localStorage.setItem(`notification_${notification_id}_converted`, true);

                /* Redirect the user to thank you url if needed */
                let thank_you_url = <?= json_encode($notification->settings->thank_you_url) ?>;

                if(thank_you_url.trim() != '') {
                    setTimeout(() => {
                        window.location.href = thank_you_url;
                    }, 350);
                }

            }

            event.preventDefault();
        });

    }
});
<?php $javascript = ob_get_clean(); ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
